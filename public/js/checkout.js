// Checkout JavaScript

// Load order summary
function loadOrderSummary() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    if (cart.length === 0) {
        window.location.href = 'cart.html';
        return;
    }
    
    const orderItems = document.getElementById('orderItems');
    let subtotal = 0;
    
    orderItems.innerHTML = cart.map(item => {
        subtotal += item.price * item.quantity;
        return `
            <div class="order-item-checkout">
                <img src="${item.image}" alt="${item.name}" class="order-item-image">
                <div class="order-item-info">
                    <div class="order-item-name">${item.name}</div>
                    <div class="order-item-quantity">SL: ${item.quantity}</div>
                </div>
                <div class="order-item-price">${formatPrice(item.price * item.quantity)}</div>
            </div>
        `;
    }).join('');
    
    // Calculate shipping fee
    const shippingFee = subtotal >= 5000000 ? 0 : 30000;
    
    // Get discount from coupon
    const coupon = JSON.parse(localStorage.getItem('appliedCoupon') || 'null');
    let discount = 0;
    if (coupon) {
        if (coupon.type === 'percent') {
            discount = (subtotal * coupon.value) / 100;
            if (coupon.maxDiscount) {
                discount = Math.min(discount, coupon.maxDiscount);
            }
        } else {
            discount = coupon.value;
        }
    }
    
    const total = subtotal + shippingFee - discount;
    
    // Update UI
    document.getElementById('subtotal').textContent = formatPrice(subtotal);
    document.getElementById('shippingFee').textContent = shippingFee === 0 ? 'Miễn phí' : formatPrice(shippingFee);
    document.getElementById('discount').textContent = '-' + formatPrice(discount);
    document.getElementById('total').textContent = formatPrice(total);
}

// Load user info
function loadUserInfo() {
    const user = JSON.parse(localStorage.getItem('currentUser'));
    if (user) {
        document.getElementById('fullname').value = user.fullname || '';
        document.getElementById('phone').value = user.phone || '';
        document.getElementById('email').value = user.email || '';
    }
}

// Handle place order
function handlePlaceOrder() {
    const form = document.getElementById('shippingForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    if (cart.length === 0) {
        showNotification('Giỏ hàng trống!', 'error');
        return;
    }
    
    // Get form data
    const fullname = document.getElementById('fullname').value;
    const phone = document.getElementById('phone').value;
    const email = document.getElementById('email').value;
    const city = document.getElementById('city').options[document.getElementById('city').selectedIndex].text;
    const district = document.getElementById('district').options[document.getElementById('district').selectedIndex].text;
    const ward = document.getElementById('ward').options[document.getElementById('ward').selectedIndex].text;
    const address = document.getElementById('address').value;
    const note = document.getElementById('note').value;
    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
    
    // Validate phone
    if (!/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/.test(phone)) {
        showNotification('Số điện thoại không hợp lệ!', 'error');
        return;
    }
    
    // Calculate totals
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += item.price * item.quantity;
    });
    
    const shippingFee = subtotal >= 5000000 ? 0 : 30000;
    const coupon = JSON.parse(localStorage.getItem('appliedCoupon') || 'null');
    let discount = 0;
    
    if (coupon) {
        if (coupon.type === 'percent') {
            discount = (subtotal * coupon.value) / 100;
            if (coupon.maxDiscount) {
                discount = Math.min(discount, coupon.maxDiscount);
            }
        } else {
            discount = coupon.value;
        }
    }
    
    const total = subtotal + shippingFee - discount;
    
    // Create order
    const order = {
        id: Date.now(),
        userId: JSON.parse(localStorage.getItem('currentUser'))?.id || 0,
        items: cart.map(item => ({
            id: item.id,
            name: item.name,
            image: item.image,
            price: item.price,
            quantity: item.quantity
        })),
        shippingInfo: {
            fullname,
            phone,
            email,
            city,
            district,
            ward,
            address: `${address}, ${ward}, ${district}, ${city}`,
            note
        },
        paymentMethod,
        subtotal,
        shippingFee,
        discount,
        total,
        status: 'pending',
        createdAt: new Date().toISOString()
    };
    
    // Save order
    const orders = JSON.parse(localStorage.getItem('orders') || '[]');
    orders.push(order);
    localStorage.setItem('orders', JSON.stringify(orders));
    
    // Clear cart and coupon
    localStorage.removeItem('cart');
    localStorage.removeItem('appliedCoupon');
    
    // Update cart count
    if (typeof updateCartCount === 'function') {
        updateCartCount();
    }
    
    // Handle payment method
    if (paymentMethod === 'COD') {
        showNotification('Đặt hàng thành công! Bạn sẽ thanh toán khi nhận hàng.', 'success');
        setTimeout(() => {
            window.location.href = `order-tracking.html?id=${order.id}`;
        }, 2000);
    } else {
        showNotification('Đang chuyển đến trang thanh toán...', 'info');
        setTimeout(() => {
            // In production, redirect to payment gateway
            // For demo, just mark as paid and redirect
            const orderIndex = orders.findIndex(o => o.id === order.id);
            if (orderIndex !== -1) {
                orders[orderIndex].paymentStatus = 'paid';
                orders[orderIndex].status = 'confirmed';
                localStorage.setItem('orders', JSON.stringify(orders));
            }
            window.location.href = `order-tracking.html?id=${order.id}`;
        }, 1500);
    }
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
    if (typeof showAlert === 'function') {
        showAlert(message, type);
    } else {
        alert(message);
    }
}

// Location data (simplified)
const locations = {
    hanoi: {
        name: 'Hà Nội',
        districts: {
            'ba-dinh': { name: 'Ba Đình', wards: ['Phúc Xá', 'Trúc Bạch', 'Vĩnh Phúc'] },
            'hoan-kiem': { name: 'Hoàn Kiếm', wards: ['Hàng Bạc', 'Hàng Bài', 'Hàng Bồ'] },
            'cau-giay': { name: 'Cầu Giấy', wards: ['Dịch Vọng', 'Mai Dịch', 'Nghĩa Đô'] }
        }
    },
    hcm: {
        name: 'TP. Hồ Chí Minh',
        districts: {
            'quan-1': { name: 'Quận 1', wards: ['Bến Nghé', 'Bến Thành', 'Cầu Kho'] },
            'quan-3': { name: 'Quận 3', wards: ['Võ Thị Sáu', 'Phường 1', 'Phường 2'] },
            'binh-thanh': { name: 'Bình Thạnh', wards: ['Phường 1', 'Phường 2', 'Phường 3'] }
        }
    },
    danang: {
        name: 'Đà Nẵng',
        districts: {
            'hai-chau': { name: 'Hải Châu', wards: ['Thanh Bình', 'Thạch Thang', 'Hải Châu 1'] },
            'thanh-khe': { name: 'Thanh Khê', wards: ['An Khê', 'Chính Gián', 'Tân Chính'] }
        }
    }
};

// Initialize location selectors
function initLocationSelectors() {
    const citySelect = document.getElementById('city');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    
    citySelect.addEventListener('change', function() {
        const cityKey = this.value;
        districtSelect.innerHTML = '<option value="">Chọn</option>';
        wardSelect.innerHTML = '<option value="">Chọn</option>';
        
        if (cityKey && locations[cityKey]) {
            Object.keys(locations[cityKey].districts).forEach(districtKey => {
                const district = locations[cityKey].districts[districtKey];
                const option = new Option(district.name, districtKey);
                districtSelect.add(option);
            });
        }
    });
    
    districtSelect.addEventListener('change', function() {
        const cityKey = citySelect.value;
        const districtKey = this.value;
        wardSelect.innerHTML = '<option value="">Chọn</option>';
        
        if (cityKey && districtKey && locations[cityKey]?.districts[districtKey]) {
            const wards = locations[cityKey].districts[districtKey].wards;
            wards.forEach(ward => {
                const option = new Option(ward, ward.toLowerCase().replace(/\s+/g, '-'));
                wardSelect.add(option);
            });
        }
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadOrderSummary();
    loadUserInfo();
    initLocationSelectors();
});
