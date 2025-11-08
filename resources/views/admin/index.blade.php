@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endpush

@section('content')
    <!-- Dashboard Content -->
    <div class="admin-content">
        <div class="page-header">
            <h1>Dashboard</h1>
            <p>Tổng quan hoạt động kinh doanh</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($totalRevenue) }}đ</h3>
                    <p>Tổng doanh thu</p>
                    @if($revenueTrend != 0)
                        <div class="stat-trend {{ $revenueTrend > 0 ? 'up' : 'down' }}">
                            <i class="fas fa-arrow-{{ $revenueTrend > 0 ? 'up' : 'down' }}"></i> 
                            {{ abs($revenueTrend) }}% so với tháng trước
                        </div>
                    @else
                        <div class="stat-trend">
                            <i class="fas fa-minus"></i> Không đổi
                        </div>
                    @endif
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($orderCount) }}</h3>
                    <p>Tổng đơn hàng</p>
                    @if($orderTrend != 0)
                        <div class="stat-trend {{ $orderTrend > 0 ? 'up' : 'down' }}">
                            <i class="fas fa-arrow-{{ $orderTrend > 0 ? 'up' : 'down' }}"></i> 
                            {{ abs($orderTrend) }}% so với tháng trước
                        </div>
                    @else
                        <div class="stat-trend">
                            <i class="fas fa-minus"></i> Không đổi
                        </div>
                    @endif
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($userCount) }}</h3>
                    <p>Khách hàng</p>
                    <div class="stat-trend">
                        <i class="fas fa-info-circle"></i> Đang hoạt động
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($productCount) }}</h3>
                    <p>Sản phẩm</p>
                    <div class="stat-trend">
                        <i class="fas fa-info-circle"></i> Đang bán
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables -->
        <div class="dashboard-grid">
            <!-- Revenue Chart -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i> Doanh thu 12 tháng</h3>
                    <select class="filter-select">
                        <option>2025</option>
                        <option>2024</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-shopping-bag"></i> Đơn hàng gần đây</h3>
                    <a href="{{ route('admin.orders') }}" class="view-all-link">Xem tất cả</a>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Sản phẩm</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td><strong>#{{ $order->order_code }}</strong></td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($order->items->count() > 0)
                                            {{ $order->items->first()->product->name ?? 'N/A' }}
                                            @if($order->items->count() > 1)
                                                <small>(+{{ $order->items->count() - 1 }} sản phẩm)</small>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ number_format($order->total) }}đ</td>
                                    <td>
                                        @php
                                            $statusClass = match($order->status) {
                                                'delivered' => 'success',
                                                'shipping' => 'warning',
                                                'processing' => 'info',
                                                'cancelled' => 'danger',
                                                default => 'secondary'
                                            };
                                            $statusText = match($order->status) {
                                                'pending' => 'Chờ xác nhận',
                                                'processing' => 'Đang xử lý',
                                                'shipping' => 'Đang giao',
                                                'delivered' => 'Đã giao',
                                                'cancelled' => 'Đã hủy',
                                                default => $order->status
                                            };
                                        @endphp
                                        <span class="badge-status {{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Chưa có đơn hàng nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Products -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-fire"></i> Sản phẩm bán chạy</h3>
                    <select class="filter-select">
                        <option>Tất cả thời gian</option>
                    </select>
                </div>
                <div class="products-list">
                    @forelse($topProducts as $product)
                        <div class="product-item">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1592286927505-2fd14fb8569b?w=60&h=60&fit=crop' }}"
                                alt="{{ $product->name }}">
                            <div class="product-details">
                                <h4>{{ $product->name }}</h4>
                                <p>Đã bán: {{ number_format($product->total_sold) }} sản phẩm</p>
                            </div>
                            <div class="product-revenue">
                                <strong>{{ number_format($product->total_revenue) }}đ</strong>
                            </div>
                        </div>
                    @empty
                        <p class="text-center">Chưa có dữ liệu bán hàng</p>
                    @endforelse
                </div>
            </div>            <!-- Customer Statistics -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie"></i> Phân loại người dùng</h3>
                </div>
                <div class="chart-container">
                    <canvas id="customerChart"></canvas>
                </div>
                <div class="legend-list">
                    <div class="legend-item">
                        <span class="legend-color" style="background: #667eea;"></span>
                        <span>Khách hàng ({{ number_format($customerChartData[0]) }})</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #00a650;"></span>
                        <span>Quản trị viên ({{ number_format($customerChartData[1]) }})</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Dữ liệu từ Controller
    const chartLabels = @json($chartLabels);
    const chartData = @json($chartData);
    const customerChartData = @json($customerChartData);

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Doanh thu',
                    data: chartData,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
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
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + 'đ';
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                }
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }

    // Customer Chart (Pie/Doughnut)
    const customerCtx = document.getElementById('customerChart');
    if (customerCtx) {
        new Chart(customerCtx, {
            type: 'doughnut',
            data: {
                labels: ['Khách hàng', 'Quản trị viên'],
                datasets: [{
                    data: customerChartData,
                    backgroundColor: [
                        '#667eea',
                        '#00a650'
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
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + new Intl.NumberFormat('vi-VN').format(context.parsed);
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
