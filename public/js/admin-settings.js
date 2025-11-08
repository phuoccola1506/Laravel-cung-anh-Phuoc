function previewSettingImage(event, key) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('preview-' + key);
    const img = previewContainer.querySelector('img');
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Loading spinner khi submit form
document.getElementById('settingsForm').addEventListener('submit', function (e) {
    const btn = this.querySelector('button[type="submit"]');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang lưu...';
    btn.disabled = true;
});

// ====== QUẢN LÝ DỮ LIỆU (localStorage demo) ======
function exportData() {
    const data = {
        products: localStorage.getItem('products'),
        orders: localStorage.getItem('orders'),
        users: localStorage.getItem('users')
    };
    const blob = new Blob([JSON.stringify(data, null, 2)], {
        type: 'application/json'
    });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'techshop-data-' + new Date().toISOString().split('T')[0] + '.json';
    link.click();
    alert('✅ Đã xuất dữ liệu thành công!');
}

function backupData() {
    const backup = {
        timestamp: new Date().toISOString(),
        data: {
            products: localStorage.getItem('products'),
            orders: localStorage.getItem('orders'),
            users: localStorage.getItem('users')
        }
    };
    localStorage.setItem('backup', JSON.stringify(backup));
    alert('💾 Đã sao lưu dữ liệu thành công!');
}

function confirmClearData() {
    if (confirm('⚠️ CẢNH BÁO: Hành động này sẽ xóa TẤT CẢ dữ liệu!\n\nBạn có chắc chắn muốn tiếp tục?')) {
        if (confirm('Xác nhận lần cuối: Bạn THỰC SỰ muốn xóa hết dữ liệu?')) {
            localStorage.clear();
            alert('🗑️ Đã xóa tất cả dữ liệu! Trang sẽ được tải lại.');
            window.location.reload();
        }
    }
}