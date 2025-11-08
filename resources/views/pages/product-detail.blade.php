@extends('layouts.app')

@section('title', $product->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/product-detail.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/product-detail.js') }}"></script>
@endpush

@section('content')
    <main>
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <div class="container">
                <a href="{{ route('home') }}">Trang chủ</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ route('category.show', $product->category->id) }}">{{ $product->category->name }}</a>
                <i class="fas fa-chevron-right"></i>
                <span>{{ $product->name }}</span>
            </div>
        </div>

        <!-- Product Detail -->
        <section class="product-detail">
            <div class="container">
                <div class="product-detail-layout">
                    <!-- Product Gallery -->
                    <div class="product-gallery">
                        <div class="main-image">
                            <img id="mainImage" src="{{ asset('images/' . $product->image) }}" alt="iPhone 15 Pro">
                            @if ($product->discount > 0)
                                <div class="product-badge sale">-{{ $product->discount }}%</div>
                            @elseif ($product->discount == 0)
                                <div class="product-badge new">Mới</div>
                            @endif
                        </div>
                        <div class="thumbnail-images">
                            {{-- Hiển thị ảnh sản phẩm chính --}}
                            <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" class="active"
                                onclick="changeImage(this)">
                            
                            {{-- Hiển thị ảnh của các variants nếu có --}}
                            @if ($product->variants->isNotEmpty())
                                @foreach ($product->variants as $index => $variant)
                                    @if ($variant->image)
                                        @php
                                            $attrs = $variant->attributes;
                                            if (is_string($attrs)) {
                                                $attrs = json_decode($attrs, true);
                                            }
                                            $variantLabel = '';
                                            if (isset($attrs['color'])) {
                                                $variantLabel .= $attrs['color'];
                                            }
                                            if (isset($attrs['storage'])) {
                                                $variantLabel .= ($variantLabel ? ' - ' : '') . $attrs['storage'];
                                            }
                                            if (!$variantLabel) {
                                                $variantLabel = 'Variant ' . ($index + 1);
                                            }
                                        @endphp
                                        <img src="{{ asset('images/' . $variant->image) }}" 
                                             alt="{{ $variantLabel }}" 
                                             data-variant-id="{{ $variant->id }}"
                                             onclick="changeImage(this)">
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="product-main-info">
                        <h1 class="product-title">{{ $product->name }}</h1>

                        <div class="product-meta">
                            <div class="product-rating-detail">
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <span class="rating-number">4.8</span>
                                </div>
                                <div class="rating-count">(125 đánh giá)</div>
                            </div>
                            <a class="text-decoration-none" href="{{ route('category.show', $product->category->id) }}">
                                <strong>{{ $product->category->name }}</strong>
                            </a>
                            <a class="text-decoration-none" href="{{ route('brand.show', $product->brand->id) }}">
                                <strong>{{ $product->brand->name }}</strong>
                            </a>
                            @php
                                $firstVariant = $product->variants->first();
                            @endphp
                            @if($firstVariant)
                                <span class="text-muted">
                                    <strong><span id="product-sku">{{ $firstVariant->sku }}</span></strong>
                                </span>
                            @endif
                        </div>

                        <div class="product-price-detail">
                            <div class="product-price" id="product-price">
                                @php
                                    $displayPrice = $firstVariant ? $firstVariant->price : $product->price;
                                    $displayDiscount =
                                        $firstVariant && isset($firstVariant->discount) ? $firstVariant->discount : 0;
                                @endphp

                                @if ($displayDiscount > 0)
                                    @php
                                        $discounted = $displayPrice * (1 - $displayDiscount / 100);
                                        $finalPrice = round($discounted, -4);
                                    @endphp

                                    <span class="price-old fs-2 text-muted text-decoration-line-through">
                                        {{ number_format($displayPrice, 0, ',', '.') }}đ
                                    </span>
                                    <span class="price-new fs-2 text-danger fw-bold">
                                        {{ number_format($finalPrice, 0, ',', '.') }}đ
                                    </span>
                                @else
                                    <span class="price-new fs-2 text-danger fw-bold">
                                        {{ number_format($displayPrice, 0, ',', '.') }}đ
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Color Selection -->
                        @php
                            // Lấy danh sách màu và storage unique (global scope)
                            $colors = collect();
                            $storages = collect();
                            if ($product->variants->isNotEmpty()) {
                                $colors = $product->variants
                                    ->map(function($v) {
                                        $attrs = $v->attributes;
                                        if (is_string($attrs)) {
                                            $attrs = json_decode($attrs, true);
                                        }
                                        return $attrs['color'] ?? null;
                                    })
                                    ->filter()
                                    ->unique();
                                    
                                $storages = $product->variants
                                    ->map(function($v) {
                                        $attrs = $v->attributes;
                                        if (is_string($attrs)) {
                                            $attrs = json_decode($attrs, true);
                                        }
                                        return $attrs['storage'] ?? null;
                                    })
                                    ->filter()
                                    ->unique();
                            }
                        @endphp
                        
                        <div class="product-options">
                            @if ($product->variants->isNotEmpty())
                                @php
                                    // Lấy tất cả các keys attributes có sẵn từ variant đầu tiên
                                    $firstVariantAttrs = $product->variants->first()->attributes ?? [];
                                    // Đảm bảo attributes là array
                                    if (is_string($firstVariantAttrs)) {
                                        $firstVariantAttrs = json_decode($firstVariantAttrs, true) ?? [];
                                    }
                                    $availableKeys = array_keys($firstVariantAttrs);
                                    
                                    // Định nghĩa label hiển thị cho từng attribute
                                    $attributeLabels = [
                                        'color' => 'Màu sắc',
                                        'storage' => 'Dung lượng',
                                        'ram' => 'RAM',
                                        'cpu' => 'CPU',
                                        'screen_size' => 'Kích thước màn hình',
                                        'connection' => 'Kết nối',
                                        'noise_cancellation' => 'Chống ồn',
                                        'battery_life' => 'Thời lượng pin',
                                        'dpi' => 'DPI',
                                        'sensor' => 'Cảm biến',
                                        'switch_type' => 'Loại switch',
                                        'layout' => 'Layout'
                                    ];
                                @endphp

                                {{-- Hiển thị màu sắc nếu có --}}
                                @if ($colors->isNotEmpty())
                                    <div class="option-group">
                                        <label class="option-label">{{ $attributeLabels['color'] ?? 'Màu sắc' }}:</label>
                                        <div class="color-options">
                                            @foreach ($colors as $index => $colorName)
                                                @php
                                                    $variantWithColor = $product->variants->first(function ($v) use ($colorName) {
                                                        $attrs = $v->attributes;
                                                        if (is_string($attrs)) {
                                                            $attrs = json_decode($attrs, true);
                                                        }
                                                        return isset($attrs['color']) && $attrs['color'] == $colorName;
                                                    });
                                                    $colorCode = '#ccc';
                                                    if ($variantWithColor) {
                                                        $attrs = $variantWithColor->attributes;
                                                        if (is_string($attrs)) {
                                                            $attrs = json_decode($attrs, true);
                                                        }
                                                        $colorCode = $attrs['color_code'] ?? '#ccc';
                                                    }
                                                @endphp
                                                <button class="color-option {{ $index == 0 ? 'active' : '' }}"
                                                    data-color="{{ $colorName }}"
                                                    style="background: {{ $colorCode }};">
                                                    <span class="color-name">{{ $colorName }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Hiển thị storage nếu có --}}
                                @if ($storages->isNotEmpty())
                                    <div class="option-group">
                                        <label class="option-label">{{ $attributeLabels['storage'] ?? 'Dung lượng' }}:</label>
                                        <div class="storage-options">
                                            @foreach ($storages as $index => $storage)
                                                <button class="storage-option {{ $index == 0 ? 'active' : '' }}"
                                                    data-storage="{{ $storage }}">
                                                    {{ $storage }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Hiển thị các attributes khác một cách động --}}
                                @foreach ($availableKeys as $key)
                                    @if (!in_array($key, ['color', 'color_code', 'storage']))
                                        @php
                                            // Lấy các giá trị unique cho attribute này
                                            $values = $product->variants
                                                ->map(function($v) use ($key) {
                                                    $attrs = $v->attributes;
                                                    if (is_string($attrs)) {
                                                        $attrs = json_decode($attrs, true);
                                                    }
                                                    return $attrs[$key] ?? null;
                                                })
                                                ->filter()
                                                ->unique();
                                        @endphp
                                        
                                        @if ($values->isNotEmpty() && $values->count() > 1)
                                            <div class="option-group">
                                                <label class="option-label">{{ $attributeLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}:</label>
                                                <div class="storage-options">
                                                    @foreach ($values as $index => $value)
                                                        <button class="variant-option {{ $index == 0 ? 'active' : '' }}"
                                                            data-attribute="{{ $key }}"
                                                            data-value="{{ $value }}">
                                                            {{ $value }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @endif

                            <!-- Quantity -->
                            <div class="option-group">
                                <label class="option-label">Số lượng:</label>
                                <div class="quantity-selector">
                                    <button class="qty-btn" onclick="decreaseQty()"><i class="fas fa-minus"></i></button>
                                    <input type="number" id="quantity" value="1" min="1" max="10">
                                    <button class="qty-btn" onclick="increaseQty()"><i class="fas fa-plus"></i></button>
                                </div>
                                <span class="stock-status" id="stock-status">
                                    <i class="fas fa-check-circle"></i>
                                    <span id="stock-quantity">{{ $firstVariant ? $firstVariant->stock : $product->stock }}</span> sản phẩm còn lại
                                </span>
                            </div>
                        </div>

                        <!-- Promotions -->
                        <div class="promotions-box">
                            <h3><i class="fas fa-gift"></i> Khuyến mãi đặc biệt</h3>
                            <ul class="promo-list">
                                <li><i class="fas fa-check"></i> Giảm thêm 500.000đ khi thu cũ đổi mới</li>
                                <li><i class="fas fa-check"></i> Trả góp 0% qua thẻ tín dụng</li>
                                <li><i class="fas fa-check"></i> Tặng ốp lưng và dán cường lực chính hãng</li>
                                <li><i class="fas fa-check"></i> Bảo hành 12 tháng tại TechShop</li>
                                <li><i class="fas fa-check"></i> Hỗ trợ 1 đổi 1 trong 30 ngày nếu có lỗi từ NSX</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="product-actions">
                            <button class="btn btn-primary btn-large" onclick="buyNow()">
                                <i class="fas fa-bolt"></i> Mua ngay
                            </button>
                            <button class="btn btn-secondary btn-large" onclick="addToCart()">
                                <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                            </button>
                            <button class="btn-icon" onclick="addToWishlist()">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>

                        <!-- Delivery Info -->
                        <div class="delivery-info">
                            <div class="delivery-item">
                                <i class="fas fa-shipping-fast"></i>
                                <div>
                                    <strong>Giao hàng miễn phí</strong>
                                    <p>Giao hàng toàn quốc trong 2-3 ngày</p>
                                </div>
                            </div>
                            <div class="delivery-item">
                                <i class="fas fa-undo-alt"></i>
                                <div>
                                    <strong>Đổi trả miễn phí</strong>
                                    <p>Trong vòng 15 ngày đầu tiên</p>
                                </div>
                            </div>
                            <div class="delivery-item">
                                <i class="fas fa-shield-alt"></i>
                                <div>
                                    <strong>Bảo hành chính hãng</strong>
                                    <p>Bảo hành 12 tháng toàn quốc</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden data for variants -->
                @php
                    // Đảm bảo attributes được parse thành array trước khi truyền vào JSON
                    $variantsData = $product->variants->map(function($v) {
                        $attrs = $v->attributes;
                        if (is_string($attrs)) {
                            $attrs = json_decode($attrs, true);
                        }
                        return [
                            'id' => $v->id,
                            'sku' => $v->sku,
                            'price' => $v->price,
                            'discount' => $v->discount,
                            'stock' => $v->stock,
                            'image' => $v->image, // Thêm image của variant
                            'attributes' => $attrs
                        ];
                    });
                @endphp
                <div id="variants-data" 
                     data-product-id="{{ $product->id }}"
                     data-variants='@json($variantsData)'
                     data-default-color="{{ $colors->first() ?? '' }}"
                     data-default-storage="{{ $storages->first() ?? '' }}"
                     style="display: none;">
                </div>

                <!-- Product Details Tabs -->
                <div class="product-tabs">
                    <div class="tabs-nav">
                        <button class="tab-btn active" data-tab="description">Mô tả sản phẩm</button>
                        <button class="tab-btn" data-tab="specifications">Thông số kỹ thuật</button>
                        <button class="tab-btn" data-tab="reviews">Đánh giá (125)</button>
                    </div>

                    <div class="tabs-content">
                        <!-- Description Tab -->
                        <div class="tab-pane active" id="description">
                            <h2>iPhone 15 Pro - Titan mạnh mẽ. Nhẹ đến bất ngờ.</h2>
                            <p>iPhone 15 Pro là sản phẩm cao cấp nhất trong dòng iPhone 15 series, được thiết kế với chất
                                liệu titan
                                hàng không vũ trụ cao cấp, mang đến sự bền bỉ vượt trội và trọng lượng nhẹ đáng kinh ngạc.
                            </p>

                            <h3>Thiết kế titan đẳng cấp</h3>
                            <p>Với khung viền làm từ titan cấp 5, iPhone 15 Pro không chỉ nhẹ hơn mà còn bền hơn so với thép
                                không
                                gỉ trên các thế hệ trước. Mặt lưng kính mờ chống bám vân tay kết hợp hoàn hảo với khung
                                titan, tạo
                                nên một thiết kế sang trọng và hiện đại.</p>

                            <h3>Chip A17 Pro - Hiệu năng đỉnh cao</h3>
                            <p>Chip A17 Pro được sản xuất trên tiến trình 3nm tiên tiến nhất hiện nay, mang lại hiệu năng
                                vượt trội
                                và tiết kiệm điện năng. GPU 6 nhân hỗ trợ ray tracing phần cứng, cho trải nghiệm chơi game
                                đỉnh cao
                                như trên console.</p>

                            <h3>Camera Pro 48MP ấn tượng</h3>
                            <p>Hệ thống camera Pro với cảm biến chính 48MP, telephoto 3x zoom quang học và camera ultra wide
                                12MP
                                cho phép bạn chụp ảnh chuyên nghiệp trong mọi điều kiện ánh sáng. Tính năng chụp ảnh 48MP
                                ProRAW và
                                quay video 4K 60fps ProRes mang đến chất lượng hình ảnh xuất sắc.</p>
                        </div>

                        <!-- Specifications Tab -->
                        <div class="tab-pane" id="specifications">
                            <table class="specs-table">
                                <tr>
                                    <th colspan="2">
                                        <h3>Màn hình</h3>
                                    </th>
                                </tr>
                                <tr>
                                    <td>Kích thước</td>
                                    <td>6.1 inch, Super Retina XDR OLED</td>
                                </tr>
                                <tr>
                                    <td>Độ phân giải</td>
                                    <td>2556 x 1179 pixels, 460 ppi</td>
                                </tr>
                                <tr>
                                    <td>Tần số quét</td>
                                    <td>ProMotion 120Hz adaptive</td>
                                </tr>
                                <tr>
                                    <th colspan="2">
                                        <h3>Hiệu năng</h3>
                                    </th>
                                </tr>
                                <tr>
                                    <td>Chip xử lý</td>
                                    <td>Apple A17 Pro (3nm)</td>
                                </tr>
                                <tr>
                                    <td>RAM</td>
                                    <td>8GB</td>
                                </tr>
                                <tr>
                                    <td>Bộ nhớ trong</td>
                                    <td>256GB</td>
                                </tr>
                                <tr>
                                    <th colspan="2">
                                        <h3>Camera</h3>
                                    </th>
                                </tr>
                                <tr>
                                    <td>Camera sau</td>
                                    <td>
                                        - Chính: 48MP, f/1.78, OIS<br>
                                        - Telephoto: 12MP, f/2.8, 3x zoom<br>
                                        - Ultra Wide: 12MP, f/2.2
                                    </td>
                                </tr>
                                <tr>
                                    <td>Camera trước</td>
                                    <td>12MP, f/1.9, TrueDepth</td>
                                </tr>
                                <tr>
                                    <th colspan="2">
                                        <h3>Pin & Sạc</h3>
                                    </th>
                                </tr>
                                <tr>
                                    <td>Dung lượng pin</td>
                                    <td>3274 mAh</td>
                                </tr>
                                <tr>
                                    <td>Sạc nhanh</td>
                                    <td>USB-C PD 20W, MagSafe 15W</td>
                                </tr>
                                <tr>
                                    <th colspan="2">
                                        <h3>Kết nối</h3>
                                    </th>
                                </tr>
                                <tr>
                                    <td>Cổng sạc</td>
                                    <td>USB-C 3.0 (10Gbps)</td>
                                </tr>
                                <tr>
                                    <td>Kết nối không dây</td>
                                    <td>5G, WiFi 6E, Bluetooth 5.3</td>
                                </tr>
                                <tr>
                                    <th colspan="2">
                                        <h3>Thông tin khác</h3>
                                    </th>
                                </tr>
                                <tr>
                                    <td>Hệ điều hành</td>
                                    <td>iOS 17</td>
                                </tr>
                                <tr>
                                    <td>Kích thước</td>
                                    <td>146.6 x 70.6 x 8.25 mm</td>
                                </tr>
                                <tr>
                                    <td>Trọng lượng</td>
                                    <td>187g</td>
                                </tr>
                                <tr>
                                    <td>Chất liệu</td>
                                    <td>Khung Titan, mặt lưng kính mờ</td>
                                </tr>
                                <tr>
                                    <td>Kháng nước</td>
                                    <td>IP68 (sâu 6m trong 30 phút)</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Reviews Tab -->
                        <div class="tab-pane" id="reviews">
                            <div class="reviews-summary">
                                <div class="rating-overview">
                                    <div class="rating-score">
                                        <h2>4.8</h2>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <p>125 đánh giá</p>
                                    </div>
                                    <div class="rating-bars">
                                        <div class="rating-bar-item">
                                            <span>5 <i class="fas fa-star"></i></span>
                                            <div class="bar">
                                                <div class="bar-fill" style="width: 75%"></div>
                                            </div>
                                            <span>94</span>
                                        </div>
                                        <div class="rating-bar-item">
                                            <span>4 <i class="fas fa-star"></i></span>
                                            <div class="bar">
                                                <div class="bar-fill" style="width: 15%"></div>
                                            </div>
                                            <span>19</span>
                                        </div>
                                        <div class="rating-bar-item">
                                            <span>3 <i class="fas fa-star"></i></span>
                                            <div class="bar">
                                                <div class="bar-fill" style="width: 6%"></div>
                                            </div>
                                            <span>8</span>
                                        </div>
                                        <div class="rating-bar-item">
                                            <span>2 <i class="fas fa-star"></i></span>
                                            <div class="bar">
                                                <div class="bar-fill" style="width: 2%"></div>
                                            </div>
                                            <span>3</span>
                                        </div>
                                        <div class="rating-bar-item">
                                            <span>1 <i class="fas fa-star"></i></span>
                                            <div class="bar">
                                                <div class="bar-fill" style="width: 1%"></div>
                                            </div>
                                            <span>1</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="reviews-list">
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-info">
                                            <div class="avatar">NH</div>
                                            <div>
                                                <h4>Nguyễn Văn A</h4>
                                                <div class="stars">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="review-date">15/10/2025</span>
                                    </div>
                                    <div class="review-content">
                                        <p>Sản phẩm rất tuyệt vời, camera chụp ảnh đẹp, pin trâu, thiết kế sang trọng. Rất
                                            đáng
                                            tiền!</p>
                                    </div>
                                    <div class="review-helpful">
                                        <button><i class="far fa-thumbs-up"></i> Hữu ích (24)</button>
                                    </div>
                                </div>

                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-info">
                                            <div class="avatar">TL</div>
                                            <div>
                                                <h4>Trần Thị B</h4>
                                                <div class="stars">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star-half-alt"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="review-date">12/10/2025</span>
                                    </div>
                                    <div class="review-content">
                                        <p>Máy đẹp, mượt mà, rất hài lòng với sản phẩm. Nhân viên tư vấn nhiệt tình.</p>
                                    </div>
                                    <div class="review-helpful">
                                        <button><i class="far fa-thumbs-up"></i> Hữu ích (18)</button>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-primary">Xem thêm đánh giá</button>
                        </div>
                    </div>
                </div>

                <!-- Related Products -->
                <section class="related-products">
                    <h2>Sản phẩm tương tự</h2>
                    <div class="products-grid">
                        <div class="product-card">
                            <a href="product-detail.html?id=2" class="product-image">
                                <img src="https://images.unsplash.com/photo-1567581935884-3349723552ca?w=300&h=300&fit=crop"
                                    alt="iPhone 14">
                            </a>
                            <div class="product-info">
                                <h3><a href="product-detail.html?id=2">iPhone 14 Plus 128GB</a></h3>
                                <div class="product-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <span>(156)</span>
                                </div>
                                <div class="product-price">
                                    <span class="price-new">19.790.000đ</span>
                                </div>
                                <button class="btn btn-cart">
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>

                        <div class="product-card">
                            <a href="product-detail.html?id=3" class="product-image">
                                <img src="https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=300&h=300&fit=crop"
                                    alt="Samsung S24">
                            </a>
                            <div class="product-info">
                                <h3><a href="product-detail.html?id=3">Samsung Galaxy S24 Ultra</a></h3>
                                <div class="product-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <span>(98)</span>
                                </div>
                                <div class="product-price">
                                    <span class="price-new">25.490.000đ</span>
                                </div>
                                <button class="btn btn-cart">
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>

                        <div class="product-card">
                            <a href="product-detail.html?id=4" class="product-image">
                                <img src="https://images.unsplash.com/photo-1591337676887-a217a6970a8a?w=300&h=300&fit=crop"
                                    alt="Xiaomi 14">
                            </a>
                            <div class="product-info">
                                <h3><a href="product-detail.html?id=4">Xiaomi 14 Ultra 5G</a></h3>
                                <div class="product-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <span>(89)</span>
                                </div>
                                <div class="product-price">
                                    <span class="price-new">24.990.000đ</span>
                                </div>
                                <button class="btn btn-cart">
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>

                        <div class="product-card">
                            <a href="product-detail.html?id=5" class="product-image">
                                <img src="https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=300&h=300&fit=crop"
                                    alt="OPPO">
                            </a>
                            <div class="product-info">
                                <h3><a href="product-detail.html?id=5">OPPO Find X7 Pro</a></h3>
                                <div class="product-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <span>(52)</span>
                                </div>
                                <div class="product-price">
                                    <span class="price-new">19.990.000đ</span>
                                </div>
                                <button class="btn btn-cart">
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </main>
@endsection
