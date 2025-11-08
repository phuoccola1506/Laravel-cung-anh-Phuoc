@extends('layouts.app')

@section('title', 'Thanh toán')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endpush

@section('content')
    <main>
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <div class="container">
                <a href="{{ route('home') }}">Trang chủ</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ route('cart.index') }}">Giỏ hàng</a>
                <i class="fas fa-chevron-right"></i>
                <span>Thanh toán</span>
            </div>
        </div>

        <!-- Checkout Content -->
        <section class="checkout-section">
            <div class="container">
                <h1 class="page-title">Thanh toán đơn hàng</h1>

                @if(session('error'))
                    <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; background: #fee; border: 1px solid #fcc; border-radius: 5px; color: #c00;">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success" style="padding: 15px; margin-bottom: 20px; background: #efe; border: 1px solid #cfc; border-radius: 5px; color: #060;">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; background: #fee; border: 1px solid #fcc; border-radius: 5px; color: #c00;">
                        <i class="fas fa-exclamation-circle"></i> <strong>Có lỗi xảy ra:</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                    @csrf
                    <div class="checkout-layout">
                        <!-- Left Column: Shipping & Payment Info -->
                        <div class="checkout-main">
                            <!-- Shipping Address -->
                            <div class="checkout-card">
                                <h2><i class="fas fa-map-marker-alt"></i> Thông tin giao hàng</h2>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="fullname">Họ và tên <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="fullname" name="fullname" 
                                               value="{{ auth()->user()->name }}" required>
                                        @error('fullname')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Số điện thoại <span class="required">*</span></label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="{{ auth()->user()->phone }}" 
                                               pattern="[0-9]{10}" 
                                               placeholder="0123456789" required>
                                        @error('phone')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="email">Email <span class="required">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="{{ auth()->user()->email }}" required>
                                        @error('email')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-4">
                                        <label for="city">Tỉnh/Thành phố <span class="required">*</span></label>
                                        <select class="form-control" id="city" name="city" required>
                                            <option value="">Chọn Tỉnh/Thành phố</option>
                                            <option value="Hồ Chí Minh">TP. Hồ Chí Minh</option>
                                            <option value="Hà Nội">Hà Nội</option>
                                            <option value="Đà Nẵng">Đà Nẵng</option>
                                            <option value="Cần Thơ">Cần Thơ</option>
                                            <option value="An Giang">An Giang</option>
                                            <option value="Bà Rịa - Vũng Tàu">Bà Rịa - Vũng Tàu</option>
                                            <option value="Bắc Giang">Bắc Giang</option>
                                            <option value="Bắc Kạn">Bắc Kạn</option>
                                            <option value="Bạc Liêu">Bạc Liêu</option>
                                            <option value="Bắc Ninh">Bắc Ninh</option>
                                            <option value="Bến Tre">Bến Tre</option>
                                            <option value="Bình Định">Bình Định</option>
                                            <option value="Bình Dương">Bình Dương</option>
                                            <option value="Bình Phước">Bình Phước</option>
                                            <option value="Bình Thuận">Bình Thuận</option>
                                            <option value="Cà Mau">Cà Mau</option>
                                            <option value="Cao Bằng">Cao Bằng</option>
                                            <option value="Đắk Lắk">Đắk Lắk</option>
                                            <option value="Đắk Nông">Đắk Nông</option>
                                            <option value="Điện Biên">Điện Biên</option>
                                            <option value="Đồng Nai">Đồng Nai</option>
                                            <option value="Đồng Tháp">Đồng Tháp</option>
                                            <option value="Gia Lai">Gia Lai</option>
                                            <option value="Hà Giang">Hà Giang</option>
                                            <option value="Hà Nam">Hà Nam</option>
                                            <option value="Hà Tĩnh">Hà Tĩnh</option>
                                            <option value="Hải Dương">Hải Dương</option>
                                            <option value="Hải Phòng">Hải Phòng</option>
                                            <option value="Hậu Giang">Hậu Giang</option>
                                            <option value="Hòa Bình">Hòa Bình</option>
                                            <option value="Hưng Yên">Hưng Yên</option>
                                            <option value="Khánh Hòa">Khánh Hòa</option>
                                            <option value="Kiên Giang">Kiên Giang</option>
                                            <option value="Kon Tum">Kon Tum</option>
                                            <option value="Lai Châu">Lai Châu</option>
                                            <option value="Lâm Đồng">Lâm Đồng</option>
                                            <option value="Lạng Sơn">Lạng Sơn</option>
                                            <option value="Lào Cai">Lào Cai</option>
                                            <option value="Long An">Long An</option>
                                            <option value="Nam Định">Nam Định</option>
                                            <option value="Nghệ An">Nghệ An</option>
                                            <option value="Ninh Bình">Ninh Bình</option>
                                            <option value="Ninh Thuận">Ninh Thuận</option>
                                            <option value="Phú Thọ">Phú Thọ</option>
                                            <option value="Phú Yên">Phú Yên</option>
                                            <option value="Quảng Bình">Quảng Bình</option>
                                            <option value="Quảng Nam">Quảng Nam</option>
                                            <option value="Quảng Ngãi">Quảng Ngãi</option>
                                            <option value="Quảng Ninh">Quảng Ninh</option>
                                            <option value="Quảng Trị">Quảng Trị</option>
                                            <option value="Sóc Trăng">Sóc Trăng</option>
                                            <option value="Sơn La">Sơn La</option>
                                            <option value="Tây Ninh">Tây Ninh</option>
                                            <option value="Thái Bình">Thái Bình</option>
                                            <option value="Thái Nguyên">Thái Nguyên</option>
                                            <option value="Thanh Hóa">Thanh Hóa</option>
                                            <option value="Thừa Thiên Huế">Thừa Thiên Huế</option>
                                            <option value="Tiền Giang">Tiền Giang</option>
                                            <option value="Trà Vinh">Trà Vinh</option>
                                            <option value="Tuyên Quang">Tuyên Quang</option>
                                            <option value="Vĩnh Long">Vĩnh Long</option>
                                            <option value="Vĩnh Phúc">Vĩnh Phúc</option>
                                            <option value="Yên Bái">Yên Bái</option>
                                        </select>
                                        @error('city')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="district">Quận/Huyện <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="district" name="district" 
                                               placeholder="VD: Quận 7, Huyện Củ Chi" required>
                                        @error('district')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="ward">Phường/Xã <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="ward" name="ward" 
                                               placeholder="VD: Phường Tân Phú" required>
                                        @error('ward')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="address">Địa chỉ cụ thể <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="address" name="address" 
                                           placeholder="Số nhà, tên đường" 
                                           value="{{ auth()->user()->address }}" required>
                                    @error('address')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="checkout-card">
                                <h2><i class="fas fa-credit-card"></i> Phương thức thanh toán</h2>
                                
                                <div class="payment-methods-list">
                                    @if(setting('payment_cod', '1') == '1')
                                    <label class="payment-method">
                                        <input type="radio" name="payment_method" value="cod" checked required>
                                        <div class="payment-content">
                                            <i class="fas fa-money-bill-wave"></i>
                                            <div class="payment-info">
                                                <strong>Thanh toán khi nhận hàng (COD)</strong>
                                                <small>Thanh toán bằng tiền mặt khi nhận hàng</small>
                                            </div>
                                        </div>
                                    </label>
                                    @endif

                                    @if(setting('payment_bank', '0') == '1')
                                    <label class="payment-method">
                                        <input type="radio" name="payment_method" value="bank" required>
                                        <div class="payment-content">
                                            <i class="fas fa-university"></i>
                                            <div class="payment-info">
                                                <strong>Chuyển khoản ngân hàng</strong>
                                                <small>Chuyển khoản qua Internet Banking</small>
                                            </div>
                                        </div>
                                    </label>
                                    @endif

                                    @if(setting('payment_vnpay', '0') == '1')
                                    <label class="payment-method">
                                        <input type="radio" name="payment_method" value="vnpay" required>
                                        <div class="payment-content">
                                            <i class="fas fa-wallet"></i>
                                            <div class="payment-info">
                                                <strong>VNPay</strong>
                                                <small>Thanh toán qua cổng VNPay</small>
                                            </div>
                                        </div>
                                    </label>
                                    @endif

                                    @if(setting('payment_momo', '0') == '1')
                                    <label class="payment-method">
                                        <input type="radio" name="payment_method" value="momo" required>
                                        <div class="payment-content">
                                            <i class="fas fa-mobile-alt"></i>
                                            <div class="payment-info">
                                                <strong>Ví MoMo</strong>
                                                <small>Thanh toán qua ví điện tử MoMo</small>
                                            </div>
                                        </div>
                                    </label>
                                    @endif
                                </div>
                                @error('payment_method')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Order Notes -->
                            <div class="checkout-card">
                                <h2><i class="fas fa-comment"></i> Ghi chú đơn hàng (Tùy chọn)</h2>
                                <div class="form-group">
                                    <textarea class="form-control" id="notes" name="notes" rows="4" 
                                              placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Order Summary -->
                        <div class="checkout-sidebar">
                            <div class="checkout-card order-summary-card">
                                <h2><i class="fas fa-shopping-cart"></i> Đơn hàng của bạn</h2>
                                
                                <!-- Products List -->
                                <div class="order-products">
                                    @foreach($cartItems as $item)
                                    <div class="order-product-item">
                                        <img src="{{ asset('images/' . $item['options']['image']) }}" 
                                             alt="{{ $item['name'] }}">
                                        <div class="order-product-info">
                                            <div class="order-product-name">{{ $item['name'] }}</div>
                                            {{-- <div class="order-product-variant">
                                                <small class="text-muted">{{ $item['options']['attributes_display'] ?? '' }}</small>
                                            </div> --}}
                                            <div class="order-product-qty">Số lượng: {{ $item['qty'] }}</div>
                                        </div>
                                        <div class="order-product-price">
                                            {{ number_format(round($item['subtotal'] / 10000) * 10000, 0, ',', '.') }} ₫
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Applied Coupons -->
                                @if(count($appliedCoupons) > 0)
                                <div class="order-coupons">
                                    <h4>Mã giảm giá đã áp dụng:</h4>
                                    @foreach($appliedCoupons as $coupon)
                                    <div class="order-coupon-item">
                                        <i class="fas fa-ticket-alt"></i>
                                        <strong>{{ $coupon['code'] }}</strong>
                                        @if($coupon['type'] === 'percentage')
                                            <small>(-{{ $coupon['percentage'] }}%)</small>
                                        @elseif($coupon['type'] === 'amount')
                                            <small>(-{{ number_format($coupon['amount'], 0, ',', '.') }}₫)</small>
                                        @elseif($coupon['type'] === 'shipping')
                                            <small>(Miễn phí ship)</small>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                <div class="order-divider"></div>

                                <!-- Order Summary -->
                                <div class="order-summary-row">
                                    <span>Tạm tính:</span>
                                    <strong>{{ number_format($calculation['subtotal'], 0, ',', '.') }} ₫</strong>
                                </div>
                                <div class="order-summary-row">
                                    <span>Phí vận chuyển:</span>
                                    <strong>{{ number_format($calculation['shipping'], 0, ',', '.') }} ₫</strong>
                                </div>
                                @if($calculation['discount'] > 0)
                                <div class="order-summary-row discount-row">
                                    <span>Giảm giá:</span>
                                    <strong class="text-success">-{{ number_format($calculation['discount'], 0, ',', '.') }} ₫</strong>
                                </div>
                                @endif
                                <div class="order-divider"></div>
                                <div class="order-summary-row order-total">
                                    <span>Tổng cộng:</span>
                                    <strong class="total-amount">{{ number_format($calculation['total'], 0, ',', '.') }} ₫</strong>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary btn-block btn-large" id="submitOrderBtn">
                                    <i class="fas fa-check-circle"></i> Hoàn tất đặt hàng
                                </button>

                                <div class="checkout-notes">
                                    <p><i class="fas fa-info-circle"></i> Bằng việc nhấn "Hoàn tất đặt hàng", bạn đồng ý với 
                                    <a href="#">Điều khoản sử dụng</a> và 
                                    <a href="#">Chính sách bảo mật</a> của chúng tôi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

    @push('scripts')
    <script>
        // Form validation và submit
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitOrderBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        });

        // Phone validation
        document.getElementById('phone').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
    @endpush
@endsection
