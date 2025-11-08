// View order details
async function viewOrder(id) {
    try {
        const response = await fetch(`/admin/orders/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const order = data.order;
            
            // Status labels
            const statusLabels = {
                'pending': 'Chờ xử lý',
                'processing': 'Đang xử lý',
                'shipping': 'Đang giao',
                'delivered': 'Đã giao',
                'cancelled': 'Đã hủy'
            };
            
            const paymentLabels = {
                'pending': 'Chờ thanh toán',
                'paid': 'Đã thanh toán',
                'failed': 'Thất bại'
            };
            
            // Build order items HTML
            let itemsHtml = '';
            if (order.items && order.items.length > 0) {
                order.items.forEach(item => {
                    itemsHtml += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    ${item.product.image ? `<img src="/storage/${item.product.image}" alt="${item.product.name}" class="me-2" width="50" height="50" style="object-fit: cover;">` : ''}
                                    <div>
                                        <strong>${item.product.name}</strong><br>
                                        ${item.variant ? `<small class="text-muted">${item.variant.sku || ''}</small>` : ''}
                                    </div>
                                </div>
                            </td>
                            <td>${number_format(item.price)}₫</td>
                            <td>${item.quantity}</td>
                            <td><strong>${number_format(item.price * item.quantity)}₫</strong></td>
                        </tr>
                    `;
                });
            }
            
            const html = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Mã đơn hàng</h6>
                        <p class="h5 text-primary">#${order.order_code}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Ngày đặt</h6>
                        <p>${formatDate(order.created_at)}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Khách hàng</h6>
                        <p>
                            <strong>${order.user.name}</strong><br>
                            ${order.user.email}<br>
                            ${order.user.phone || ''}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Địa chỉ giao hàng</h6>
                        <p>${order.shipping_address || '-'}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Trạng thái đơn hàng</h6>
                        <p><span class="badge bg-info">${statusLabels[order.status] || order.status}</span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Trạng thái thanh toán</h6>
                        <p><span class="badge bg-success">${paymentLabels[order.payment_status] || order.payment_status}</span></p>
                    </div>
                </div>
                
                <hr>
                
                <h6 class="mb-3">Sản phẩm</h6>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th width="150">Đơn giá</th>
                                <th width="100">Số lượng</th>
                                <th width="150">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                <td><strong class="text-danger h5">${number_format(order.total)}₫</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                ${order.notes ? `
                    <hr>
                    <h6>Ghi chú</h6>
                    <p class="text-muted">${order.notes}</p>
                ` : ''}
            `;
            
            document.getElementById('orderDetailContent').innerHTML = html;
            
            const modal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi tải thông tin đơn hàng!');
    }
}

// Edit order
async function editOrder(id) {
    try {
        const response = await fetch(`/admin/orders/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const order = data.order;
            
            document.getElementById('orderId').value = order.id;
            document.getElementById('orderStatus').value = order.status;
            document.getElementById('paymentStatus').value = order.payment_status;
            document.getElementById('shippingAddress').value = order.shipping_address || '';
            document.getElementById('orderNotes').value = order.notes || '';
            
            const modal = new bootstrap.Modal(document.getElementById('editOrderModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi tải thông tin đơn hàng!');
    }
}

// Update order
async function updateOrder() {
    const form = document.getElementById('orderForm');
    const formData = new FormData(form);
    const orderId = document.getElementById('orderId').value;
    
    try {
        const response = await fetch(`/admin/orders/${orderId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message || 'Cập nhật đơn hàng thành công!');
            window.location.reload();
        } else {
            // Show validation errors
            if (data.errors) {
                let errorMessage = 'Vui lòng kiểm tra lại:\n';
                Object.values(data.errors).forEach(errors => {
                    errors.forEach(error => {
                        errorMessage += '- ' + error + '\n';
                    });
                });
                alert(errorMessage);
            } else {
                alert(data.message || 'Có lỗi xảy ra!');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật đơn hàng!');
    }
}

// Cancel order
async function cancelOrder(id) {
    if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        
        const response = await fetch(`/admin/orders/${id}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message || 'Hủy đơn hàng thành công!');
            window.location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra khi hủy đơn hàng!');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi hủy đơn hàng!');
    }
}

// Quick update status
async function updateStatus(orderId, newStatus) {
    try {
        const formData = new FormData();
        formData.append('status', newStatus);
        formData.append('_method', 'PUT');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        
        const response = await fetch(`/admin/orders/${orderId}/status`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message || 'Cập nhật trạng thái thành công!');
            window.location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra!');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật trạng thái!');
    }
}

// Helper functions
function number_format(number) {
    return new Intl.NumberFormat('vi-VN').format(number);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${day}/${month}/${year} ${hours}:${minutes}`;
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit filter form on change
    const statusFilter = document.getElementById('statusFilter');
    const paymentStatusFilter = document.getElementById('paymentStatusFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
    
    if (paymentStatusFilter) {
        paymentStatusFilter.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
});
