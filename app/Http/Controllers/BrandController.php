<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource (Admin).
     */
    public function index()
    {
        $brands = Brand::withCount('products')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.brands', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'active' => 'nullable|boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $logo->move(public_path('images/brands'), $logoName);
            $validated['logo'] = 'brands/' . $logoName;
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active') ? 1 : 0;

        Brand::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm thương hiệu thành công!'
            ]);
        }

        return redirect()->route('admin.brands')
            ->with('success', 'Thêm thương hiệu thành công!');
    }

    /**
     * Display the specified resource (Public).
     */
    public function show(string $id)
    {
        $brand = Brand::with(['products' => function($query) {
            $query->where('active', 1)->with('variants');
        }])->findOrFail($id);

        $products = $brand->products()->paginate(12);

        return view('pages.brand', compact('brand', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $brand = Brand::findOrFail($id);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'brand' => $brand
            ]);
        }
        
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brand = Brand::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'active' => 'nullable|boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($brand->logo && file_exists(public_path('images/' . $brand->logo))) {
                unlink(public_path('images/' . $brand->logo));
            }

            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $logo->move(public_path('images/brands'), $logoName);
            $validated['logo'] = 'brands/' . $logoName;
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active') ? 1 : 0;

        $brand->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thương hiệu thành công!'
            ]);
        }

        return redirect()->route('admin.brands')
            ->with('success', 'Cập nhật thương hiệu thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::findOrFail($id);

        // Check if brand has products
        if ($brand->products()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa thương hiệu có sản phẩm!'
                ], 422);
            }
            
            return redirect()->route('admin.brands')
                ->with('error', 'Không thể xóa thương hiệu có sản phẩm!');
        }

        // Delete logo if exists
        if ($brand->logo && file_exists(public_path('images/' . $brand->logo))) {
            unlink(public_path('images/' . $brand->logo));
        }

        $brand->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa thương hiệu thành công!'
            ]);
        }

        return redirect()->route('admin.brands')
            ->with('success', 'Xóa thương hiệu thành công!');
    }
}
