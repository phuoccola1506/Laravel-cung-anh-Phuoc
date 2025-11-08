<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order code or customer
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('order_code', 'LIKE', '%' . $request->search . '%')
                  ->orWhereHas('user', function($query) use ($request) {
                      $query->where('name', 'LIKE', '%' . $request->search . '%')
                            ->orWhere('email', 'LIKE', '%' . $request->search . '%');
                  });
            });
        }

        $orders = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
        }

        return view('admin.orders', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['user', 'items.product', 'payment'])->findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'order' => $order
            ]);
        }

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $order = Order::with(['items'])->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'order' => $order
            ]);
        }

        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipping,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
            'shipping_address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đơn hàng thành công!',
                'order' => $order->load(['user', 'items.product'])
            ]);
        }

        return redirect()->route('admin.orders')
            ->with('success', 'Cập nhật đơn hàng thành công!');
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipping,delivered,cancelled',
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái đơn hàng thành công!',
            'order' => $order
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        
        // Only allow cancellation if order is pending or processing
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng đã giao hoặc đang vận chuyển!'
            ], 400);
        }

        $order->update(['status' => 'cancelled']);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hủy đơn hàng thành công!'
            ]);
        }

        return redirect()->route('admin.orders')
            ->with('success', 'Hủy đơn hàng thành công!');
    }

    /**
     * Order success page
     */
    public function success($id)
    {
        $order = Order::with(['items.variant.product', 'discount', 'user'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('order.success', compact('order'));
    }
}
