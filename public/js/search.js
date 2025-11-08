/* ============================================================================
   Search Page JavaScript
   Chức năng: Sắp xếp, lọc, thay đổi view
   ============================================================================ */

// Lưu trữ dữ liệu sản phẩm gốc
let originalProducts = [];
let currentView = 'grid';

// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
});

/**
 * Khởi tạo chức năng search
 */
function initializeSearch() {
    // Lưu dữ liệu sản phẩm gốc
    saveOriginalProducts();
    
    // Khôi phục view preference từ localStorage
    const savedView = localStorage.getItem('searchView');
    if (savedView) {
        currentView = savedView;
        applyView(currentView);
    }
    
    // Khôi phục sort preference
    const savedSort = localStorage.getItem('searchSort');
    if (savedSort) {
        document.getElementById('sortSelect').value = savedSort;
        sortProducts(savedSort);
    }
}

/**
 * Lưu dữ liệu sản phẩm gốc để có thể sắp xếp lại
 */
function saveOriginalProducts() {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    
    const productCards = grid.querySelectorAll('.col-lg-3');
    originalProducts = Array.from(productCards).map(card => {
        const product = {
            element: card.cloneNode(true),
            name: card.querySelector('.product-name')?.textContent.trim() || '',
            price: extractPrice(card),
            rating: extractRating(card),
            discount: extractDiscount(card)
        };
        return product;
    });
}

/**
 * Trích xuất giá từ card sản phẩm
 */
function extractPrice(card) {
    const priceText = card.querySelector('.product-price')?.textContent.trim() || '0';
    // Loại bỏ ký tự không phải số
    const price = priceText.replace(/[^\d]/g, '');
    return parseInt(price) || 0;
}

/**
 * Trích xuất rating từ card sản phẩm
 */
function extractRating(card) {
    const stars = card.querySelectorAll('.product-rating .fas.fa-star');
    return stars ? stars.length : 0;
}

/**
 * Trích xuất discount từ card sản phẩm
 */
function extractDiscount(card) {
    const discountBadge = card.querySelector('.discount-badge, .product-discount');
    if (!discountBadge) return 0;
    
    const discountText = discountBadge.textContent.trim();
    const discount = discountText.replace(/[^\d]/g, '');
    return parseInt(discount) || 0;
}

/**
 * Sắp xếp sản phẩm
 */
function sortProducts(value) {
    if (originalProducts.length === 0) {
        console.warn('Không có sản phẩm để sắp xếp');
        return;
    }
    
    // Lưu lựa chọn sort vào localStorage
    localStorage.setItem('searchSort', value);
    
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    
    let sortedProducts = [...originalProducts];
    
    switch(value) {
        case 'name-asc':
            sortedProducts.sort((a, b) => a.name.localeCompare(b.name, 'vi'));
            break;
            
        case 'name-desc':
            sortedProducts.sort((a, b) => b.name.localeCompare(a.name, 'vi'));
            break;
            
        case 'price-asc':
            sortedProducts.sort((a, b) => a.price - b.price);
            break;
            
        case 'price-desc':
            sortedProducts.sort((a, b) => b.price - a.price);
            break;
            
        case 'rating':
            sortedProducts.sort((a, b) => b.rating - a.rating);
            break;
            
        case 'discount':
            sortedProducts.sort((a, b) => b.discount - a.discount);
            break;
            
        case 'newest':
            // Giữ nguyên thứ tự ban đầu (mới nhất)
            break;
            
        case 'default':
        default:
            // Giữ nguyên thứ tự gốc
            break;
    }
    
    // Xóa grid hiện tại
    grid.innerHTML = '';
    
    // Thêm các sản phẩm đã sắp xếp
    sortedProducts.forEach(product => {
        grid.appendChild(product.element.cloneNode(true));
    });
    
    // Hiển thị thông báo
    showNotification(`Đã sắp xếp theo: ${getSortLabel(value)}`, 'info');
}

/**
 * Lấy label của sort option
 */
function getSortLabel(value) {
    const labels = {
        'default': 'Mặc định',
        'name-asc': 'Tên A-Z',
        'name-desc': 'Tên Z-A',
        'price-asc': 'Giá thấp đến cao',
        'price-desc': 'Giá cao đến thấp',
        'rating': 'Đánh giá cao nhất',
        'discount': 'Giảm giá nhiều nhất',
        'newest': 'Mới nhất'
    };
    return labels[value] || 'Mặc định';
}

/**
 * Thay đổi view (grid/list)
 */
function changeView(view) {
    const grid = document.getElementById('productsGrid');
    const buttons = document.querySelectorAll('.view-btn');
    
    if (!grid) return;
    
    // Cập nhật active button
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.closest('.view-btn').classList.add('active');
    
    // Lưu preference
    localStorage.setItem('searchView', view);
    currentView = view;
    
    applyView(view);
}

/**
 * Áp dụng view style
 */
function applyView(view) {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    
    const buttons = document.querySelectorAll('.view-btn');
    buttons.forEach(btn => {
        if (btn.dataset.view === view) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    if (view === 'list') {
        // List view
        grid.classList.remove('row', 'g-4');
        grid.classList.add('list-view');
        
        // Thay đổi class của các item
        const items = grid.querySelectorAll('.col-lg-3, .col-md-4, .col-sm-6');
        items.forEach(item => {
            item.className = 'list-item';
        });
    } else {
        // Grid view
        grid.classList.remove('list-view');
        grid.classList.add('row', 'g-4');
        
        // Khôi phục class của các item
        const items = grid.querySelectorAll('.list-item');
        items.forEach(item => {
            item.className = 'col-lg-3 col-md-4 col-sm-6';
        });
    }
}

/**
 * Lọc sản phẩm theo giá
 */
function filterByPrice(minPrice, maxPrice) {
    if (originalProducts.length === 0) return;
    
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    
    const filteredProducts = originalProducts.filter(product => {
        const price = product.price;
        return price >= minPrice && price <= maxPrice;
    });
    
    // Xóa grid hiện tại
    grid.innerHTML = '';
    
    if (filteredProducts.length === 0) {
        grid.innerHTML = '<div class="col-12 text-center py-5"><p>Không tìm thấy sản phẩm trong khoảng giá này</p></div>';
        return;
    }
    
    // Thêm các sản phẩm đã lọc
    filteredProducts.forEach(product => {
        grid.appendChild(product.element.cloneNode(true));
    });
    
    // Cập nhật số lượng kết quả
    updateResultCount(filteredProducts.length);
}

/**
 * Lọc sản phẩm theo rating
 */
function filterByRating(minRating) {
    if (originalProducts.length === 0) return;
    
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    
    const filteredProducts = originalProducts.filter(product => {
        return product.rating >= minRating;
    });
    
    // Xóa grid hiện tại
    grid.innerHTML = '';
    
    if (filteredProducts.length === 0) {
        grid.innerHTML = '<div class="col-12 text-center py-5"><p>Không tìm thấy sản phẩm với đánh giá này</p></div>';
        return;
    }
    
    // Thêm các sản phẩm đã lọc
    filteredProducts.forEach(product => {
        grid.appendChild(product.element.cloneNode(true));
    });
    
    // Cập nhật số lượng kết quả
    updateResultCount(filteredProducts.length);
}

/**
 * Reset tất cả filter
 */
function resetFilters() {
    if (originalProducts.length === 0) return;
    
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    
    // Khôi phục tất cả sản phẩm
    grid.innerHTML = '';
    originalProducts.forEach(product => {
        grid.appendChild(product.element.cloneNode(true));
    });
    
    // Reset sort select
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.value = 'default';
    }
    
    // Xóa localStorage
    localStorage.removeItem('searchSort');
    
    // Cập nhật số lượng kết quả
    updateResultCount(originalProducts.length);
    
    showNotification('Đã xóa tất cả bộ lọc', 'success');
}

/**
 * Cập nhật số lượng kết quả hiển thị
 */
function updateResultCount(count) {
    const resultsCount = document.querySelector('.results-count');
    if (resultsCount) {
        const total = originalProducts.length;
        resultsCount.innerHTML = `Hiển thị <strong>${count}</strong> trong số <strong>${total}</strong> sản phẩm`;
    }
}

/**
 * Hiển thị notification
 */
function showNotification(message, type = 'info') {
    // Kiểm tra xem đã có notification chưa
    const existingNotification = document.querySelector('.search-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = `search-notification search-notification-${type}`;
    
    let icon = 'info-circle';
    if (type === 'success') icon = 'check-circle';
    else if (type === 'error') icon = 'exclamation-circle';
    else if (type === 'warning') icon = 'exclamation-triangle';
    
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Tự động ẩn sau 3 giây
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Scroll to top smooth
 */
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
