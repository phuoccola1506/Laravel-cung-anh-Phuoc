// Admin Dashboard JavaScript

// Toggle sidebar on mobile
function toggleSidebar() {
    const sidebar = document.querySelector('.admin-sidebar');
    sidebar.classList.toggle('active');
}

// Logout function - Not needed anymore, using Laravel form
// function logout() {
//     if (confirm('Bạn có chắc muốn đăng xuất?')) {
//         document.getElementById('logout-form').submit();
//     }
// }

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    initRevenueChart();
    initCustomerChart();
});

// Revenue Chart
function initRevenueChart() {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            datasets: [{
                label: 'Doanh thu (triệu đồng)',
                data: [1200, 1500, 1800, 1650, 2100, 2400, 2200, 2600, 2800, 2500, 2900, 2540],
                borderColor: '#d70018',
                backgroundColor: 'rgba(215, 0, 24, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1f1f1f',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' triệu đồng';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + 'M';
                        }
                    },
                    grid: {
                        borderDash: [5, 5]
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Customer Chart
function initCustomerChart() {
    const ctx = document.getElementById('customerChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Khách hàng mới', 'Khách hàng thân thiết', 'Khách hàng VIP'],
            datasets: [{
                data: [45, 35, 20],
                backgroundColor: [
                    '#667eea',
                    '#00a650',
                    '#ffc107'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1f1f1f',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// Check admin authentication
function checkAdminAuth() {
    // Tạm thời tắt kiểm tra đăng nhập
    // const adminUser = localStorage.getItem('adminUser');
    // if (!adminUser && !window.location.pathname.includes('login.html')) {
    //     window.location.href = '../pages/login.html';
    // }
}

// Initialize
// checkAdminAuth(); // Tạm thời tắt
