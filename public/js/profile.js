// Profile JavaScript

// Check authentication
function checkProfileAuth() {
    const user = JSON.parse(localStorage.getItem('currentUser'));
    if (!user) {
        window.location.href = 'login.html';
        return null;
    }
    return user;
}

// Load user data
function loadUserData() {
    const user = checkProfileAuth();
    if (!user) return;

    // Update profile info
    document.getElementById('userName').textContent = user.fullname;
    document.getElementById('userEmail').textContent = user.email;
    document.getElementById('userAvatar').src = user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.fullname)}`;

    // Load form data
    document.getElementById('fullname').value = user.fullname || '';
    document.getElementById('email').value = user.email || '';
    document.getElementById('phone').value = user.phone || '';
    document.getElementById('gender').value = user.gender || '';
    document.getElementById('birthday').value = user.birthday || '';

    // Load orders
    loadOrders();
    
    // Load favorites
    loadFavorites();
    
    // Load addresses
    loadAddresses();
}

// Handle avatar change
function handleAvatarChange(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const newAvatar = e.target.result;
            document.getElementById('userAvatar').src = newAvatar;
            
            // Update user data
            const user = JSON.parse(localStorage.getItem('currentUser'));
            user.avatar = newAvatar;
            localStorage.setItem('currentUser', JSON.stringify(user));
            
            // Update in users array
            const users = JSON.parse(localStorage.getItem('users') || '[]');
            const userIndex = users.findIndex(u => u.id === user.id);
            if (userIndex !== -1) {
                users[userIndex].avatar = newAvatar;
                localStorage.setItem('users', JSON.stringify(users));
            }
            
            showNotification('Cập nhật ảnh đại diện thành công!', 'success');
        };
        reader.readAsDataURL(file);
    }
}

// Handle account update
function handleUpdateAccount(event) {
    event.preventDefault();
    
    const fullname = document.getElementById('fullname').value;
    const phone = document.getElementById('phone').value;
    const gender = document.getElementById('gender').value;
    const birthday = document.getElementById('birthday').value;
    
    // Validate phone
    if (phone && !/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/.test(phone)) {
        showNotification('Số điện thoại không hợp lệ!', 'error');
        return;
    }
    
    // Update current user
    const user = JSON.parse(localStorage.getItem('currentUser'));
    user.fullname = fullname;
    user.phone = phone;
    user.gender = gender;
    user.birthday = birthday;
    localStorage.setItem('currentUser', JSON.stringify(user));
    
    // Update in users array
    const users = JSON.parse(localStorage.getItem('users') || '[]');
    const userIndex = users.findIndex(u => u.id === user.id);
    if (userIndex !== -1) {
        users[userIndex] = { ...users[userIndex], ...user };
        localStorage.setItem('users', JSON.stringify(users));
    }
    
    // Update display
    document.getElementById('userName').textContent = fullname;
    
    showNotification('Cập nhật thông tin thành công!', 'success');
}

// Handle password change
function handleChangePassword(event) {
    event.preventDefault();
    
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Get user data
    const user = JSON.parse(localStorage.getItem('currentUser'));
    const users = JSON.parse(localStorage.getItem('users') || '[]');
    const userData = users.find(u => u.id === user.id);
    
    // Validate current password
    if (userData.password !== currentPassword) {
        showNotification('Mật khẩu hiện tại không chính xác!', 'error');
        return;
    }
    
    // Validate new password
    if (newPassword.length < 8) {
        showNotification('Mật khẩu mới phải có ít nhất 8 ký tự!', 'error');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        showNotification('Mật khẩu xác nhận không khớp!', 'error');
        return;
    }
    
    // Update password
    const userIndex = users.findIndex(u => u.id === user.id);
    if (userIndex !== -1) {
        users[userIndex].password = newPassword;
        localStorage.setItem('users', JSON.stringify(users));
    }
    
    // Clear form
    document.getElementById('changePasswordForm').reset();
    
    showNotification('Đổi mật khẩu thành công!', 'success');
}

// Toggle password visibility
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Load orders
function loadOrders(status = 'all') {
    const user = JSON.parse(localStorage.getItem('currentUser'));
    let orders = JSON.parse(localStorage.getItem('orders') || '[]');
    
    // Filter by user
    orders = orders.filter(order => order.userId === user.id);
    
    // Filter by status
    if (status !== 'all') {
        orders = orders.filter(order => order.status === status);
    }
    
    const ordersList = document.getElementById('ordersList');
    
    if (orders.length === 0) {
        ordersList.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Không có đơn hàng nào</p>
                <a href="products.html" class="btn btn-primary">
                    <i class="fas fa-shopping-cart me-2"></i> Mua sắm ngay
                </a>
            </div>
        `;
        return;
    }
    
    const statusLabels = {
        pending: 'Chờ xác nhận',
        confirmed: 'Đã xác nhận',
        shipping: 'Đang giao',
        completed: 'Hoàn thành',
        cancelled: 'Đã hủy'
    };
    
    ordersList.innerHTML = orders.map(order => `
        <div class="order-item">
            <div class="order-header">
                <div>
                    <div class="order-id">Đơn hàng #${order.id}</div>
                    <small class="text-muted">${new Date(order.createdAt).toLocaleString('vi-VN')}</small>
                </div>
                <span class="order-status status-${order.status}">${statusLabels[order.status]}</span>
            </div>
            <div class="order-products">
                ${order.items.map(item => `
                    <div class="order-product">
                        <img src="${item.image}" alt="${item.name}" class="order-product-image">
                        <div class="order-product-info">
                            <div class="order-product-name">${item.name}</div>
                            <div class="text-muted small">Số lượng: ${item.quantity}</div>
                        </div>
                        <div class="order-product-price">${formatPrice(item.price * item.quantity)}</div>
                    </div>
                `).join('')}
            </div>
            <div class="order-footer">
                <div class="order-total">
                    Tổng tiền: ${formatPrice(order.total)}
                </div>
                <div>
                    ${order.status === 'pending' ? `
                        <button class="btn btn-sm btn-danger" onclick="cancelOrder(${order.id})">
                            <i class="fas fa-times me-2"></i> Hủy đơn
                        </button>
                    ` : ''}
                    <a href="order-tracking.html?id=${order.id}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-2"></i> Chi tiết
                    </a>
                </div>
            </div>
        </div>
    `).join('');
}

// Cancel order
function cancelOrder(orderId) {
    if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) return;
    
    const orders = JSON.parse(localStorage.getItem('orders') || '[]');
    const orderIndex = orders.findIndex(o => o.id === orderId);
    
    if (orderIndex !== -1) {
        orders[orderIndex].status = 'cancelled';
        localStorage.setItem('orders', JSON.stringify(orders));
        loadOrders();
        showNotification('Hủy đơn hàng thành công!', 'success');
    }
}

// Load favorites
function loadFavorites() {
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    const favoritesList = document.getElementById('favoritesList');
    
    if (favorites.length === 0) {
        favoritesList.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-heart-broken fa-3x text-muted mb-3"></i>
                <p class="text-muted">Chưa có sản phẩm yêu thích</p>
                <a href="products.html" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i> Khám phá sản phẩm
                </a>
            </div>
        `;
        return;
    }
    
    favoritesList.innerHTML = `
        <div class="row g-3">
            ${favorites.map(product => `
                <div class="col-md-6 col-lg-4">
                    <div class="favorite-card">
                        <button class="btn-remove-favorite" onclick="removeFavorite(${product.id})">
                            <i class="fas fa-times"></i>
                        </button>
                        <img src="${product.image}" alt="${product.name}">
                        <div class="favorite-card-body">
                            <h6 class="favorite-card-title">${product.name}</h6>
                            <div class="favorite-card-price">${formatPrice(product.price)}</div>
                            <a href="product-detail.html?id=${product.id}" class="btn btn-sm btn-primary mt-2 w-100">
                                <i class="fas fa-eye me-2"></i> Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

// Remove favorite
function removeFavorite(productId) {
    let favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    favorites = favorites.filter(p => p.id !== productId);
    localStorage.setItem('favorites', JSON.stringify(favorites));
    loadFavorites();
    showNotification('Đã xóa khỏi danh sách yêu thích!', 'success');
}

// Load addresses
function loadAddresses() {
    const user = JSON.parse(localStorage.getItem('currentUser'));
    let addresses = JSON.parse(localStorage.getItem('addresses') || '[]');
    addresses = addresses.filter(addr => addr.userId === user.id);
    
    const addressesList = document.getElementById('addressesList');
    
    if (addresses.length === 0) {
        addressesList.innerHTML = `
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chưa có địa chỉ nhận hàng</p>
                </div>
            </div>
        `;
        return;
    }
    
    addressesList.innerHTML = addresses.map(addr => `
        <div class="col-md-6">
            <div class="address-card ${addr.isDefault ? 'default' : ''}">
                ${addr.isDefault ? '<span class="address-badge">Mặc định</span>' : ''}
                <div class="address-name">${addr.name}</div>
                <div class="address-phone"><i class="fas fa-phone me-2"></i> ${addr.phone}</div>
                <div class="address-detail">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    ${addr.address}, ${addr.ward}, ${addr.district}, ${addr.city}
                </div>
                <div class="address-actions">
                    ${!addr.isDefault ? `
                        <button class="btn btn-sm btn-outline-primary" onclick="setDefaultAddress(${addr.id})">
                            <i class="fas fa-check me-2"></i> Đặt làm mặc định
                        </button>
                    ` : ''}
                    <button class="btn btn-sm btn-outline-secondary" onclick="editAddress(${addr.id})">
                        <i class="fas fa-edit me-2"></i> Sửa
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteAddress(${addr.id})">
                        <i class="fas fa-trash me-2"></i> Xóa
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Format price
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

// Show notification
function showNotification(message, type = 'info') {
    // Reuse the showAlert function from auth.js or main.js
    if (typeof showAlert === 'function') {
        showAlert(message, type);
    } else {
        alert(message);
    }
}

// Tab navigation
function initTabNavigation() {
    const tabLinks = document.querySelectorAll('.list-group-item[data-tab]');
    const tabContents = document.querySelectorAll('.tab-content[data-content]');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            const targetTab = link.getAttribute('data-tab');
            
            // Remove active class from all tabs
            tabLinks.forEach(l => l.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab
            link.classList.add('active');
            document.querySelector(`[data-content="${targetTab}"]`).classList.add('active');
        });
    });
    
    // Order status filter
    const statusFilters = document.querySelectorAll('.nav-pills .nav-link[data-status]');
    statusFilters.forEach(filter => {
        filter.addEventListener('click', (e) => {
            e.preventDefault();
            
            const status = filter.getAttribute('data-status');
            
            // Update active state
            statusFilters.forEach(f => f.classList.remove('active'));
            filter.classList.add('active');
            
            // Load orders with filter
            loadOrders(status);
        });
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadUserData();
    initTabNavigation();
    
    // Initialize demo orders if not exists
    const orders = JSON.parse(localStorage.getItem('orders') || '[]');
    if (orders.length === 0 && localStorage.getItem('currentUser')) {
        const user = JSON.parse(localStorage.getItem('currentUser'));
        const demoOrders = [
            {
                id: Date.now(),
                userId: user.id,
                items: [
                    {
                        id: 1,
                        name: 'iPhone 15 Pro Max',
                        image: 'https://images.unsplash.com/photo-1678652197831-2d180705cd2f?w=300',
                        price: 29990000,
                        quantity: 1
                    }
                ],
                total: 29990000,
                status: 'pending',
                createdAt: new Date().toISOString(),
                paymentMethod: 'COD',
                shippingAddress: {
                    name: user.fullname,
                    phone: user.phone,
                    address: '123 Đường ABC, Phường XYZ, Quận 1, TP.HCM'
                }
            }
        ];
        localStorage.setItem('orders', JSON.stringify(demoOrders));
        loadOrders();
    }
});
