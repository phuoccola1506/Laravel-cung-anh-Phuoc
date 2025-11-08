<!-- Admin Sidebar -->
<aside class="admin-sidebar">
    <div class="admin-logo">
        <h2>Tech<span>Shop</span></h2>
        <p>Admin Panel</p>
    </div>

    <nav class="admin-nav">
        <a href="{{ route('admin.index') }}" class="nav-item text-decoration-none {{ request()->routeIs('admin.index') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.products') }}" class="nav-item text-decoration-none {{ request()->routeIs('admin.products') ? 'active' : '' }}">
            <i class="fas fa-box"></i>
            <span>Sản phẩm</span>
        </a>
        <a href="{{ route('admin.discounts') }}" class="nav-item text-decoration-none {{ request()->routeIs('admin.discounts') ? 'active' : '' }}">
            <i class="fas fa-tag"></i>
            <span>Giảm giá</span>
        </a>
        <a href="{{ route('admin.orders') }}" class="nav-item text-decoration-none {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
            <i class="fas fa-shopping-bag"></i>
            <span>Đơn hàng</span>
        </a>
        <a href="{{ route('admin.users') }}" class="nav-item text-decoration-none {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Người dùng</span>
        </a>
        <a href="{{ route('admin.analytics') }}" class="nav-item text-decoration-none {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i>
            <span>Thống kê</span>
        </a>
        <a href="{{ route('admin.settings') }}" class="nav-item text-decoration-none {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Cài đặt</span>
        </a>
    </nav>

    <div class="admin-user">
        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=d70018&color=fff" alt="Admin">
        <div>
            <h4>{{ Auth::user()->name ?? 'Admin' }}</h4>
            <a href="{{ route('home') }}">Về trang chủ</a>
        </div>
    </div>
</aside>
