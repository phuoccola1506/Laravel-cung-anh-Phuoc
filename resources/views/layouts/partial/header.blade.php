@php
    use Gloudemans\Shoppingcart\Facades\Cart;
@endphp

<!-- Header -->
<header class="header">
    <!-- Header Top -->
    <div class="header-top">
        <div class="container">
            <div class="d-flex justify-content-end align-items-center">
                <div class="d-flex gap-3">
                    <a href="tel:{{ str_replace(['-', '.', ' '], '', setting('contact_phone', '18001234')) }}" class="text-decoration-none text-muted">
                        <i class="fas fa-phone"></i> {{ setting('contact_phone', '1800.1234') }}
                    </a>
                    <a href="#" class="text-decoration-none text-muted">
                        <i class="fas fa-map-marker-alt"></i> Hệ thống cửa hàng
                    </a>
                    <a href="#" class="text-decoration-none text-muted">{{ setting('site_description', 'Điện thoại, laptop, tablet và phụ kiện chính hãng.') }}</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="nav-main">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between py-2">
                <!-- Logo -->
                <div class="logo">
                    <a href="{{ route('home') }}"
                        class="text-decoration-none text-white d-flex align-items-center gap-2">
                        <h1 class="mb-0">
                            {{ setting('site_name', 'Tech') }}
                            {{-- <span>Shop</span> --}}
                        </h1>
                    </a>
                </div>

                <!-- Navigation Menu -->
                <ul class="nav-menu list-unstyled mb-0 d-flex align-items-center gap-3">
                    <li class="position-relative">
                        <a href="#" class="text-decoration-none text-white">
                            <i class="fa-solid fa-bars"></i> Danh mục
                        </a>
                        <div class="mega-menu rounded-3">
                            <div class="container">
                                <div class="mega-menu-content">
                                    <div class="mega-menu-categories">
                                        @foreach ($categories as $category)
                                            <x-category-card :category="$category" />
                                        @endforeach
                                    </div>

                                    <div class="mega-menu-brands">
                                        <div class="brand-section">
                                            <h4>Hãng điện thoại</h4>
                                            <div class="brand-grid">
                                                <a href="#" class="brand-item text-decoration-none"><i
                                                        class="fab fa-apple text-warning"></i> Apple</a>
                                                <a href="#" class="brand-item text-decoration-none">SAMSUNG</a>
                                                <a href="#" class="brand-item text-decoration-none">xiaomi</a>
                                                <a href="#" class="brand-item text-decoration-none">OPPO</a>
                                                <a href="#" class="brand-item text-decoration-none">TECNO</a>
                                                <a href="#" class="brand-item text-decoration-none">HONOR</a>
                                                <a href="#" class="brand-item text-decoration-none">ZTE
                                                    nubia</a>
                                                <a href="#" class="brand-item text-decoration-none">SONY</a>
                                                <a href="#" class="brand-item text-decoration-none">NOKIA</a>
                                                <a href="#" class="brand-item text-decoration-none">Infinix</a>
                                                <a href="#" class="brand-item text-decoration-none">NOTHING</a>
                                                <a href="#" class="brand-item text-decoration-none">Masstel</a>
                                                <a href="#" class="brand-item text-decoration-none">realme</a>
                                                <a href="#" class="brand-item text-decoration-none">itel</a>
                                                <a href="#" class="brand-item text-decoration-none">vivo</a>
                                            </div>
                                            <div class="mt-3">
                                                <a href="#" class="text-primary-custom text-decoration-none">Điện
                                                    thoại phổ
                                                    thông</a>
                                            </div>
                                        </div>

                                        <div class="brand-section">
                                            <h4>Điện thoại HOT <i class="fas fa-bolt text-warning"></i></h4>
                                            <div class="brand-grid">
                                                <a href="#" class="brand-item hot text-decoration-none">iPhone
                                                    17</a>
                                                <a href="#" class="brand-item hot text-decoration-none">iPhone
                                                    Air</a>
                                                <a href="#" class="brand-item hot text-decoration-none">iPhone
                                                    16</a>
                                                <a href="#" class="brand-item hot text-decoration-none">Galaxy Z
                                                    Fold7</a>
                                                <a href="#" class="brand-item text-decoration-none">S25
                                                    Ultra</a>
                                                <a href="#" class="brand-item hot text-decoration-none">OPPO
                                                    Reno14</a>
                                                <a href="#" class="brand-item text-decoration-none">Xiaomi
                                                    15T</a>
                                                <a href="#" class="brand-item hot text-decoration-none">OPPO
                                                    Find X9</a>
                                                <a href="#" class="brand-item text-decoration-none">Samsung
                                                    Galaxy S25 FE</a>
                                                <a href="#" class="brand-item text-decoration-none">Redmi
                                                    Note 14</a>
                                                <a href="#" class="brand-item hot text-decoration-none">HONOR
                                                    Magic v5</a>
                                                <a href="#" class="brand-item hot text-decoration-none">iPhone
                                                    17 Pro</a>
                                                <a href="#" class="brand-item hot text-decoration-none">OPPO
                                                    Find X9 Pro</a>
                                                <a href="#" class="brand-item text-decoration-none">Sony
                                                    Xperia 1 VII</a>
                                                <a href="#" class="brand-item text-decoration-none">Redmi
                                                    15C 4GB</a>
                                            </div>

                                            <div class="price-range-section">
                                                <h4>Mức giá điện thoại</h4>
                                                <div class="price-range-grid">
                                                    <a href="#" class="brand-item text-decoration-none">Dưới
                                                        2 triệu</a>
                                                    <a href="#" class="brand-item text-decoration-none">Từ 2
                                                        - 4 triệu</a>
                                                    <a href="#" class="brand-item text-decoration-none">Từ 4
                                                        - 7 triệu</a>
                                                    <a href="#" class="brand-item text-decoration-none">Từ 7
                                                        - 13 triệu</a>
                                                    <a href="#" class="brand-item text-decoration-none">Từ
                                                        13 - 20 triệu</a>
                                                    <a href="#" class="brand-item text-decoration-none">Trên
                                                        20 triệu</a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="brand-section">
                                            <h4>Hãng máy tính bảng</h4>
                                            <div class="brand-grid">
                                                <a href="#" class="brand-item text-decoration-none"><i
                                                        class="fab fa-apple text-warning"></i> iPad</a>
                                                <a href="#" class="brand-item text-decoration-none">SAMSUNG</a>
                                                <a href="#" class="brand-item text-decoration-none">xiaomi</a>
                                                <a href="#" class="brand-item text-decoration-none">HUAWEI</a>
                                                <a href="#" class="brand-item text-decoration-none">Lenovo</a>
                                                <a href="#" class="brand-item text-decoration-none">TECLAST</a>
                                                <a href="#" class="brand-item text-decoration-none">HONOR</a>
                                                <a href="#" class="brand-item text-decoration-none">nubia</a>
                                                <a href="#" class="brand-item text-decoration-none">Máy đọc
                                                    sách</a>
                                                <a href="#" class="brand-item text-decoration-none">Kindle</a>
                                                <a href="#" class="brand-item text-decoration-none">Boox</a>
                                                <a href="#" class="brand-item text-decoration-none">Xem thêm
                                                    tất cả Tablet</a>
                                            </div>

                                            <div class="mt-3">
                                                <h4>Máy tính bảng HOT <i class="fas fa-bolt text-warning"></i></h4>
                                                <div class="brand-grid">
                                                    <a href="#" class="brand-item hot text-decoration-none">iPad
                                                        Pro M5</a>
                                                    <a href="#" class="brand-item text-decoration-none">iPad
                                                        A16</a>
                                                    <a href="#" class="brand-item text-decoration-none">iPad
                                                        Pro 2024</a>
                                                    <a href="#" class="brand-item text-decoration-none">iPad
                                                        mini 7</a>
                                                    <a href="#"
                                                        class="brand-item hot text-decoration-none">Galaxy Tab S11
                                                        Series</a>
                                                    <a href="#" class="brand-item text-decoration-none">Galaxy
                                                        Tab S10
                                                        Series</a>
                                                    <a href="#" class="brand-item text-decoration-none">Lenovo
                                                        Idea Tab
                                                        Wifi</a>
                                                    <a href="#" class="brand-item text-decoration-none">Xiaomi
                                                        Pad Mini</a>
                                                    <a href="#"
                                                        class="brand-item hot text-decoration-none">Huawei MatePad
                                                        Pro 2025</a>
                                                    <a href="#"
                                                        class="brand-item hot text-decoration-none">HONOR Pad
                                                        X7</a>
                                                    <a href="#"
                                                        class="brand-item hot text-decoration-none">Teclast Wifi
                                                        P30</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- Search Bar -->
                <div class="search-bar flex-grow-1 mx-4">
                    <form action="{{ route('pages.search') }}" method="GET" class="d-flex" role="search">
                        <input name="keyword" type="search" class="form-control me-2" placeholder="Bạn cần tìm gì?"
                            value="{{ request('keyword') }}">
                        <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Cart & Login -->
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('cart.index') }}" class="cart-icon text-decoration-none text-white">
                        <i class="fas fa-shopping-cart fs-5"></i>
                        <span class="cart-count">{{ Cart::count() }}</span>
                        <span class="d-none d-lg-inline ms-2">Giỏ hàng</span>
                    </a>

                    @auth
                        <!-- User Menu (when logged in) -->
                        <div class="dropdown">
                            <a href="#"
                                class="cart-icon text-decoration-none text-white d-flex align-items-center gap-2 dropdown-toggle"
                                id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle fs-5"></i>
                                <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-user me-2"></i> Tài khoản của tôi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-box me-2"></i> Đơn hàng của tôi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-heart me-2"></i> Sản phẩm yêu thích
                                    </a>
                                </li>
                                @if (Auth::user()->role === 'admin')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.index') }}">
                                            <i class="fa-solid fa-lock me-2"></i> Dashboard
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item logout-btn" href="#"
                                        onclick="event.preventDefault(); logout();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @else
                        <!-- Login Button (when not logged in) -->
                        <a href="{{ route('login') }}"
                            class="cart-icon text-decoration-none text-white d-flex align-items-center gap-1">
                            <i class="fas fa-user"></i>
                            <span class="d-none d-lg-inline">Đăng nhập</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
