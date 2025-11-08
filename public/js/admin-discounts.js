// Toggle value input based on type
function toggleValueInput() {
    const type = document.getElementById('discountType').value;
    const label = document.getElementById('valueLabel');
    const input = document.getElementById('discountValue');
    
    if (type === 'percentage') {
        label.innerHTML = 'Giá trị giảm (%) <span class="text-danger">*</span>';
        input.placeholder = 'VD: 10 (cho 10%)';
        input.max = '100';
    } else {
        label.innerHTML = 'Số tiền giảm (₫) <span class="text-danger">*</span>';
        input.placeholder = 'VD: 50000';
        input.removeAttribute('max');
    }
}

// Open modal for adding new discount
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Thêm Mã Giảm Giá';
    document.getElementById('discountForm').reset();
    document.getElementById('discountId').value = '';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('discountStatus').checked = true;
    document.getElementById('passwordLabel').innerHTML = 'Mật khẩu <span class="text-danger">*</span>';
    document.getElementById('userPassword').required = true;
    document.getElementById('passwordHint').style.display = 'none';
    
    const modal = new bootstrap.Modal(document.getElementById('addDiscountModal'));
    modal.show();
}

// Edit discount
async function editDiscount(id) {
    try {
        const response = await fetch(`/admin/discounts/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const discount = data.discount;
            
            document.getElementById('modalTitle').textContent = 'Sửa Mã Giảm Giá';
            document.getElementById('discountId').value = discount.id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('discountCode').value = discount.code;
            document.getElementById('discountType').value = discount.type;
            document.getElementById('discountValue').value = discount.type === 'percentage' ? discount.percentage : discount.amount;
            document.getElementById('usageLimit').value = discount.usage_limit || '';
            
            // Format dates for datetime-local input
            if (discount.start_date) {
                document.getElementById('startDate').value = formatDateForInput(discount.start_date);
            }
            if (discount.end_date) {
                document.getElementById('endDate').value = formatDateForInput(discount.end_date);
            }
            
            document.getElementById('minPurchase').value = discount.min_purchase || '';
            document.getElementById('maxDiscount').value = discount.max_discount || '';
            document.getElementById('discountDescription').value = discount.description || '';
            document.getElementById('discountStatus').checked = discount.active;
            
            toggleValueInput();
            
            const modal = new bootstrap.Modal(document.getElementById('addDiscountModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi tải thông tin mã giảm giá!');
    }
}

// Format date for datetime-local input
function formatDateForInput(dateString) {
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

// Save discount (add or update)
async function saveDiscount() {
    const form = document.getElementById('discountForm');
    const formData = new FormData(form);
    const discountId = document.getElementById('discountId').value;
    const method = document.getElementById('formMethod').value;
    
    let url = '/admin/discounts';
    if (method === 'PUT') {
        url = `/admin/discounts/${discountId}`;
        formData.append('_method', 'PUT');
    }
    
    // Handle checkbox
    if (!formData.has('active')) {
        formData.append('active', '0');
    } else {
        formData.set('active', '1');
    }
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message || 'Lưu mã giảm giá thành công!');
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
        alert('Có lỗi xảy ra khi lưu mã giảm giá!');
    }
}

// Delete discount
async function deleteDiscount(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        
        const response = await fetch(`/admin/discounts/${id}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message || 'Xóa mã giảm giá thành công!');
            window.location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra khi xóa mã giảm giá!');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xóa mã giảm giá!');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit filter form on change
    const typeFilter = document.getElementById('typeFilter');
    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
});
