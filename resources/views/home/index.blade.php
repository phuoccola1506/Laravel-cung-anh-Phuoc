@extends('layouts.app')

@section('title', 'Home Page')

@push('scripts')
    <script src="{{ asset('js/cart.js') }}"></script>
@endpush

@section('content')
    <main>
        <!-- Slider Banner -->
        <section class="slider">
            <div class="slider-wrapper">
                <div class="slider-main">
                    <div class="slide active">
                        <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=1200&h=400&fit=crop"
                            alt="Banner 1">
                    </div>
                    <div class="slide">
                        <img src="https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=1200&h=400&fit=crop"
                            alt="Banner 2">
                    </div>
                    <div class="slide">
                        <img src="https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=1200&h=400&fit=crop"
                            alt="Banner 3">
                    </div>
                </div>
                <div class="slider-dots"></div>
            </div>
        </section>

        <!-- Features -->
        <section class="py-4 bg-light">
            <div class="container">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded shadow-sm">
                            <i class="fas fa-shipping-fast fs-2 text-primary-custom"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">Giao hàng miễn phí</h6>
                                <small class="text-muted">Áp dụng cho đơn từ 500.000đ</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded shadow-sm">
                            <i class="fas fa-undo-alt fs-2 text-primary-custom"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">Đổi trả trong 15 ngày</h6>
                                <small class="text-muted">Miễn phí đổi trả hàng</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded shadow-sm">
                            <i class="fas fa-shield-alt fs-2 text-primary-custom"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">Bảo hành chính hãng</h6>
                                <small class="text-muted">Bảo hành toàn quốc</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded shadow-sm">
                            <i class="fas fa-headset fs-2 text-primary-custom"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">Hỗ trợ 24/7</h6>
                                <small class="text-muted">Tư vấn miễn phí</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Hot Deals -->
        <section class="py-5">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0"><i class="fas fa-fire text-danger me-2"></i> Khuyến mãi hot</h2>
                    <a href="pages/products.html?sale=true" class="text-decoration-none text-primary-custom">
                        Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="row g-4">
                    @foreach ($hot_sale_products as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Phones Section -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0"><i class="fas fa-mobile-alt text-primary-custom me-2"></i> Điện thoại nổi bật</h2>
                    <a href="pages/products.html?category=phone" class="text-decoration-none text-primary-custom">
                        Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="row g-4">
                    @foreach ($hot_phones as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Laptop Section -->
        <section class="py-5">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0"><i class="fas fa-laptop text-primary-custom me-2"></i> Laptop bán chạy</h2>
                    <a href="pages/products.html?category=laptop" class="text-decoration-none text-primary-custom">
                        Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="row g-4">
                    @foreach ($hot_laptops as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Accessories Banner -->
        <section class="banner-section">
            <div class="container">
                <div class="banner-grid">
                    <div class="banner-item">
                        <img src="https://images.unsplash.com/photo-1484704849700-f032a568e944?w=600&h=300&fit=crop"
                            alt="Tai nghe">
                        <div class="banner-content">
                            <h3>Tai nghe cao cấp</h3>
                            <p>Giảm đến 30%</p>
                            <a href="pages/products.html?category=accessory" class="btn btn-light">Mua ngay</a>
                        </div>
                    </div>
                    <div class="banner-item">
                        <img src="https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?w=600&h=300&fit=crop"
                            alt="Smartwatch">
                        <div class="banner-content">
                            <h3>Smartwatch thông minh</h3>
                            <p>Từ 2.990.000đ</p>
                            <a href="pages/products.html?category=smartwatch" class="btn btn-light">Khám phá</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
