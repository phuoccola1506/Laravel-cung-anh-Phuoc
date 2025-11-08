@extends('layouts.admin')

@section('title', 'Quản Lý Sản Phẩm')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/admin-products.js') }}"></script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="admin-title">Quản Lý Sản Phẩm</h1>
                <p class="text-muted">Quản lý danh sách sản phẩm của cửa hàng</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fas fa-plus me-2"></i>Thêm Sản Phẩm
            </button>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="filterForm" method="GET" action="{{ route('admin.products') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select class="form-select" name="category" id="categoryFilter">
                                <option value="">Tất cả danh mục</option>
                                @foreach(\App\Models\Category::where('active', 1)->get() as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="brand" id="brandFilter">
                                <option value="">Tất cả thương hiệu</option>
                                @foreach(\App\Models\Brand::where('active', 1)->get() as $brand)
                                    <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang bán</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng bán</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Lọc
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="80">ID</th>
                                <th>Sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Thương hiệu</th>
                                <th>Trạng thái</th>
                                <th width="150">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            @forelse($products as $product)
                                <tr>
                                    <td>#{{ $product->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($product->image)
                                                <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" 
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                            @else
                                                <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $product->name }}</div>
                                                <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->active ? 'success' : 'danger' }}">
                                            {{ $product->active ? 'Đang bán' : 'Ngừng bán' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editProduct({{ $product->id }})" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct({{ $product->id }})" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Không có sản phẩm nào</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm Sản Phẩm Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        @csrf
                        <input type="hidden" id="productId" name="product_id">
                        <input type="hidden" id="formMethod" name="_method" value="POST">
                        
                        <h6 class="fw-bold mb-3">Thông tin cơ bản</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="productName" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select" id="productCategory" name="category_id" required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach(\App\Models\Category::where('active', 1)->get() as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Thương hiệu <span class="text-danger">*</span></label>
                                <select class="form-select" id="productBrand" name="brand_id" required>
                                    <option value="">Chọn thương hiệu</option>
                                    @foreach(\App\Models\Brand::where('active', 1)->get() as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Mô tả</label>
                                <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Hình ảnh sản phẩm</label>
                                <input type="file" class="form-control" id="productImage" name="image" accept="image/*" onchange="previewImage(event)">
                                <small class="text-muted">Chấp nhận: JPG, PNG, GIF. Tối đa 2MB.</small>
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="productStatus" name="active" checked>
                                    <label class="form-check-label" for="productStatus">
                                        Đang bán
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Variants Section -->
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Biến thể sản phẩm (Variants)</h6>
                            <button type="button" class="btn btn-sm btn-success" onclick="addVariantRow()">
                                <i class="fas fa-plus me-1"></i> Thêm biến thể
                            </button>
                        </div>
                        
                        <div id="variantsContainer">
                            <!-- Variant rows will be added here -->
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Lưu Sản Phẩm</button>
                </div>
            </div>
        </div>
    </div>
@endsection
