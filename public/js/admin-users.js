// Open modal for adding new user
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Thêm Người Dùng';
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('userStatus').checked = true;
    document.getElementById('passwordLabel').innerHTML = 'Mật khẩu <span class="text-danger">*</span>';
    document.getElementById('userPassword').required = true;
    document.getElementById('passwordHint').style.display = 'none';
    
    const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
    modal.show();
}

// Edit user
async function editUser(id) {
    try {
        const response = await fetch(`/admin/users/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const user = data.user;
            
            document.getElementById('modalTitle').textContent = 'Sửa Người Dùng';
            document.getElementById('userId').value = user.id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('userName').value = user.name;
            document.getElementById('userEmail').value = user.email;
            document.getElementById('userPhone').value = user.phone || '';
            document.getElementById('userRole').value = user.role;
            document.getElementById('userAddress').value = user.address || '';
            document.getElementById('userStatus').checked = user.active;
            
            // Password is optional when editing
            document.getElementById('passwordLabel').innerHTML = 'Mật khẩu mới';
            document.getElementById('userPassword').required = false;
            document.getElementById('userPassword').value = '';
            document.getElementById('userPasswordConfirm').value = '';
            document.getElementById('passwordHint').style.display = 'block';
            
            const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi tải thông tin người dùng!');
    }
}

// Save user (add or update)
async function saveUser() {
    const form = document.getElementById('userForm');
    const formData = new FormData(form);
    const userId = document.getElementById('userId').value;
    const method = document.getElementById('formMethod').value;
    
    // Validate password confirmation
    const password = document.getElementById('userPassword').value;
    const passwordConfirm = document.getElementById('userPasswordConfirm').value;
    
    if (password && password !== passwordConfirm) {
        alert('Mật khẩu xác nhận không khớp!');
        return;
    }
    
    let url = '/admin/users';
    if (method === 'PUT') {
        url = `/admin/users/${userId}`;
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
            alert(data.message || 'Lưu người dùng thành công!');
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
        alert('Có lỗi xảy ra khi lưu người dùng!');
    }
}

// Delete user
async function deleteUser(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        
        const response = await fetch(`/admin/users/${id}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message || 'Xóa người dùng thành công!');
            window.location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra khi xóa người dùng!');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xóa người dùng!');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit filter form on change
    const roleFilter = document.getElementById('roleFilter');
    if (roleFilter) {
        roleFilter.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
});
