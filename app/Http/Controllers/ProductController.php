<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource for admin.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'variants'])
            ->where('active', 1); // Only show active products

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by brand
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('active', $request->status == 'active' ? 1 : 0);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            });
        }

        $products = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'products' => $products
            ]);
        }

        return view('admin.products', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::where('active', 1)->get();
        $brands = \App\Models\Brand::where('active', 1)->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'nullable|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.discount' => 'nullable|numeric|min:0|max:100',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            
            // Move to public/images/products/
            $image->move(public_path('images/products'), $imageName);
            $validated['image'] = 'products/' . $imageName;
        }

        // Convert checkbox to boolean
        $validated['active'] = $request->has('active') ? 1 : 0;

        $product = Product::create($validated);

        // Create variants
        if ($request->has('variants')) {
            foreach ($request->variants as $index => $variantData) {
                // Collect all attributes dynamically
                $attributes = [];
                if (isset($variantData['attributes']) && is_array($variantData['attributes'])) {
                    foreach ($variantData['attributes'] as $key => $value) {
                        if (!empty($value)) {
                            $attributes[$key] = $value;
                        }
                    }
                }

                // Handle variant image upload
                $variantImagePath = null;
                if ($request->hasFile("variants.{$index}.image")) {
                    $variantImage = $request->file("variants.{$index}.image");
                    $variantImageName = $variantImage->getClientOriginalName();
                    $variantImage->storeAs('public/products', $variantImageName);
                    $variantImagePath = 'products/' . $variantImageName;
                }

                $product->variants()->create([
                    'sku' => $variantData['sku'] ?? null,
                    'price' => $variantData['price'],
                    'discount' => $variantData['discount'] ?? 0,
                    'stock' => $variantData['stock'] ?? 0,
                    'image' => $variantImagePath,
                    'attributes' => $attributes,
                    'active' => isset($variantData['active']) ? 1 : 0,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm sản phẩm thành công!',
                'product' => $product->load(['category', 'brand', 'variants'])
            ]);
        }

        return redirect()->route('admin.products')
            ->with('success', 'Thêm sản phẩm thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Lấy sản phẩm với category, brand và variants (chỉ active = 1)
        $product = Product::with(['category', 'brand', 'variants' => function($query) {
            $query->where('active', 1);
        }])
        ->where('active', 1)
        ->findOrFail($id);

        // Lấy sản phẩm liên quan (cùng category) kèm variants (chỉ active = 1)
        $relatedProducts = Product::with(['variants' => function($query) {
            $query->where('active', 1);
        }])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('active', 1)
            ->take(8)
            ->get();

        // Lấy tổng số đánh giá và điểm trung bình
        // $reviewsCount = $product->reviews()->count();
        // $averageRating = $product->reviews()->avg('rating');

        // Lấy danh sách đánh giá gần nhất (tùy chọn)
        // $reviews = $product->reviews()->latest()->take(5)->get();

        // Lấy currency từ settings
        $currency = Setting::get('currency', 'VND');

        return view('pages.product-detail', compact(
            'product',
            'relatedProducts',
            'currency'
            // 'reviewsCount',
            // 'averageRating',
            // 'reviews'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with(['variants'])->findOrFail($id);
        $categories = \App\Models\Category::where('active', 1)->get();
        $brands = \App\Models\Brand::where('active', 1)->get();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'product' => $product,
                'categories' => $categories,
                'brands' => $brands
            ]);
        }
        
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.sku' => 'nullable|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.discount' => 'nullable|numeric|min:0|max:100',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && file_exists(public_path('images/' . $product->image))) {
                unlink(public_path('images/' . $product->image));
            }
            
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            
            // Move to public/images/products/
            $image->move(public_path('images/products'), $imageName);
            $validated['image'] = 'products/' . $imageName;
        }

        // Convert checkbox to boolean
        $validated['active'] = $request->has('active') ? 1 : 0;

        $product->update($validated);

        // Update or create variants
        $existingVariantIds = [];
        
        if ($request->has('variants')) {
            foreach ($request->variants as $index => $variantData) {
                // Collect all attributes dynamically
                $attributes = [];
                if (isset($variantData['attributes']) && is_array($variantData['attributes'])) {
                    foreach ($variantData['attributes'] as $key => $value) {
                        if (!empty($value)) {
                            $attributes[$key] = $value;
                        }
                    }
                }

                $variantPayload = [
                    'sku' => $variantData['sku'] ?? null,
                    'price' => $variantData['price'],
                    'discount' => $variantData['discount'] ?? 0,
                    'stock' => $variantData['stock'] ?? 0,
                    'attributes' => $attributes,
                    'active' => isset($variantData['active']) ? 1 : 0,
                ];

                // Handle variant image upload
                if ($request->hasFile("variants.{$index}.image")) {
                    $variantImage = $request->file("variants.{$index}.image");
                    $variantImageName = $variantImage->getClientOriginalName();
                    $variantImage->storeAs('public/products', $variantImageName);
                    $variantPayload['image'] = 'products/' . $variantImageName;
                    
                    // Delete old variant image if updating
                    if (!empty($variantData['id'])) {
                        $oldVariant = $product->variants()->find($variantData['id']);
                        if ($oldVariant && $oldVariant->image && Storage::exists('public/' . $oldVariant->image)) {
                            Storage::delete('public/' . $oldVariant->image);
                        }
                    }
                }

                if (!empty($variantData['id'])) {
                    // Update existing variant
                    $variant = $product->variants()->find($variantData['id']);
                    if ($variant) {
                        $variant->update($variantPayload);
                        $existingVariantIds[] = $variant->id;
                    }
                } else {
                    // Create new variant
                    $variant = $product->variants()->create($variantPayload);
                    $existingVariantIds[] = $variant->id;
                }
            }
        }

        // Delete variants that are not in the request
        $product->variants()->whereNotIn('id', $existingVariantIds)->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật sản phẩm thành công!',
                'product' => $product->load(['category', 'brand', 'variants'])
            ]);
        }

        return redirect()->route('admin.products')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        
        // Soft delete: Set active to 0 instead of deleting
        $product->update(['active' => 0]);
        
        // Also deactivate all variants
        $product->variants()->update(['active' => 0]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa sản phẩm thành công!'
            ]);
        }

        return redirect()->route('admin.products')
            ->with('success', 'Xóa sản phẩm thành công!');
    }

    /**
     * Search the specified resource from storage.
     */
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        // Nếu người dùng chưa nhập gì
        if (!$keyword) {
            return redirect()->back()->with('error', 'Vui lòng nhập từ khóa tìm kiếm!');
        }

        // Tìm trong bảng products (chỉ sản phẩm active = 1)
        $products = \App\Models\Product::with(['variants' => function($query) {
            $query->where('active', 1);
        }])
            ->where('active', 1)
            ->where(function($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('description', 'LIKE', "%{$keyword}%");
            })
            ->paginate(12);

        return view('pages.search', compact('products', 'keyword'));
    }
}
