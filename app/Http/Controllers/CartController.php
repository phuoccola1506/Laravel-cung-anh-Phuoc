<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductVariant;
use App\Models\Setting;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cartItems = Cart::content()->map(function ($item) {
            return [
                'rowId' => $item->rowId,
                'id' => $item->id,
                'name' => $item->name,
                'qty' => $item->qty,
                'price' => (int) $item->price,
                'subtotal' => (int) $item->subtotal,
                'options' => $item->options,
            ];
        });

        // Tính tổng thủ công từ items vì Cart::total() trả về decimal
        $cartTotal = Cart::content()->sum(function ($item) {
            return (int) $item->subtotal;
        });
        $cartCount = Cart::count();

        // Lấy danh sách mã giảm giá của user (chưa dùng và còn hiệu lực)
        $userDiscounts = DB::table('discount_user')
            ->join('discounts', 'discount_user.discount_id', '=', 'discounts.id')
            ->where('discount_user.user_id', Auth::id())
            ->where('discount_user.used', 0)
            ->where('discounts.active', 1)
            ->where('discounts.start_date', '<=', now())
            ->where('discounts.end_date', '>=', now())
            ->select('discounts.*')
            ->get();

        // Lấy các mã đã áp dụng
        $appliedCoupons = session()->get('applied_coupons', []);
        
        // Tính toán với discount
        $calculation = $this->calculateCartTotal($cartTotal, $appliedCoupons);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $cartItems,
                'total' => $cartTotal,
                'count' => $cartCount,
            ]);
        }

        return view('cart.index', compact('cartItems', 'cartTotal', 'cartCount', 'userDiscounts', 'appliedCoupons', 'calculation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function addToCart(Request $request)
    {
        try {
            // Validate đầu vào
            $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'variant_id' => 'required|integer|exists:product_variants,id',
                'quantity'   => 'nullable|integer|min:1'
            ]);

            $productId = $request->input('product_id');
            $variantId = $request->input('variant_id');

            // Lấy variant đúng thuộc product (chỉ active = 1)
            $variant = ProductVariant::with(['product' => function($query) {
                $query->where('active', 1);
            }])
                ->where('product_id', $productId)
                ->where('active', 1)
                ->findOrFail($variantId);
            
            // Kiểm tra product có active không
            if (!$variant->product || $variant->product->active != 1) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sản phẩm không còn khả dụng!'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Sản phẩm không còn khả dụng!');
            }

            // Kiểm tra stock
            $requestedQty = $request->input('quantity', 1);
            
            if ($variant->stock < 1) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sản phẩm đã hết hàng!'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Sản phẩm đã hết hàng!');
            }
            
            if ($requestedQty > $variant->stock) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Chỉ còn {$variant->stock} sản phẩm trong kho!"
                    ], 422);
                }
                return redirect()->back()->with('error', "Chỉ còn {$variant->stock} sản phẩm trong kho!");
            }

            // Tính giá cuối cùng
            $finalPrice = $variant->discount > 0
                ? $variant->price * (1 - $variant->discount / 100)
                : $variant->price;

            // Format thuộc tính hiển thị
            $attributesDisplay = collect($variant->attributes ?? [])->map(function ($value, $key) {
                $labels = [
                    'storage' => 'Dung lượng',
                    'color' => 'Màu sắc',
                    'ram' => 'RAM',
                    'cpu' => 'CPU',
                    'dpi' => 'DPI',
                    'connection' => 'Kết nối',
                    'switch_type' => 'Switch',
                    'layout' => 'Layout',
                    'sensor' => 'Cảm biến',
                    'noise_cancellation' => 'Chống ồn',
                    'battery_life' => 'Thời lượng pin',
                ];
                $label = $labels[$key] ?? ucfirst($key);
                return "$label: $value";
            })->join(', ');

            // Thêm vào giỏ hàng
            Cart::add([
                'id' => $variant->id,
                'name' => $variant->product->name,
                'qty' => $requestedQty,
                'price' => $finalPrice,
                'options' => [
                    'product_id' => $productId,
                    'sku' => $variant->sku,
                    'attributes' => $variant->attributes,
                    'attributes_display' => $attributesDisplay,
                    'image' => $variant->image ?? $variant->product->image,
                    'discount' => $variant->discount,
                    'original_price' => $variant->price,
                ],
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                    'cart_count' => Cart::count()
                ]);
            }

            return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm sản phẩm!');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, string $rowId)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1|max:100'
            ]);

            // Lấy item hiện tại
            $cartItem = Cart::get($rowId);
            
            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại trong giỏ hàng!'
                ], 404);
            }

            // Kiểm tra stock từ variant (chỉ active = 1)
            $variant = ProductVariant::where('active', 1)->find($cartItem->id);
            
            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không còn khả dụng!'
                ], 404);
            }

            // Kiểm tra số lượng yêu cầu có vượt quá stock không
            if ($request->quantity > $variant->stock) {
                return response()->json([
                    'success' => false,
                    'message' => "Chỉ còn {$variant->stock} sản phẩm trong kho!"
                ], 422);
            }

            Cart::update($rowId, $request->quantity);

            if ($request->expectsJson()) {
                // Lấy item vừa update để trả về subtotal mới
                $updatedItem = Cart::get($rowId);
                
                // Tính tổng giỏ hàng
                $cartTotal = Cart::content()->sum(function ($item) {
                    return (int) $item->subtotal;
                });
                
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật số lượng!',
                    'cart_count' => Cart::count(),
                    'item_subtotal' => (int) $updatedItem->subtotal,
                    'cart_total' => $cartTotal
                ]);
            }

            return redirect()->back()->with('success', 'Đã cập nhật số lượng!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Có lỗi xảy ra!');
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, string $rowId)
    {
        try {
            Cart::remove($rowId);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                    'cart_count' => Cart::count()
                ]);
            }

            return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Có lỗi xảy ra!');
        }
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        try {
            Cart::destroy();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa toàn bộ giỏ hàng!'
                ]);
            }

            return redirect()->back()->with('success', 'Đã xóa toàn bộ giỏ hàng!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Có lỗi xảy ra!');
        }
    }

    /**
     * Apply coupon code
     */
    public function applyCoupon(Request $request)
    {
        try {
            $code = strtoupper(trim($request->input('code')));
            
            if (!$code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập mã giảm giá!'
                ], 422);
            }

            // Kiểm tra mã có tồn tại trong discount_user của user không
            $discount = DB::table('discount_user')
                ->join('discounts', 'discount_user.discount_id', '=', 'discounts.id')
                ->where('discount_user.user_id', Auth::id())
                ->where('discounts.code', $code)
                ->where('discount_user.used', 0)
                ->where('discounts.active', 1)
                ->where('discounts.start_date', '<=', now())
                ->where('discounts.end_date', '>=', now())
                ->select('discounts.*')
                ->first();

            if (!$discount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn!'
                ], 404);
            }

            // Lấy các mã đã áp dụng từ session
            $appliedCoupons = session()->get('applied_coupons', []);

            // Kiểm tra rule: 1 mã percentage/amount + 1 mã shipping
            if ($discount->type === 'shipping') {
                // Kiểm tra đã có mã shipping chưa
                $hasShipping = collect($appliedCoupons)->contains('type', 'shipping');
                if ($hasShipping) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã áp dụng mã miễn phí vận chuyển rồi!'
                    ], 422);
                }
            } else {
                // Kiểm tra đã có mã percentage/amount chưa
                $hasDiscount = collect($appliedCoupons)->whereIn('type', ['percentage', 'amount'])->isNotEmpty();
                if ($hasDiscount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn chỉ được áp dụng 1 mã giảm giá cho đơn hàng!'
                    ], 422);
                }
            }

            // Kiểm tra mã đã được áp dụng chưa
            if (collect($appliedCoupons)->contains('code', $code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá này đã được áp dụng!'
                ], 422);
            }

            // Thêm mã vào session
            $appliedCoupons[] = [
                'id' => $discount->id,
                'code' => $discount->code,
                'type' => $discount->type,
                'percentage' => $discount->percentage,
                'amount' => $discount->amount,
            ];
            session()->put('applied_coupons', $appliedCoupons);

            // Tính lại tổng tiền
            $cartTotal = Cart::content()->sum(function ($item) {
                return (int) $item->subtotal;
            });

            $calculation = $this->calculateCartTotal($cartTotal, $appliedCoupons);

            return response()->json([
                'success' => true,
                'message' => 'Đã áp dụng mã giảm giá thành công!',
                'discount' => $discount,
                'applied_coupons' => $appliedCoupons,
                'calculation' => $calculation
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove coupon
     */
    public function removeCoupon(Request $request)
    {
        try {
            $code = strtoupper(trim($request->input('code')));
            
            $appliedCoupons = session()->get('applied_coupons', []);
            $appliedCoupons = collect($appliedCoupons)->reject(function ($coupon) use ($code) {
                return $coupon['code'] === $code;
            })->values()->toArray();
            
            session()->put('applied_coupons', $appliedCoupons);

            // Tính lại tổng tiền
            $cartTotal = Cart::content()->sum(function ($item) {
                return (int) $item->subtotal;
            });

            $calculation = $this->calculateCartTotal($cartTotal, $appliedCoupons);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa mã giảm giá!',
                'applied_coupons' => $appliedCoupons,
                'calculation' => $calculation
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show checkout page
     */
    public function checkout()
    {
        // Kiểm tra giỏ hàng có trống không
        if (Cart::count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $cartItems = Cart::content()->map(function ($item) {
            return [
                'rowId' => $item->rowId,
                'id' => $item->id,
                'name' => $item->name,
                'qty' => $item->qty,
                'price' => (int) $item->price,
                'subtotal' => (int) $item->subtotal,
                'options' => $item->options,
            ];
        });

        // Tính tổng thủ công
        $cartTotal = Cart::content()->sum(function ($item) {
            return (int) $item->subtotal;
        });

        // Lấy các mã đã áp dụng
        $appliedCoupons = session()->get('applied_coupons', []);
        
        // Tính toán với discount
        $calculation = $this->calculateCartTotal($cartTotal, $appliedCoupons);

        return view('checkout.index', compact('cartItems', 'appliedCoupons', 'calculation'));
    }

    /**
     * Process checkout and create order
     */
    public function processCheckout(Request $request)
    {
        try {
            // Log request để debug
            Log::info('Checkout process started', [
                'user_id' => Auth::id(),
                'cart_count' => Cart::count(),
                'request_data' => $request->all()
            ]);
            
            // Validate form input
            $validated = $request->validate([
                'fullname' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'email' => 'required|email|max:255',
                'city' => 'required|string|max:100',
                'district' => 'required|string|max:100',
                'ward' => 'required|string|max:100',
                'address' => 'required|string|max:500',
                'payment_method' => 'required|in:cod,bank,vnpay,momo',
                'notes' => 'nullable|string|max:1000',
            ]);
            
            Log::info('Validation passed', ['validated' => $validated]);

            // Kiểm tra giỏ hàng
            if (Cart::count() == 0) {
                return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
            }

            // Tạo địa chỉ giao hàng đầy đủ
            $shippingAddress = "{$validated['address']}, {$validated['ward']}, {$validated['district']}, {$validated['city']}";
            $shippingAddress .= "\nSĐT: {$validated['phone']}\nEmail: {$validated['email']}\nNgười nhận: {$validated['fullname']}";

            // Tính tổng đơn hàng
            $cartTotal = Cart::content()->sum(function ($item) {
                return (int) $item->subtotal;
            });

            $appliedCoupons = session()->get('applied_coupons', []);
            $calculation = $this->calculateCartTotal($cartTotal, $appliedCoupons);

            // Lấy discount_id nếu có
            $discountId = null;
            if (!empty($appliedCoupons)) {
                foreach ($appliedCoupons as $coupon) {
                    if (in_array($coupon['type'], ['percentage', 'amount'])) {
                        $discountId = $coupon['id'];
                        break;
                    }
                }
            }

            // Tạo mã đơn hàng
            $orderCode = 'ORD' . strtoupper(uniqid());
            
            Log::info('Creating order', [
                'order_code' => $orderCode,
                'user_id' => Auth::id(),
                'discount_id' => $discountId,
                'calculation' => $calculation
            ]);

            // Tạo đơn hàng
            $orderId = DB::table('orders')->insertGetId([
                'order_code' => $orderCode,
                'user_id' => Auth::id(),
                'discount_id' => $discountId,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'shipping_address' => $shippingAddress,
                'notes' => $validated['notes'],
                'subtotal' => $calculation['subtotal'],
                'shipping_fee' => $calculation['shipping'],
                'discount' => $calculation['discount'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            Log::info('Order created successfully', ['order_id' => $orderId]);

            // Thêm chi tiết đơn hàng
            foreach (Cart::content() as $item) {
                // Lấy thông tin product variant
                $variant = ProductVariant::with('product')->find($item->id);
                
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $item->id,
                    'variant_id' => $item->id, // Same as product_variant_id
                    'sku' => $variant->sku,
                    'product_name' => $variant->product->name,
                    'attributes' => json_encode($item->options->attributes ?? []),
                    'price' => (int) $item->price,
                    'quantity' => $item->qty,
                    // Không cần 'total_price' vì nó là GENERATED column
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Giảm stock
                DB::table('product_variants')
                    ->where('id', $item->id)
                    ->decrement('stock', $item->qty);
            }

            // Đánh dấu discount đã sử dụng
            if ($discountId) {
                DB::table('discount_user')
                    ->where('user_id', Auth::id())
                    ->where('discount_id', $discountId)
                    ->where('used', 0)
                    ->update([
                        'used' => 1,
                        'used_at' => now()
                    ]);
            }

            // Xóa giỏ hàng và session
            Cart::destroy();
            session()->forget('applied_coupons');

            return redirect()->route('order.success', $orderId)->with('success', 'Đặt hàng thành công!');

        } catch (\Exception $e) {
            Log::error('Checkout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Calculate cart total with discounts
     */
    private function calculateCartTotal($cartTotal, $appliedCoupons)
    {
        $subtotal = $cartTotal;
        // Lấy shipping fee từ settings, mặc định 50000 nếu chưa set
        $shippingFee = (float) Setting::get('shipping_fee', 50000);
        $discountAmount = 0;
        $shippingDiscount = 0;

        foreach ($appliedCoupons as $coupon) {
            if ($coupon['type'] === 'percentage') {
                $discountAmount += ($subtotal * $coupon['percentage'] / 100);
            } elseif ($coupon['type'] === 'amount') {
                $discountAmount += $coupon['amount'];
            } elseif ($coupon['type'] === 'shipping') {
                $shippingDiscount = $shippingFee;
            }
        }

        // Làm tròn đến hàng chục nghìn
        $subtotalRounded = round($subtotal / 10000) * 10000;
        $discountRounded = round($discountAmount / 10000) * 10000;
        $shippingRounded = $shippingFee - $shippingDiscount;
        $total = $subtotalRounded - $discountRounded + $shippingRounded;

        return [
            'subtotal' => $subtotalRounded,
            'discount' => $discountRounded,
            'shipping' => $shippingRounded,
            'shipping_discount' => $shippingDiscount,
            'total' => $total
        ];
    }
}
