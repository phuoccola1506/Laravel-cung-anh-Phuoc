
// Setup CSRF Token for AJAX requests
function setupCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        // Set default headers for all fetch requests
        window.csrfToken = token.getAttribute('content');
    }
}

// Add to Cart - AJAX Request to Laravel Backend
async function addToCart(productId, variantId, quantity = 1) {
    if (!window.csrfToken) {
        setupCSRFToken();
    }

    try {
        const response = await fetch(`/cart/add`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                variant_id: variantId,
                quantity: quantity
            })
        });

        // Kiểm tra nếu chưa đăng nhập (401 Unauthorized)
        if (response.status === 401) {
            showNotification('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!', 'error');
            setTimeout(() => {
                window.location.href = '/login';
            }, 1500);
            return;
        }

        const data = await response.json();

        if (response.ok && data.success) {
            showNotification(data.message || 'Đã thêm sản phẩm vào giỏ hàng!', 'success');
            // Hiển thị cảnh báo nếu số lượng đã được điều chỉnh
            if (data.warning) {
                setTimeout(() => {
                    showNotification(data.warning, 'warning');
                }, 500);
            }
            // Cập nhật cart count trong header (không reload)
            updateCartCountInHeader(data.cart_count);
        } else {
            showNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Không thể thêm vào giỏ hàng. Vui lòng thử lại!', 'error');
    }
}

// Remove from Cart - AJAX Request
async function removeFromCart(rowId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
        return;
    }

    try {
        const response = await fetch(`/cart/remove/${rowId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            showNotification(data.message || 'Đã xóa sản phẩm!', 'success');
            // Xóa dòng sản phẩm khỏi table
            removeCartItemFromUI(rowId);
            // Cập nhật cart count
            updateCartCountInHeader(data.cart_count);
            // Reload cart để cập nhật tổng tiền
            await refreshCartData();
        } else {
            showNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
        showNotification('Không thể xóa sản phẩm. Vui lòng thử lại!', 'error');
    }
}

// Update Cart Item Quantity - AJAX Request
async function updateCartItemQuantity(rowId, quantity) {
    quantity = parseInt(quantity);

    if (quantity < 1) {
        removeFromCart(rowId);
        return;
    }

    try {
        const response = await fetch(`/cart/update/${rowId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                quantity: quantity
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Cập nhật số lượng trong input với giá trị đã được điều chỉnh
            const row = document.querySelector(`tr[data-row-id="${rowId}"]`);
            if (row) {
                const qtyInput = row.querySelector('input[type="number"]');
                if (qtyInput && data.adjusted_quantity !== undefined) {
                    qtyInput.value = data.adjusted_quantity;
                }
                
                // Cập nhật subtotal của dòng này nếu backend trả về
                if (data.item_subtotal) {
                    const subtotalCell = row.querySelector('.cart-total');
                    if (subtotalCell) {
                        subtotalCell.textContent = formatCurrency(data.item_subtotal);
                    }
                }
            }
            
            // Cập nhật tổng tiền và cart count
            if (data.cart_total !== undefined) {
                const shipping = 50000;
                document.getElementById('cartSubtotal').textContent = formatCurrency(data.cart_total);
                document.getElementById('cartTotal').textContent = formatCurrency(data.cart_total + shipping);
            }
            
            updateCartCountInHeader(data.cart_count);
            showNotification(data.message || 'Đã cập nhật!', 'success');
            
            // Hiển thị cảnh báo nếu số lượng đã được điều chỉnh
            if (data.warning) {
                setTimeout(() => {
                    showNotification(data.warning, 'warning');
                }, 500);
            }
        } else {
            showNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    } catch (error) {
        console.error('Error updating cart:', error);
        showNotification('Không thể cập nhật số lượng. Vui lòng thử lại!', 'error');
    }
}

// Clear entire cart
async function clearCart() {
    if (!confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) {
        return;
    }

    try {
        const response = await fetch('/cart/clear', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok) {
            showNotification(data.message || 'Đã xóa giỏ hàng!', 'success');
            window.location.reload();
        } else {
            showNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    } catch (error) {
        console.error('Error clearing cart:', error);
        showNotification('Không thể xóa giỏ hàng. Vui lòng thử lại!', 'error');
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    // Icon theo loại thông báo
    let icon = 'info-circle';
    if (type === 'success') icon = 'check-circle';
    else if (type === 'error') icon = 'exclamation-circle';
    else if (type === 'warning') icon = 'exclamation-triangle';
    
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Add styles if not exist
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 100px;
                right: 20px;
                padding: 15px 25px;
                background: white;
                border-radius: 8px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.2);
                display: flex;
                align-items: center;
                gap: 10px;
                z-index: 9999;
                animation: slideIn 0.3s ease;
            }
            
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            .notification-success {
                border-left: 4px solid #00a650;
            }
            
            .notification-success i {
                color: #00a650;
                font-size: 1.5rem;
            }
            
            .notification-error {
                border-left: 4px solid #d70018;
            }
            
            .notification-error i {
                color: #d70018;
                font-size: 1.5rem;
            }
        `;
        document.head.appendChild(style);
    }
    
    setTimeout(() => {
        notification.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Update cart count in header
function updateCartCountInHeader(count) {
    const cartCounts = document.querySelectorAll('.cart-count');
    cartCounts.forEach(el => {
        el.textContent = count;
    });
}

// Remove cart item from UI
function removeCartItemFromUI(rowId) {
    const row = document.querySelector(`tr[data-row-id="${rowId}"]`);
    if (row) {
        row.remove();
    }
}

// Refresh cart data without reload
async function refreshCartData() {
    try {
        const response = await fetch('/cart', {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // data.total là integer từ backend
            const subtotal = parseInt(data.total) || 0;
            const shipping = 50000;
            const total = subtotal + shipping;
            
            // Update UI
            document.getElementById('cartSubtotal').textContent = formatCurrency(subtotal);
            document.getElementById('cartTotal').textContent = formatCurrency(total);
            
            // Update count
            updateCartCountInHeader(data.count);
        }
    } catch (error) {
        console.error('Error refreshing cart:', error);
    }
}

// Proceed to Checkout
function proceedToCheckout() {
    // Kiểm tra xem có sản phẩm trong giỏ hàng không
    const cartTableBody = document.getElementById('cartTableBody');
    if (!cartTableBody || cartTableBody.children.length === 0) {
        showNotification('Giỏ hàng của bạn đang trống!', 'error');
        return;
    }
    
    // Chuyển đến trang checkout
    window.location.href = '/checkout';
}

// Apply Coupon
async function applyCoupon() {
    const couponCode = document.getElementById('couponCode').value.trim().toUpperCase();
    if (!couponCode) {
        showNotification('Vui lòng nhập mã giảm giá!', 'error');
        return;
    }
    
    try {
        const response = await fetch('/cart/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code: couponCode })
        });

        const data = await response.json();

        if (data.success) {
            showNotification(data.message, 'success');
            
            // Cập nhật UI
            updateCartSummary(data.calculation);
            displayAppliedCoupons(data.applied_coupons);
            
            // Clear input
            document.getElementById('couponCode').value = '';
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error applying coupon:', error);
        showNotification('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
    }
}

// Remove Coupon
async function removeCoupon(code) {
    try {
        const response = await fetch('/cart/remove-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code: code })
        });

        const data = await response.json();

        if (data.success) {
            showNotification(data.message, 'success');
            
            // Cập nhật UI
            updateCartSummary(data.calculation);
            displayAppliedCoupons(data.applied_coupons);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error removing coupon:', error);
        showNotification('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
    }
}

// Update Cart Summary
function updateCartSummary(calculation) {
    document.getElementById('cartSubtotal').textContent = formatCurrency(calculation.subtotal);
    document.getElementById('cartShipping').textContent = formatCurrency(calculation.shipping);
    document.getElementById('cartTotal').textContent = formatCurrency(calculation.total);
    
    const discountRow = document.getElementById('discountRow');
    if (calculation.discount > 0) {
        document.getElementById('cartDiscount').textContent = '-' + formatCurrency(calculation.discount);
        discountRow.style.display = 'flex';
    } else {
        discountRow.style.display = 'none';
    }
}

// Display Applied Coupons
function displayAppliedCoupons(coupons) {
    const container = document.getElementById('appliedCouponsContainer');
    if (!container) return;
    
    if (coupons.length === 0) {
        container.innerHTML = '';
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'block';
    container.innerHTML = '<h4 style="margin-bottom: 10px; font-size: 0.95rem;">Mã đã áp dụng:</h4>';
    
    coupons.forEach(coupon => {
        let desc = '';
        if (coupon.type === 'percentage') {
            desc = `Giảm ${coupon.percentage}%`;
        } else if (coupon.type === 'amount') {
            desc = `Giảm ${formatCurrency(coupon.amount)}`;
        } else if (coupon.type === 'shipping') {
            desc = 'Miễn phí vận chuyển';
        }
        
        const couponEl = document.createElement('div');
        couponEl.className = 'applied-coupon-item';
        couponEl.innerHTML = `
            <div>
                <strong>${coupon.code}</strong>
                <small>${desc}</small>
            </div>
            <button onclick="removeCoupon('${coupon.code}')" class="btn-remove-coupon">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(couponEl);
    });
}

// Select Coupon from list
function selectCoupon(code) {
    document.getElementById('couponCode').value = code;
    applyCoupon();
}

// Format currency - Làm tròn đến hàng chục nghìn
function formatCurrency(amount) {
    // Làm tròn đến hàng chục nghìn (10,000)
    const rounded = Math.round(amount / 10000) * 10000;
    
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(rounded);
}

// Initialize when DOM loaded
document.addEventListener('DOMContentLoaded', function() {
    setupCSRFToken();
});
