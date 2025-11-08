<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ChatbotController extends Controller
{
    /**
     * Tìm kiếm sản phẩm thông qua chatbot
     */
    public function search(Request $request)
    {
        // Parse query từ chatbot
        $category = $request->input('category');
        $brand = $request->input('brand');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $keywords = $request->input('keywords', []);
        
        // Query builder
        $query = Product::query()
            ->select([
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'products.image',
                'products.views',
                'categories.name as category_name',
                'brands.name as brand_name'
            ])
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->where('products.active', 1);
        
        // Lọc theo danh mục
        if ($category) {
            $query->where('categories.name', 'LIKE', '%' . $category . '%');
        }
        
        // Lọc theo thương hiệu
        if ($brand) {
            $query->where('brands.name', 'LIKE', '%' . $brand . '%');
        }
        
        // Lọc theo giá tối thiểu
        if ($priceMin) {
            $query->where('products.price', '>=', $priceMin);
        }
        
        // Lọc theo giá tối đa
        if ($priceMax) {
            $query->where('products.price', '<=', $priceMax);
        }
        
        // Tìm kiếm theo keywords
        if (!empty($keywords)) {
            $query->where(function($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('products.name', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('products.description', 'LIKE', '%' . $keyword . '%');
                }
            });
        }
        
        // Sắp xếp theo giá hoặc độ phổ biến
        if ($priceMax && !$priceMin) {
            // Nếu chỉ có giá tối đa -> sắp xếp giá tăng dần
            $query->orderBy('products.price', 'asc');
        } elseif ($priceMin && !$priceMax) {
            // Nếu chỉ có giá tối thiểu -> sắp xếp giá giảm dần
            $query->orderBy('products.price', 'desc');
        } else {
            // Mặc định sắp xếp theo views (phổ biến)
            $query->orderBy('products.views', 'desc');
        }
        
        // Giới hạn kết quả
        $products = $query->limit(6)->get();
        
        // Format data
        $formattedProducts = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description ? substr($product->description, 0, 100) . '...' : '',
                'price' => (int) $product->price,
                'image' => $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png'),
                'category' => $product->category_name,
                'brand' => $product->brand_name,
                'url' => route('product.show', $product->id)
            ];
        });
        
        return response()->json([
            'success' => true,
            'count' => $formattedProducts->count(),
            'products' => $formattedProducts,
            'query' => [
                'category' => $category,
                'brand' => $brand,
                'price_min' => $priceMin,
                'price_max' => $priceMax,
            ]
        ]);
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
