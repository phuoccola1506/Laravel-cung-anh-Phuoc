<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Hiển thị danh sách danh mục (Admin)
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.categories', compact('categories'));
    }

    /**
     * Hiển thị form tạo mới
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Lưu danh mục mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active') ? 1 : 0;

        Category::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm danh mục thành công!'
            ]);
        }

        return redirect()->route('admin.categories')
            ->with('success', 'Thêm danh mục thành công!');
    }

    /**
     * Hiển thị 1 danh mục cụ thể (Public)
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);

        $brands = Brand::withCount(['products' => function ($query) use ($id) {
            $query->where('category_id', $id);
        }])->having('products_count', '>', 0)
            ->get();

        $products = $category->products()
            ->where('active', 1)
            ->with('variants')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $numberOfProducts = $products->total();

        return view('pages.category', compact('category', 'brands', 'products', 'numberOfProducts'));
    }

    /**
     * Hiển thị form chỉnh sửa
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category
            ]);
        }
        
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Cập nhật danh mục
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active') ? 1 : 0;

        $category->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh mục thành công!'
            ]);
        }

        return redirect()->route('admin.categories')
            ->with('success', 'Cập nhật danh mục thành công!');
    }

    /**
     * Xóa danh mục
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Check if category has products
        if ($category->products()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa danh mục có sản phẩm!'
                ], 422);
            }
            
            return redirect()->route('admin.categories')
                ->with('error', 'Không thể xóa danh mục có sản phẩm!');
        }

        $category->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa danh mục thành công!'
            ]);
        }

        return redirect()->route('admin.categories')
            ->with('success', 'Xóa danh mục thành công!');
    }
}
