@extends('layouts.admin')

@section('title', 'Quản Lý Mã Giảm Giá')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="admin-title">Quản Lý Mã Giảm Giá</h1>
                <p class="text-muted">Quản lý các mã giảm giá và khuyến mãi</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDiscountModal">
                <i class="fas fa-plus me-2"></i>Thêm Mã Giảm Giá
            </button>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="filterForm" method="GET" action="{{ route('admin.discounts') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select class="form-select" name="type" id="typeFilter">
                                <option value="">Tất cả loại</option>
                                <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                                <option value="amount" {{ request('type') == 'amount' ? 'selected' : '' }}>Số tiền cố định</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" placeholder="Tìm theo mã hoặc mô tả..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Lọc
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Discounts Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="80">ID</th>
                                <th>Mã</th>
                                <th>Loại</th>
                                <th>Giá trị</th>
                                <th>Thời gian</th>
                                <th>Sử dụng</th>
                                <th>Trạng thái</th>
                                <th width="150">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="discountsTableBody">
                            @forelse($discounts as $discount)
                                <tr>
                                    <td>#{{ $discount->id }}</td>
                                    <td>
                                        <strong class="text-primary">{{ $discount->code }}</strong>
                                        @if($discount->description)
                                            <br><small class="text-muted">{{ Str::limit($discount->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($discount->type == 'percentage')
                                            <span class="badge bg-info">Phần trăm</span>
                                        @else
                                            <span class="badge bg-success">Số tiền</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($discount->percentage)
                                            <strong class="text-danger">{{ $discount->percentage }}%</strong>
                                        @else
                                            <strong class="text-success">{{ number_format($discount->amount, 0, ',', '.') }}₫</strong>
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            <i class="fas fa-calendar-start"></i> {{ $discount->start_date->format('d/m/Y') }}<br>
                                            <i class="fas fa-calendar-end"></i> {{ $discount->end_date->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        {{ $discount->used_count ?? 0 }} 
                                        @if($discount->usage_limit)
                                            / {{ $discount->usage_limit }}
                                        @else
                                            / ∞
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $discount->active ? 'success' : 'danger' }}">
                                            {{ $discount->active ? 'Hoạt động' : 'Ngưng' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editDiscount({{ $discount->id }})" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteDiscount({{ $discount->id }})" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Chưa có mã giảm giá nào</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($discounts->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $discounts->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add/Edit Discount Modal -->
    <div class="modal fade" id="addDiscountModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm Mã Giảm Giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="discountForm" onsubmit="event.preventDefault(); saveDiscount();">
                        @csrf
                        <input type="hidden" id="discountId" name="id">
                        <input type="hidden" id="formMethod" name="_method" value="POST">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Mã giảm giá <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="discountCode" name="code" required placeholder="VD: SUMMER2025">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Loại <span class="text-danger">*</span></label>
                                <select class="form-select" id="discountType" name="type" required onchange="toggleValueInput()">
                                    <option value="percentage">Phần trăm (%)</option>
                                    <option value="amount">Số tiền cố định</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" id="valueLabel">Giá trị giảm (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="discountValue" name="value" required min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Giới hạn sử dụng</label>
                                <input type="number" class="form-control" id="usageLimit" name="usage_limit" min="1" placeholder="Để trống = không giới hạn">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="startDate" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="endDate" name="end_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Giá trị đơn hàng tối thiểu</label>
                                <input type="number" class="form-control" id="minPurchase" name="min_purchase" min="0" placeholder="Để trống = không yêu cầu">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Giảm tối đa (cho %)</label>
                                <input type="number" class="form-control" id="maxDiscount" name="max_discount" min="0" placeholder="Để trống = không giới hạn">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Mô tả</label>
                                <textarea class="form-control" id="discountDescription" name="description" rows="3" placeholder="Mô tả chi tiết về mã giảm giá..."></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="discountStatus" name="active" checked>
                                    <label class="form-check-label" for="discountStatus">Kích hoạt</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="saveDiscount()">Lưu</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-discounts.js') }}"></script>
@endpush
