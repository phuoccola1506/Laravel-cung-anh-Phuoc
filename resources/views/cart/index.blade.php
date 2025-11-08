@extends('layouts.app')

@section('title', 'My Cart')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/cart.js') }}"></script>
@endpush

@section('content')
    <main>
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <div class="container">
                <a href="{{ route('home') }}">Trang chủ</a>
                <i class="fas fa-chevron-right"></i>
                <span>Giỏ hàng</span>
            </div>
        </div>

        <!-- Cart Content -->
        <section class="cart-section">
            <div class="container">
                <h1 class="page-title">Giỏ hàng của bạn</h1>

                <div class="cart-layout">
                    <div class="cart-content">
                        <div class="cart-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Hình ảnh</th>
                                        <th>Sản phẩm</th>
                                        <th>SKU</th>
                                        <th>Giảm giá</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Tổng tiền</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody">
                                    @foreach ($cartItems as $item)
                                        <tr data-row-id="{{ $item['rowId'] }}">
                                            <td>
                                                <img src="{{ asset('images/' . $item['options']['image']) }}" 
                                                     alt="{{ $item['name'] }}" class="cart-product-image" width="100" height="100">
                                            </td>
                                            <td>
                                                <div class="cart-product-name">{{ $item['name'] }}</div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $item['options']['sku'] ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $item['options']['discount'] ?? 'N/A' }}%</span>
                                            </td>
                                            <td>{{ number_format(round($item['price'] / 10000) * 10000, 0, ',', '.') }} ₫</td>
                                            <td>
                                                <div class="cart-quantity">
                                                    {{-- <button class="qty-btn" onclick="updateCartItemQuantity('{{ $item['rowId'] }}', {{ $item['qty'] - 1 }})">
                                                        <i class="fas fa-minus"></i>
                                                    </button> --}}
                                                    <input type="number" value="{{ $item['qty'] }}" min="1" 
                                                           onchange="updateCartItemQuantity('{{ $item['rowId'] }}', this.value)">
                                                    {{-- <button class="qty-btn" onclick="updateCartItemQuantity('{{ $item['rowId'] }}', {{ $item['qty'] + 1 }})">
                                                        <i class="fas fa-plus"></i>
                                                    </button> --}}
                                                </div>
                                            </td>
                                            <td class="cart-total">{{ number_format(round($item['subtotal'] / 10000) * 10000, 0, ',', '.') }} ₫</td>
                                            <td>
                                                <button class="btn btn-danger" onclick="removeFromCart('{{ $item['rowId'] }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="cart-actions-bottom">
                            <a href="{{ route('home') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                            </a>
                            <button class="btn btn-outline" onclick="clearCart()">
                                <i class="fas fa-trash"></i> Xóa giỏ hàng
                            </button>
                        </div>
                    </div>

                    <div class="cart-sidebar">
                        <!-- Coupon Box -->
                        <div class="coupon-box">
                            <h3><i class="fas fa-ticket-alt"></i> Mã giảm giá</h3>
                            <div class="coupon-input">
                                <input type="text" placeholder="Nhập mã giảm giá" id="couponCode">
                                <button class="btn btn-primary" onclick="applyCoupon()">Áp dụng</button>
                            </div>
                            
                            <!-- Applied Coupons -->
                            <div id="appliedCouponsContainer" style="display: {{ count($appliedCoupons) > 0 ? 'block' : 'none' }}; margin-bottom: 15px;">
                                @if(count($appliedCoupons) > 0)
                                    <h4 style="margin-bottom: 10px; font-size: 0.95rem;">Mã đã áp dụng:</h4>
                                    @foreach($appliedCoupons as $coupon)
                                        <div class="applied-coupon-item">
                                            <div>
                                                <strong>{{ $coupon['code'] }}</strong>
                                                <small>
                                                    @if($coupon['type'] === 'percentage')
                                                        Giảm {{ $coupon['percentage'] }}%
                                                    @elseif($coupon['type'] === 'amount')
                                                        Giảm {{ number_format($coupon['amount'], 0, ',', '.') }} {{ $currency }}
                                                    @elseif($coupon['type'] === 'shipping')
                                                        Miễn phí vận chuyển
                                                    @endif
                                                </small>
                                            </div>
                                            <button onclick="removeCoupon('{{ $coupon['code'] }}')" class="btn-remove-coupon">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            
                            <div class="coupon-list">
                                <h4 style="margin-bottom: 10px; font-size: 0.95rem;">Mã giảm giá hiện có:</h4>
                                @forelse($userDiscounts as $discount)
                                    <div class="coupon-item" onclick="selectCoupon('{{ $discount->code }}')">
                                        <div class="coupon-code">{{ $discount->code }}</div>
                                        <div class="coupon-desc">
                                            @if($discount->type === 'percentage')
                                                Giảm {{ $discount->percentage }}%
                                            @elseif($discount->type === 'amount')
                                                Giảm {{ number_format($discount->amount, 0, ',', '.') }} {{ $currency }}
                                            @elseif($discount->type === 'shipping')
                                                Miễn phí vận chuyển
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted text-center py-3">
                                        <i class="fas fa-ticket-alt"></i>
                                        <p class="mb-0 mt-2">Bạn chưa có mã giảm giá nào</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Cart Summary -->
                        <div class="cart-summary">
                            <h3>Tổng  ₫ơn hàng</h3>
                            <div class="summary-row">
                                <span>Tạm tính:</span>
                                <strong id="cartSubtotal">{{ number_format($calculation['subtotal'], 0, ',', '.') }} ₫</strong>
                            </div>
                            <div class="summary-row">
                                <span>Phí vận chuyển:</span>
                                <strong id="cartShipping">{{ number_format($calculation['shipping'], 0, ',', '.') }} ₫</strong>
                            </div>
                            <div class="summary-row discount" id="discountRow" style="display: {{ $calculation['discount'] > 0 ? 'flex' : 'none' }};">
                                <span>Giảm giá:</span>
                                <strong class="text-success" id="cartDiscount">-{{ number_format($calculation['discount'], 0, ',', '.') }} {{ $currency }}</strong>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row summary-total">
                                <span>Tổng cộng:</span>
                                <strong id="cartTotal">{{ number_format($calculation['total'], 0, ',', '.') }} {{ $currency }}</strong>
                            </div>
                            <button class="btn btn-primary btn-block btn-large" onclick="proceedToCheckout()">
                                <i class="fas fa-credit-card"></i> Thanh toán
                            </button>
                            <div class="payment-methods">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/200px-Visa_Inc._logo.svg.png"
                                    alt="Visa">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/200px-Mastercard-logo.svg.png"
                                    alt="Mastercard">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/American_Express_logo_%282018%29.svg/200px-American_Express_logo_%282018%29.svg.png"
                                    alt="Amex">
                            </div>
                        </div>

                        <!-- Trust Badges -->
                        <div class="trust-badges">
                            <div class="trust-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Thanh toán an toàn 100%</span>
                            </div>
                            <div class="trust-item">
                                <i class="fas fa-undo-alt"></i>
                                <span>Hoàn tiền trong 15 ngày</span>
                            </div>
                            <div class="trust-item">
                                <i class="fas fa-headset"></i>
                                <span>Hỗ trợ 24/7</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
