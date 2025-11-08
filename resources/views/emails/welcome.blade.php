<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào mừng đến với TechShop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
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
            border-bottom: 2px solid #007bff;
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
        h1 {
            color: #007bff;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .welcome-message {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .benefits {
            list-style: none;
            padding: 0;
        }
        .benefits li {
            padding: 10px 0;
            padding-left: 30px;
            position: relative;
        }
        .benefits li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
            font-size: 18px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
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
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Tech<span>Shop</span></div>
            <p style="margin: 10px 0 0 0; color: #666;">Nền tảng mua sắm công nghệ hàng đầu</p>
        </div>

        <h1>Chào mừng {{ $user->name }}! 🎉</h1>

        <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>TechShop</strong>. Chúng tôi rất vui được chào đón bạn trở thành thành viên của cộng đồng!</p>

        <div class="welcome-message">
            <h3 style="margin-top: 0; color: #007bff;">Thông tin tài khoản của bạn:</h3>
            <p><strong>Họ tên:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            @if($user->phone)
                <p><strong>Số điện thoại:</strong> {{ $user->phone }}</p>
            @endif
            <p><strong>Ngày đăng ký:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <h3>Lợi ích khi mua sắm tại TechShop:</h3>
        <ul class="benefits">
            <li>Sản phẩm chính hãng, đa dạng mẫu mã</li>
            <li>Giá cả cạnh tranh, nhiều ưu đãi hấp dẫn</li>
            <li>Giao hàng nhanh chóng, thanh toán linh hoạt</li>
            <li>Hỗ trợ khách hàng 24/7</li>
            <li>Chính sách đổi trả trong 7 ngày</li>
            <li>Tích điểm thành viên và nhận quà tặng</li>
        </ul>

        <div style="text-align: center;">
            <a href="{{ route('home') }}" class="btn">Khám phá ngay</a>
        </div>

        <p style="margin-top: 30px;">Nếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại liên hệ với chúng tôi qua:</p>
        <ul style="list-style: none; padding: 0;">
            <li>📧 Email: support@techshop.vn</li>
            <li>📞 Hotline: 1900 xxxx</li>
            <li>💬 Chat trực tuyến trên website</li>
        </ul>

        <div class="footer">
            <p>Trân trọng,<br><strong>Đội ngũ TechShop</strong></p>
            
            <div class="social-links">
                <a href="#">Facebook</a> | 
                <a href="#">Instagram</a> | 
                <a href="#">YouTube</a>
            </div>

            <p style="font-size: 12px; color: #999;">
                Email này được gửi tự động, vui lòng không trả lời.<br>
                © {{ date('Y') }} TechShop. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
