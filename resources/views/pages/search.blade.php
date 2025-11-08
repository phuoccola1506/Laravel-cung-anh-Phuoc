@extends('layouts.app')

@section('title', 'Tìm kiếm: ' . $keyword)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/search.js') }}"></script>
@endpush

@section('content')
    <!-- Search Results -->
    <section class="search-section py-4">
        <div class="container">
            <div class="search-content">
                <!-- Search Header -->
                <div class="search-header">
                    <div class="results-count">
                        Hiển thị <strong>{{ $products->count() }}</strong> trong số <strong>{{ $products->total() }}</strong> sản phẩm
                    </div>
                    <div class="search-sort">
                        <label>Sắp xếp:</label>
                        <select id="sortSelect" onchange="sortProducts(this.value)">
                            <option value="default">Mặc định</option>
                            <option value="name-asc">Tên A-Z</option>
                            <option value="name-desc">Tên Z-A</option>
                            <option value="price-asc">Giá thấp đến cao</option>
                            <option value="price-desc">Giá cao đến thấp</option>
                            <option value="newest">Mới nhất</option>
                        </select>
                    </div>
                    <div class="search-view">
                        <button class="view-btn active" data-view="grid" onclick="changeView('grid')">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="view-btn" data-view="list" onclick="changeView('list')">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>

                <!-- Search Results Grid -->
                @if($products->count() > 0)
                    <div class="search-results">
                        <div class="row g-4" id="productsGrid">
                            @foreach ($products as $product)
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <x-product-card :product="$product" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($products->onFirstPage())
                                <button class="page-btn" disabled>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            @else
                                <a href="{{ $products->appends(['keyword' => $keyword])->previousPageUrl() }}" class="page-btn">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                @if ($page == $products->currentPage())
                                    <button class="page-btn active">{{ $page }}</button>
                                @else
                                    <a href="{{ $products->appends(['keyword' => $keyword])->url($page) }}" class="page-btn">{{ $page }}</a>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($products->hasMorePages())
                                <a href="{{ $products->appends(['keyword' => $keyword])->nextPageUrl() }}" class="page-btn">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <button class="page-btn" disabled>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="search-results">
                        <div class="empty-search">
                            <div class="empty-search-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h2>Không tìm thấy sản phẩm nào</h2>
                            <p>Không tìm thấy kết quả cho "<strong>{{ $keyword }}</strong>". Vui lòng thử từ khóa khác.</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-home"></i> Về trang chủ
                            </a>

                            <!-- Search Suggestions -->
                            <div class="search-suggestions">
                                <h3>Gợi ý tìm kiếm:</h3>
                                <div class="suggestions-list">
                                    <a href="{{ route('pages.search', ['keyword' => 'iPhone']) }}" class="suggestion-tag">iPhone</a>
                                    <a href="{{ route('pages.search', ['keyword' => 'Samsung']) }}" class="suggestion-tag">Samsung</a>
                                    <a href="{{ route('pages.search', ['keyword' => 'Laptop']) }}" class="suggestion-tag">Laptop</a>
                                    <a href="{{ route('pages.search', ['keyword' => 'Xiaomi']) }}" class="suggestion-tag">Xiaomi</a>
                                    <a href="{{ route('pages.search', ['keyword' => 'OPPO']) }}" class="suggestion-tag">OPPO</a>
                                    <a href="{{ route('pages.search', ['keyword' => 'Gaming']) }}" class="suggestion-tag">Gaming</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
