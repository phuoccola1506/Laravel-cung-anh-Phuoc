@extends('layouts.app')

@section('title', $category->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/products.css') }}">
@endpush

@section('content')
    <main>
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <div class="container">
                <a href="../">Trang chủ</a>
                <i class="fas fa-chevron-right"></i>
                <span>{{ $category->name }}</span>
            </div>
        </div>

        <!-- Products Page -->
        <section class="products-page">
            <div class="container">
                <div class="products-layout">
                    <!-- Sidebar Filter -->
                    <aside class="products-sidebar">
                        <div class="filter-section">
                            <h3>Thương hiệu</h3>
                            <div class="filter-search">
                                <input type="text" placeholder="Tìm thương hiệu...">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="filter-group">
                                @foreach ($brands as $brand)
                                    <x-brand-filter-card :brand="$brand" />
                                @endforeach
                            </div>
                        </div>

                        <div class="filter-section">
                            <h3>Khoảng giá</h3>
                            <div class="filter-group">
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="price" value="0-5">
                                    <span>Dưới 5 triệu</span>
                                </label>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="price" value="5-10">
                                    <span>5 - 10 triệu</span>
                                </label>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="price" value="10-15">
                                    <span>10 - 15 triệu</span>
                                </label>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="price" value="15-20">
                                    <span>15 - 20 triệu</span>
                                </label>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="price" value="20-30">
                                    <span>20 - 30 triệu</span>
                                </label>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="price" value="30">
                                    <span>Trên 30 triệu</span>
                                </label>
                            </div>
                            <div class="price-range">
                                <input type="number" placeholder="Từ" class="price-input">
                                <span>-</span>
                                <input type="number" placeholder="Đến" class="price-input">
                            </div>
                            <button class="btn btn-primary btn-block">Áp dụng</button>
                        </div>

                        <div class="filter-section">
                            <h3>Đánh giá</h3>
                            <div class="filter-group">
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="rating" value="5">
                                    <span class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </span>
                                </label>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="rating" value="4">
                                    <span class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </span>
                                </label>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="rating" value="3">
                                    <span class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <button class="btn btn-secondary btn-block">Xóa bộ lọc</button>
                    </aside>

                    <!-- Products Content -->
                    <div class="products-content">
                        <!-- Products Header -->
                        <div class="products-header">
                            <div class="products-count">
                                <strong>{{ $numberOfProducts }}</strong> sản phẩm được tìm thấy
                            </div>
                            <div class="products-sort">
                                <label>Sắp xếp:</label>
                                <select id="sortSelect">
                                    <option value="default">Mặc định</option>
                                    <option value="name-asc">Tên A-Z</option>
                                    <option value="name-desc">Tên Z-A</option>
                                    <option value="price-asc">Giá thấp đến cao</option>
                                    <option value="price-desc">Giá cao đến thấp</option>
                                    <option value="rating">Đánh giá cao nhất</option>
                                    <option value="newest">Mới nhất</option>
                                </select>
                            </div>
                            <div class="products-view">
                                <button class="view-btn active" data-view="grid">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button class="view-btn" data-view="list">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div class="products-grid" id="productsGrid">
                            @foreach ($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="pagination">
                            <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                            <button class="page-btn active">1</button>
                            <button class="page-btn">2</button>
                            <button class="page-btn">3</button>
                            <button class="page-btn">4</button>
                            <button class="page-btn">5</button>
                            <span class="page-dots">...</span>
                            <button class="page-btn">12</button>
                            <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
