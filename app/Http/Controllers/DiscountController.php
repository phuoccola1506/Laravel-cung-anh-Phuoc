<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Discount::where('active', 1);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            });
        }

        $discounts = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'discounts' => $discounts
            ]);
        }

        return view('admin.discounts', compact('discounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:discounts,code',
            'type' => 'required|in:percentage,amount,shipping',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);

        // Set percentage or amount based on type
        if ($validated['type'] === 'percentage') {
            $validated['percentage'] = $validated['value'];
            $validated['amount'] = null;
        } else {
            $validated['amount'] = $validated['value'];
            $validated['percentage'] = null;
        }

        $validated['active'] = $request->has('active') ? 1 : 0;

        $discount = Discount::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm mã giảm giá thành công!',
                'discount' => $discount
            ]);
        }

        return redirect()->route('admin.discounts')
            ->with('success', 'Thêm mã giảm giá thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $discount = Discount::with('users')->findOrFail($id);
        return response()->json(['discount' => $discount]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $discount = Discount::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'discount' => $discount
            ]);
        }

        return view('admin.discounts.edit', compact('discount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $discount = Discount::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:discounts,code,' . $id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
        ]);

        $validated['active'] = $request->has('active') ? 1 : 0;

        // Set amount or percentage based on type
        if ($validated['type'] == 'percentage') {
            $validated['percentage'] = $validated['value'];
            $validated['amount'] = null;
        } else {
            $validated['amount'] = $validated['value'];
            $validated['percentage'] = null;
        }

        $discount->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật mã giảm giá thành công!',
                'discount' => $discount
            ]);
        }

        return redirect()->route('admin.discounts')
            ->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $discount = Discount::findOrFail($id);

        // Soft delete: Set active to 0
        $discount->update(['active' => 0]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa mã giảm giá thành công!'
            ]);
        }

        return redirect()->route('admin.discounts')
            ->with('success', 'Xóa mã giảm giá thành công!');
    }
}
