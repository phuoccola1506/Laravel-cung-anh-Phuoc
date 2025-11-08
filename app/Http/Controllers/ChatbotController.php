<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OpenAI;

class ChatbotController extends Controller
{
    private $openai;

    public function __construct()
    {
        // Khởi tạo OpenAI client
        $apiKey = env('OPENAI_API_KEY');
        
        if ($apiKey && $apiKey !== 'your-openai-api-key-here') {
            $this->openai = OpenAI::client($apiKey);
        }
    }

    /**
     * Tìm kiếm sản phẩm dựa trên query - Sử dụng GPT API với dữ liệu thực từ database
     */
    public function search(Request $request)
    {
        try {
            $userQuery = $request->input('original');
            
            Log::info('Chatbot search query:', ['original' => $userQuery]);

            // Lấy currency từ settings
            $currency = Setting::get('currency', 'VND');

            if (!$this->openai) {
                // Nếu không có OpenAI API, fallback sang logic đơn giản
                $parsedQuery = $this->parseQuerySimple($userQuery);
                $products = $this->searchProductsInDatabase($parsedQuery);
                
                return response()->json([
                    'success' => true,
                    'products' => $products,
                    'query' => $parsedQuery,
                    'method' => 'simple_parse',
                    'currency' => $currency
                ]);
            }

            // Sử dụng GPT để tìm kiếm với đầy đủ context từ database
            $result = $this->searchWithGPT($userQuery);

            return response()->json([
                'success' => true,
                'products' => $result['products'],
                'query' => $result['parsed_query'],
                'method' => 'gpt_search',
                'reasoning' => $result['reasoning'] ?? null,
                'currency' => $currency
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot search error: ' . $e->getMessage());
            
            // Fallback sang simple parse nếu GPT lỗi
            try {
                $currency = Setting::get('currency', 'VND');
                $parsedQuery = $this->parseQuerySimple($request->input('original'));
                $products = $this->searchProductsInDatabase($parsedQuery);
                
                return response()->json([
                    'success' => true,
                    'products' => $products,
                    'query' => $parsedQuery,
                    'method' => 'fallback',
                    'error' => $e->getMessage(),
                    'currency' => $currency
                ]);
            } catch (\Exception $fallbackError) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi tìm kiếm sản phẩm',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }

    /**
     * BỘ NÃO CHÍNH: Tìm kiếm sản phẩm bằng GPT API với dữ liệu thực từ database
     */
    private function searchWithGPT($userQuery)
    {
        // Bước 1: Lấy toàn bộ sản phẩm từ database
        $allProducts = $this->getAllProductsForGPT();
        
        // Bước 2: Tạo context về categories và brands
        $categoriesContext = $this->getCategoriesContext();
        $brandsContext = $this->getBrandsContext();
        
        // Bước 3: Gọi GPT để phân tích và tìm kiếm
        $systemPrompt = $this->buildGPTSystemPrompt($categoriesContext, $brandsContext, $allProducts);
        $userPrompt = $this->buildGPTUserPrompt($userQuery);
        
        Log::info('GPT Search - System prompt length:', ['length' => strlen($systemPrompt)]);
        Log::info('GPT Search - User query:', ['query' => $userQuery]);

        $response = $this->openai->chat()->create([
            'model' => 'gpt-3.5-turbo-16k', // Sử dụng model 16k để xử lý nhiều sản phẩm
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $userPrompt
                ]
            ],
            'temperature' => 0.3,
            'max_tokens' => 2000
        ]);

        $gptResult = $response->choices[0]->message->content;
        Log::info('GPT Search - Raw response:', ['response' => $gptResult]);

        // Bước 4: Parse kết quả từ GPT
        $result = $this->parseGPTSearchResult($gptResult);
        
        return $result;
    }

    /**
     * Lấy tất cả sản phẩm active để cung cấp cho GPT
     */
    private function getAllProductsForGPT()
    {
        return Product::where('active', 1)
            ->with(['category', 'brand'])
            ->orderBy('category_id')
            ->orderBy('price')
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'price_formatted' => number_format($product->price / 1000000, 1) . ' triệu',
                    'category' => $product->category->name ?? 'N/A',
                    'brand' => $product->brand->name ?? 'N/A',
                    'description' => mb_substr($product->description, 0, 100) . '...'
                ];
            })
            ->toArray();
    }

    /**
     * Lấy danh sách categories
     */
    private function getCategoriesContext()
    {
        return DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->where('products.active', 1)
            ->select('categories.id', 'categories.name', DB::raw('COUNT(*) as count'))
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->map(function($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'count' => $cat->count
                ];
            })
            ->toArray();
    }

    /**
     * Lấy danh sách brands
     */
    private function getBrandsContext()
    {
        return DB::table('brands')
            ->join('products', 'brands.id', '=', 'products.brand_id')
            ->where('products.active', 1)
            ->select('brands.id', 'brands.name', DB::raw('COUNT(*) as count'))
            ->groupBy('brands.id', 'brands.name')
            ->get()
            ->map(function($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'count' => $brand->count
                ];
            })
            ->toArray();
    }

    /**
     * Tạo system prompt cho GPT với đầy đủ thông tin sản phẩm
     */
    private function buildGPTSystemPrompt($categories, $brands, $products)
    {
        $categoriesText = "DANH MỤC SẢN PHẨM:\n";
        foreach ($categories as $cat) {
            $categoriesText .= "- {$cat['name']} (ID: {$cat['id']}): {$cat['count']} sản phẩm\n";
        }

        $brandsText = "\nTHƯƠNG HIỆU:\n";
        foreach ($brands as $brand) {
            $brandsText .= "- {$brand['name']} (ID: {$brand['id']}): {$brand['count']} sản phẩm\n";
        }

        $productsText = "\nDANH SÁCH SẢN PHẨM (" . count($products) . " sản phẩm):\n";
        foreach ($products as $index => $product) {
            $productsText .= sprintf(
                "%d. %s - %s - %s - %s\n",
                $index + 1,
                $product['name'],
                $product['price_formatted'],
                $product['category'],
                $product['brand']
            );
        }

        return <<<PROMPT
Bạn là trợ lý tìm kiếm sản phẩm thông minh của TechShop.

{$categoriesText}
{$brandsText}
{$productsText}

NHIỆM VỤ:
1. Phân tích yêu cầu của khách hàng
2. Tìm các sản phẩm phù hợp nhất từ danh sách trên
3. Trả về kết quả theo định dạng JSON CHÍNH XÁC

ĐỊNH DẠNG KẾT QUẢ (bắt buộc):
{
  "parsed_query": {
    "category": "tên danh mục hoặc null",
    "brand": "tên thương hiệu hoặc null",
    "price_min": số tiền (VND) hoặc null,
    "price_max": số tiền (VND) hoặc null,
    "keywords": ["từ khóa"]
  },
  "product_ids": [1, 3, 5],
  "reasoning": "Giải thích ngắn gọn tại sao chọn những sản phẩm này"
}

QUY TẮC:
- product_ids: Mảng các số ID sản phẩm phù hợp (tối đa 10 sản phẩm)
- Ưu tiên sản phẩm giá thấp hơn khi có nhiều lựa chọn
- Nếu không tìm thấy sản phẩm nào, trả về product_ids: []
- Chỉ trả về JSON, không thêm text hoặc markdown
- Giá tính bằng VND (1 triệu = 1000000)
PROMPT;
    }

    /**
     * Tạo user prompt
     */
    private function buildGPTUserPrompt($userQuery)
    {
        return "Khách hàng hỏi: \"{$userQuery}\"\n\nHãy tìm kiếm và trả về JSON kết quả.";
    }

    /**
     * Parse kết quả JSON từ GPT
     */
    private function parseGPTSearchResult($gptResult)
    {
        // Loại bỏ markdown nếu có
        $gptResult = preg_replace('/```json\s*/i', '', $gptResult);
        $gptResult = preg_replace('/```\s*$/i', '', $gptResult);
        $gptResult = trim($gptResult);

        $data = json_decode($gptResult, true);

        if (!$data || !isset($data['product_ids'])) {
            Log::warning('GPT returned invalid JSON:', ['result' => $gptResult]);
            throw new \Exception('GPT returned invalid response format');
        }

        // Lấy currency từ settings
        $currency = Setting::get('currency', 'VND');

        // Lấy sản phẩm theo IDs từ GPT
        $productIds = array_slice($data['product_ids'], 0, 10); // Giới hạn 10 sản phẩm
        
        $products = Product::whereIn('id', $productIds)
            ->where('active', 1)
            ->with(['category', 'brand'])
            ->get()
            ->map(function($product) use ($currency) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => number_format($product->price, 0, ',', '.') . ' ' . $currency,
                    'price_raw' => $product->price,
                    'image' => $product->image ? asset('images/' . $product->image) : asset('images/no-image.png'),
                    'description' => $product->description,
                    'category' => $product->category->name ?? 'N/A',
                    'brand' => $product->brand->name ?? 'N/A',
                    'url' => route('product.show', $product->id)
                ];
            });

        // Sắp xếp theo thứ tự IDs từ GPT (GPT đã sort theo mức độ phù hợp)
        $sortedProducts = collect($productIds)->map(function($id) use ($products) {
            return $products->firstWhere('id', $id);
        })->filter()->values();

        return [
            'products' => $sortedProducts,
            'parsed_query' => $data['parsed_query'] ?? [],
            'reasoning' => $data['reasoning'] ?? null
        ];
    }

    /**
     * Parse query đơn giản khi không có GPT (FALLBACK)
     */
    private function parseQuerySimple($original)
    {
        $query = [
            'original' => $original,
            'category' => null,
            'brand' => null,
            'price_min' => null,
            'price_max' => null,
            'keywords' => []
        ];

        $text = mb_strtolower($original, 'UTF-8');

        // Mapping categories (không dấu và có dấu)
        $categoryMap = [
            'điện thoại' => ['dien thoai', 'điện thoại', 'phone', 'smartphone', 'đt'],
            'laptop' => ['laptop', 'máy tính xách tay', 'may tinh xach tay'],
            'tablet' => ['tablet', 'máy tính bảng', 'may tinh bang'],
            'tai nghe' => ['tai nghe', 'headphone', 'earphone'],
            'chuột' => ['chuot', 'chuột', 'mouse'],
            'bàn phím' => ['ban phim', 'bàn phím', 'keyboard'],
            'phụ kiện' => ['phu kien', 'phụ kiện', 'accessory']
        ];

        // Tìm category
        foreach ($categoryMap as $category => $patterns) {
            foreach ($patterns as $pattern) {
                if (stripos($text, $pattern) !== false) {
                    $query['category'] = $category;
                    $query['keywords'][] = $category;
                    break 2;
                }
            }
        }

        // Mapping brands
        $brands = ['Apple', 'Samsung', 'Xiaomi', 'Oppo', 'Vivo', 'Realme', 
                   'Dell', 'HP', 'Asus', 'Lenovo', 'Acer', 'MSI',
                   'Sony', 'JBL', 'Logitech', 'Razer', 'iPhone'];

        foreach ($brands as $brand) {
            if (stripos($text, mb_strtolower($brand, 'UTF-8')) !== false) {
                $query['brand'] = $brand;
                $query['keywords'][] = mb_strtolower($brand, 'UTF-8');
                break;
            }
        }

        // Parse price (triệu, nghìn, tr, k) - WORD BOUNDARY
        
        // Dưới X triệu / triệu xuống
        if (preg_match('/(dưới|duoi|tới|toi|đến|den)\s*(\d+)\s*(triệu|triẹu|tr|trieu)\b/ui', $text, $matches)) {
            $query['price_max'] = intval($matches[2]) * 1000000;
        }
        
        // Trên X triệu / triệu trở lên
        if (preg_match('/(trên|tren|trở lên|tro len|từ|tu)\s*(\d+)\s*(triệu|triẹu|tr|trieu)\b\s*(trở lên|tro len|trở xuống|tro xuong)?/ui', $text, $matches)) {
            if (isset($matches[4]) && stripos($matches[4], 'xuống') !== false) {
                $query['price_max'] = intval($matches[2]) * 1000000;
            } else {
                $query['price_min'] = intval($matches[2]) * 1000000;
            }
        }
        
        // Từ X đến Y triệu
        if (preg_match('/(từ|tu)\s*(\d+)\s*(đến|den|tới|toi|-)\s*(\d+)\s*(triệu|triẹu|tr|trieu)\b/ui', $text, $matches)) {
            $query['price_min'] = intval($matches[2]) * 1000000;
            $query['price_max'] = intval($matches[4]) * 1000000;
        }
        
        // Giá X triệu (chính xác)
        if (preg_match('/giá\s*(\d+)\s*(triệu|triẹu|tr|trieu)\b/ui', $text, $matches) && empty($query['price_min']) && empty($query['price_max'])) {
            $price = intval($matches[1]) * 1000000;
            $query['price_min'] = $price * 0.8; // -20%
            $query['price_max'] = $price * 1.2; // +20%
        }

        Log::info('Simple parse result:', $query);

        return $query;
    }

    /**
     * Tìm kiếm sản phẩm trong database MySQL
     */
    private function searchProductsInDatabase($query)
    {
        $builder = Product::query();

        // Filter by active status
        $builder->where('active', 1);

        Log::info('Search query received:', $query);

        // Category filter
        if (!empty($query['category'])) {
            $categoryId = DB::table('categories')
                ->where('name', 'LIKE', '%' . $query['category'] . '%')
                ->value('id');
            
            Log::info('Category search:', ['category' => $query['category'], 'found_id' => $categoryId]);
            
            if ($categoryId) {
                $builder->where('category_id', $categoryId);
            }
        }

        // Brand filter
        if (!empty($query['brand'])) {
            $brandId = DB::table('brands')
                ->where('name', 'LIKE', '%' . $query['brand'] . '%')
                ->value('id');
            
            Log::info('Brand search:', ['brand' => $query['brand'], 'found_id' => $brandId]);
            
            if ($brandId) {
                $builder->where('brand_id', $brandId);
            } else {
                // Nếu không tìm thấy brand trong bảng brands, tìm trong tên sản phẩm
                $builder->where('name', 'LIKE', '%' . $query['brand'] . '%');
                Log::info('Brand not found in table, searching in product names:', ['brand' => $query['brand']]);
            }
        }

        // Price filters
        if (!empty($query['price_min'])) {
            $builder->where('price', '>=', $query['price_min']);
        }

        if (!empty($query['price_max'])) {
            $builder->where('price', '<=', $query['price_max']);
        }

        // Keywords search - CHỈ tìm khi KHÔNG có category và brand
        if (empty($query['category']) && empty($query['brand'])) {
            if (!empty($query['keywords']) && is_array($query['keywords'])) {
                $builder->where(function($q) use ($query) {
                    foreach ($query['keywords'] as $keyword) {
                        $q->orWhere('name', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('description', 'LIKE', '%' . $keyword . '%');
                    }
                });
            } elseif (!empty($query['original'])) {
                $builder->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', '%' . $query['original'] . '%')
                      ->orWhere('description', 'LIKE', '%' . $query['original'] . '%');
                });
            }
        }

        // Log SQL query
        $sql = $builder->toSql();
        $bindings = $builder->getBindings();
        Log::info('SQL Query:', ['sql' => $sql, 'bindings' => $bindings]);

        // Smart sorting based on query intent
        $sortBy = 'price';
        $sortOrder = 'asc';
        
        if (!empty($query['original'])) {
            $text = mb_strtolower($query['original'], 'UTF-8');
            
            if (preg_match('/(mới nhất|moi nhat|ra mắt|ra mat|gần đây|gan day|latest|newest)/ui', $text)) {
                $sortBy = 'created_at';
                $sortOrder = 'desc';
            } elseif (preg_match('/(cũ nhất|cu nhat|xưa nhất|xua nhat|oldest)/ui', $text)) {
                $sortBy = 'created_at';
                $sortOrder = 'asc';
            } elseif (preg_match('/(đắt nhất|dat nhat|giá cao|gia cao|expensive)/ui', $text)) {
                $sortBy = 'price';
                $sortOrder = 'desc';
            } elseif (preg_match('/(rẻ nhất|re nhat|giá thấp|gia thap|cheap)/ui', $text)) {
                $sortBy = 'price';
                $sortOrder = 'asc';
            } elseif (preg_match('/(phổ biến|pho bien|bán chạy|ban chay|popular|trending)/ui', $text)) {
                $sortBy = 'views';
                $sortOrder = 'desc';
            }
        }
        
        $builder->orderBy($sortBy, $sortOrder);

        // Eager load relationships
        $builder->with(['category', 'brand']);

        // Limit results
        $products = $builder->limit(10)->get();
        
        Log::info('Products found:', ['count' => $products->count()]);

        // Lấy currency từ settings
        $currency = Setting::get('currency', 'VND');

        // Format response
        return $products->map(function($product) use ($currency) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format($product->price, 0, ',', '.') . ' ' . $currency,
                'price_raw' => $product->price,
                'image' => $product->image ? asset('images/' . $product->image) : asset('images/no-image.png'),
                'description' => $product->description,
                'category' => $product->category->name ?? 'N/A',
                'brand' => $product->brand->name ?? 'N/A',
                'url' => route('product.show', $product->id)
            ];
        });
    }

    /**
     * Chat với AI (sử dụng ChatGPT để trả lời câu hỏi tổng quát)
     */
    public function chat(Request $request)
    {
        try {
            if (!$this->openai) {
                return response()->json([
                    'success' => false,
                    'message' => 'OpenAI API key chưa được cấu hình'
                ], 503);
            }

            $userMessage = $request->input('message');
            
            // Lấy context về sản phẩm từ database
            $productsContext = $this->getProductsContext();

            $response = $this->openai->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Bạn là trợ lý bán hàng chuyên nghiệp của TechShop, cửa hàng điện tử uy tín. Nhiệm vụ của bạn là tư vấn sản phẩm cho khách hàng một cách nhiệt tình và chuyên nghiệp.\n\nThông tin sản phẩm hiện có:\n{$productsContext}"
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500
            ]);

            $aiResponse = $response->choices[0]->message->content;

            return response()->json([
                'success' => true,
                'message' => $aiResponse
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot chat error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Xin lỗi, tôi đang gặp sự cố. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Lấy context về sản phẩm cho GPT
     */
    private function getProductsContext()
    {
        $categories = DB::table('products')
            ->select('category_id', DB::raw('COUNT(*) as count'))
            ->where('active', 1)
            ->groupBy('category_id')
            ->get();

        $context = "Danh mục sản phẩm:\n";
        
        foreach ($categories as $cat) {
            $categoryName = DB::table('categories')
                ->where('id', $cat->category_id)
                ->value('name');
            
            $context .= "- {$categoryName}: {$cat->count} sản phẩm\n";
        }

        $brands = DB::table('brands')
            ->join('products', 'brands.id', '=', 'products.brand_id')
            ->where('products.active', 1)
            ->select('brands.name', DB::raw('COUNT(*) as count'))
            ->groupBy('brands.id', 'brands.name')
            ->get();

        $context .= "\nThương hiệu:\n";
        foreach ($brands as $brand) {
            $context .= "- {$brand->name}: {$brand->count} sản phẩm\n";
        }

        return $context;
    }
    
    /**
     * Lấy gợi ý tìm kiếm nhanh
     */
    public function suggestions()
    {
        $suggestions = [
            [
                'text' => 'Điện thoại giá rẻ dưới 5 triệu',
                'icon' => '📱',
                'query' => 'Tìm điện thoại giá dưới 5 triệu'
            ],
            [
                'text' => 'Laptop Dell cho văn phòng',
                'icon' => '💻',
                'query' => 'Tìm laptop Dell giá từ 10 đến 20 triệu'
            ],
            [
                'text' => 'Tai nghe gaming cao cấp',
                'icon' => '🎧',
                'query' => 'Tìm tai nghe gaming giá từ 3 triệu'
            ],
            [
                'text' => 'Chuột không dây Logitech',
                'icon' => '🖱️',
                'query' => 'Tìm chuột Logitech không dây'
            ],
        ];
        
        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }
}
