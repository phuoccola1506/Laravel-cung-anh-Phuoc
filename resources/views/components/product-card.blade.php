@props(['product'])

@php
    // Lấy variant có giá thấp nhất để hiển thị discount
    $minVariant = $product->variants->sortBy('price')->first();
    $hasDiscount = $minVariant && $minVariant->discount > 0;
@endphp

<div class="product-card">
    @if ($hasDiscount)
        <div class="product-badge sale">-{{ $minVariant->discount }}%</div>
    @else
        <div class="product-badge new">Mới</div>
    @endif
    <a href="{{ route('product.show', $product->id) }}" class="product-image">
        <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}">
    </a>
    <div class="product-info">
        <h3><a class="text-dark text-decoration-none"
                href="{{ route('product.show', $product->id) }}">{{ $product->name }}</a></h3>
        <div class="product-rating">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <span>({{ $product->views ?? 0 }})</span>
        </div>
        <div class="product-price">
            @if ($hasDiscount)
                @php
                    // Tính giá sau giảm
                    $discounted = $product->price * (1 - $minVariant->discount / 100);
                    // Làm tròn đến hàng chục nghìn
                    $finalPrice = round($discounted, -4);
                @endphp

                <span class="price-old text-muted text-decoration-line-through">
                    {{ number_format($product->price, 0, ',', '.') }} {{ $currency }}
                </span>
                <span class="price-new text-danger fw-bold">
                    {{ number_format($finalPrice, 0, ',', '.') }} {{ $currency }}
                </span>
            @else
                <span class="price-new text-danger fw-bold">
                    Từ {{ number_format($product->price, 0, ',', '.') }} {{ $currency }}
                </span>
            @endif
        </div>
        <button class="btn btn-primary w-100" onclick="addToCart({{ $product->id }}, {{ $minVariant->id }})">
            <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
        </button>
    </div>
</div>
