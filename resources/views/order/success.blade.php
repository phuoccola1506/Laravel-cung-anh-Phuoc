@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@push('styles')
<style>
.success-section {
    padding: 60px 0;
    background-color: #f8f9fa;
}

.success-card {
    background: white;
    border-radius: 16px;
    padding: 40px;
    max-width: 800px;
    margin: 0 auto;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    text-align: center;
}

.success-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #00a650 0%, #008a40 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    animation: scaleIn 0.5s ease;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
    }
    to {
        transform: scale(1);
    }
}

.success-icon i {
    font-size: 3rem;
    color: white;
}

.success-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
}

.success-message {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 30px;
}

.order-info-box {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 25px;
    margin: 30px 0;
    text-align: left;
}

.order-info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
}

.order-info-row:last-child {
    border-bottom: none;
}

.order-info-label {
    font-weight: 500;
    color: #666;
}

.order-info-value {
    font-weight: 600;
    color: #333;
}

.order-code {
    font-size: 1.5rem;
    color: #e74c3c;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
}

.btn {
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
}

.btn-secondary {
    background: #f0f0f0;
    color: #333;
}

.btn-secondary:hover {
    background: #e0e0e0;
}
</style>
@endpush

@section('content')
<main>
    <section class="success-section">
        <div class="container">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                
                <h1 class="success-title">Đặt hàng thành công!</h1>
                <p class="success-message">
                    Cảm ơn bạn đã đặt hàng tại TechShop. Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận đơn hàng.
                </p>

                <div class="order-info-box">
                    <div class="order-info-row">
                        <span class="order-info-label">Mã đơn hàng:</span>
                        <span class="order-info-value order-code">{{ $order->order_code }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label">Ngày đặt:</span>
                        <span class="order-info-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label">Phương thức thanh toán:</span>
                        <span class="order-info-value">
                            @if($order->payment_method === 'cod')
                                Thanh toán khi nhận hàng (COD)
                            @elseif($order->payment_method === 'bank')
                                Chuyển khoản ngân hàng
                            @elseif($order->payment_method === 'vnpay')
                                VNPay
                            @elseif($order->payment_method === 'momo')
                                Ví MoMo
                            @else
                                {{ $order->payment_method }}
                            @endif
                        </span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label">Tổng tiền:</span>
                        <span class="order-info-value" style="color: #e74c3c; font-size: 1.3rem;">
                            {{ number_format($order->total, 0, ',', '.') }} ₫
                        </span>
                    </div>
                </div>

                <div class="alert alert-info" style="background: #e3f2fd; border: 1px solid #90caf9; border-radius: 8px; padding: 15px; margin: 20px 0;">
                    <i class="fas fa-info-circle"></i>
                    <strong>Lưu ý:</strong> Vui lòng kiểm tra email hoặc số điện thoại để nhận thông tin chi tiết về đơn hàng.
                </div>

                <div class="action-buttons">
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fas fa-home"></i> Về trang chủ
                    </a>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-primary">
                        <i class="fas fa-file-alt"></i> Xem chi tiết đơn hàng
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
