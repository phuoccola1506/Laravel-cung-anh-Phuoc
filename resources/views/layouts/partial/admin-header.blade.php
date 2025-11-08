<!-- Admin Header -->
<header class="admin-header">
    <button class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="admin-search">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Tìm kiếm...">
    </div>

    <div class="admin-actions">
        <button class="action-btn" title="Thông báo">
            <i class="fas fa-bell"></i>
            <span class="badge">5</span>
        </button>
        <button class="action-btn" title="Tin nhắn">
            <i class="fas fa-envelope"></i>
            <span class="badge">3</span>
        </button>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="action-btn" title="Đăng xuất">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </form>
    </div>
</header>
