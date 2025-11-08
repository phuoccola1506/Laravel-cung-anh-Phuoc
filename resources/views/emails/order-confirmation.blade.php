<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 650px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #28a745;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
        }
        .logo span {
            color: #ff6b35;
        }
        .success-badge {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            margin: 10px 0;
            font-weight: bold;
        }
        h1 {
            color: #28a745;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .order-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-info td {
            padding: 8px 0;
        }
        .order-info td:first-child {
            font-weight: bold;
            width: 150px;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .product-table th {
            background-color: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .product-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .product-table tr:last-child td {
            border-bottom: none;
        }
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .total-section {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .total-section table {
            width: 100%;
        }
        .total-section td {
            padding: 5px 0;
        }
        .total-section .grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #dc3545;
            padding-top: 10px;
            border-top: 2px solid #ddd;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        .status-processing {
            background-color: #17a2b8;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        .alert-info {
            background-color: #d1ecf1;
            border-left: 4px solid #0c5460;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Tech<span>Shop</span></div>
            <div class="success-badge">✓ Đặt hàng thành công</div>
        </div>

        <h1>Cảm ơn bạn đã đặt hàng! 🎉</h1>

        <p>Xin chào <strong>{{ $order->customer_name }}</strong>,</p>
        <p>Chúng tôi đã nhận được đơn hàng của bạn và đang tiến hành xử lý. Dưới đây là thông tin chi tiết:</p>

        <div class="order-info">
            <h3 style="margin-top: 0; color: #007bff;">Thông tin đơn hàng</h3>
            <table>
                <tr>
                    <td>Mã đơn hàng:</td>
                    <td><strong>#{{ $order->id }}</strong></td>
                </tr>
                <tr>
                    <td>Ngày đặt:</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Trạng thái:</td>
                    <td>
                        @if($order->status === 'pending')
                            <span class="status-badge status-pending">Chờ xác nhận</span>
                        @elseif($order->status === 'processing')
                            <span class="status-badge status-processing">Đang xử lý</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="order-info">
            <h3 style="margin-top: 0; color: #007bff;">Thông tin người nhận</h3>
            <table>
                <tr>
                    <td>Họ tên:</td>
                    <td>{{ $order->customer_name }}</td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>{{ $order->customer_email }}</td>
                </tr>
                <tr>
                    <td>Số điện thoại:</td>
                    <td>{{ $order->customer_phone }}</td>
                </tr>
                <tr>
                    <td>Địa chỉ giao hàng:</td>
                    <td>{{ $order->shipping_address }}</td>
                </tr>
                @if($order->note)
                <tr>
                    <td>Ghi chú:</td>
                    <td>{{ $order->note }}</td>
                </tr>
                @endif
            </table>
        </div>

        <h3>Chi tiết sản phẩm</h3>
        <table class="product-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="text-align: center;">Số lượng</th>
                    <th style="text-align: right;">Đơn giá</th>
                    <th style="text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center;">
                            @if($item->product && $item->product->image)
                                <img src="{{ asset('images/' . $item->product->image) }}" 
                                     alt="{{ $item->product_name }}" 
                                     class="product-image"
                                     style="margin-right: 10px;">
                            @endif
                            <div>
                                <strong>{{ $item->product_name }}</strong>
                                @if($item->variant)
                                    <br><small style="color: #666;">{{ $item->variant->attributes }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($item->price, 0, ',', '.') }} VND</td>
                    <td style="text-align: right;">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} VND</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <table>
                <tr>
                    <td>Tạm tính:</td>
                    <td style="text-align: right;">{{ number_format($order->subtotal, 0, ',', '.') }} VND</td>
                </tr>
                @if($order->discount_amount > 0)
                <tr>
                    <td>Giảm giá:</td>
                    <td style="text-align: right; color: #28a745;">-{{ number_format($order->discount_amount, 0, ',', '.') }} VND</td>
                </tr>
                @endif
                <tr>
                    <td>Phí vận chuyển:</td>
                    <td style="text-align: right;">{{ number_format($order->shipping_fee, 0, ',', '.') }} VND</td>
                </tr>
                <tr class="grand-total">
                    <td>TỔNG CỘNG:</td>
                    <td style="text-align: right;">{{ number_format($order->total, 0, ',', '.') }} VND</td>
                </tr>
            </table>
        </div>

        <div class="alert-info">
            <strong>💡 Lưu ý:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Đơn hàng sẽ được xử lý và giao trong vòng 2-5 ngày làm việc</li>
                <li>Bạn sẽ nhận được email thông báo khi đơn hàng được vận chuyển</li>
                <li>Vui lòng kiểm tra kỹ sản phẩm khi nhận hàng</li>
                <li>Hỗ trợ đổi trả trong vòng 7 ngày nếu có lỗi từ nhà sản xuất</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('order.success', $order->id) }}" class="btn">Xem chi tiết đơn hàng</a>
            <a href="{{ route('home') }}" class="btn" style="background-color: #6c757d;">Tiếp tục mua sắm</a>
        </div>

        <p style="margin-top: 30px;">Nếu bạn có bất kỳ câu hỏi nào về đơn hàng, vui lòng liên hệ:</p>
        <ul style="list-style: none; padding: 0;">
            <li>📧 Email: support@techshop.vn</li>
            <li>📞 Hotline: 1900 xxxx</li>
            <li>💬 Chat trực tuyến trên website</li>
        </ul>

        <div class="footer">
            <p>Trân trọng,<br><strong>Đội ngũ TechShop</strong></p>
            <p style="font-size: 12px; color: #999; margin-top: 20px;">
                Email này được gửi tự động, vui lòng không trả lời.<br>
                © {{ date('Y') }} TechShop. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
