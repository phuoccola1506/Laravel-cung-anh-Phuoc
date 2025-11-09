@extends('layouts.admin')

@section('title', 'Báo Cáo & Thống Kê')

@section('content')
    <div class="container-fluid">
        <h1 class="admin-title mb-4">Báo Cáo & Thống Kê</h1>

        <!-- Time Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select class="form-select" id="periodFilter">
                            <option value="7days">7 ngày qua</option>
                            <option value="30days" selected>30 ngày qua</option>
                            <option value="90days">90 ngày qua</option>
                            <option value="year">Năm nay</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary bg-opacity-10 border-primary">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Tổng doanh thu</h6>
                        <h3 class="mb-0 text-primary">150.5M</h3>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> +15.3% so với tháng trước</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success bg-opacity-10 border-success">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Đơn hàng</h6>
                        <h3 class="mb-0 text-success">1,234</h3>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> +8.7%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning bg-opacity-10 border-warning">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Khách hàng mới</h6>
                        <h3 class="mb-0 text-warning">87</h3>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> +12.4%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info bg-opacity-10 border-info">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Giá trị TB đơn hàng</h6>
                        <h3 class="mb-0 text-info">122K</h3>
                        <small class="text-danger"><i class="fas fa-arrow-down"></i> -2.1%</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Doanh Thu Theo Thời Gian</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueTimeChart" height="80"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Trạng Thái Đơn Hàng</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Sản Phẩm Bán Chạy</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="topProductsChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Doanh Thu Theo Danh Mục</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryRevenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products Table -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Top 10 Sản Phẩm Bán Chạy</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Hạng</th>
                                <th>Sản phẩm</th>
                                <th>Đã bán</th>
                                <th>Doanh thu</th>
                                <th>Tồn kho</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-warning text-dark">1</span></td>
                                <td>iPhone 15 Pro Max</td>
                                <td>156</td>
                                <td class="fw-bold text-primary">4.68 tỷ</td>
                                <td><span class="text-success">50</span></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-secondary">2</span></td>
                                <td>MacBook Pro M3</td>
                                <td>98</td>
                                <td class="fw-bold text-primary">4.89 tỷ</td>
                                <td><span class="text-warning">20</span></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-secondary">3</span></td>
                                <td>Samsung Galaxy S24 Ultra</td>
                                <td>87</td>
                                <td class="fw-bold text-primary">2.61 tỷ</td>
                                <td><span class="text-success">30</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue Time Chart
        new Chart(document.getElementById('revenueTimeChart'), {
            type: 'line',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [{
                    label: 'Doanh thu (triệu)',
                    data: [120, 150, 180, 165, 210, 240, 220, 260, 280, 250, 290, 254],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } }
            }
        });

        // Order Status Chart
        new Chart(document.getElementById('orderStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Chờ xác nhận', 'Đã xác nhận', 'Đang giao', 'Hoàn thành', 'Đã hủy'],
                datasets: [{
                    data: [45, 35, 60, 250, 10],
                    backgroundColor: ['#ffc107', '#0dcaf0', '#0d6efd', '#198754', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Top Products Chart
        new Chart(document.getElementById('topProductsChart'), {
            type: 'bar',
            data: {
                labels: ['iPhone 15 Pro', 'MacBook Pro M3', 'Galaxy S24', 'iPad Pro', 'AirPods Pro'],
                datasets: [{
                    label: 'Số lượng bán',
                    data: [156, 98, 87, 76, 65],
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });

        // Category Revenue Chart
        new Chart(document.getElementById('categoryRevenueChart'), {
            type: 'pie',
            data: {
                labels: ['Điện thoại', 'Laptop', 'Tablet', 'Phụ kiện'],
                datasets: [{
                    data: [45, 30, 15, 10],
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
@endpush
