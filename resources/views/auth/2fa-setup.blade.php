<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thiết lập xác thực 2 bước - TechShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .setup-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 480px;
            width: 100%;
            padding: 40px 35px;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        .header-icon i {
            font-size: 28px;
            color: white;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }

        .instructions {
            margin-bottom: 25px;
        }

        .instructions h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            font-size: 14px;
            color: #444;
            line-height: 1.6;
        }

        .step-number {
            min-width: 24px;
            height: 24px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .app-buttons {
            display: flex;
            gap: 10px;
            margin: 15px 0 20px;
            justify-content: center;
        }

        .app-btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background: #000;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .app-btn:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .app-btn i {
            margin-right: 6px;
            font-size: 16px;
        }

        .qr-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin: 20px 0;
        }

        .qr-code {
            background: white;
            padding: 15px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .qr-code img {
            display: block;
            width: 200px;
            height: 200px;
        }

        .qr-label {
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 10px;
        }

        .secret-key {
            background: white;
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 12px 15px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: 600;
            color: #667eea;
            letter-spacing: 2px;
            word-break: break-all;
            user-select: all;
        }

        .input-section {
            margin: 25px 0;
        }

        .input-label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }

        .otp-input {
            width: 100%;
            padding: 15px 20px;
            font-size: 24px;
            font-family: 'Courier New', monospace;
            text-align: center;
            letter-spacing: 8px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            outline: none;
            transition: all 0.3s;
        }

        .otp-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn i {
            margin-right: 8px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: #667eea;
        }

        .back-link i {
            margin-right: 5px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert i {
            margin-right: 8px;
        }

        @media (max-width: 480px) {
            .setup-container {
                padding: 30px 25px;
            }

            .header h1 {
                font-size: 20px;
            }

            .qr-code img {
                width: 180px;
                height: 180px;
            }

            .app-buttons {
                flex-direction: column;
            }

            .app-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1>Thiết lập xác thực 2 bước</h1>
            <p>Bảo vệ tài khoản của bạn với Google Authenticator</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="instructions">
            <h3>Hướng dẫn cài đặt:</h3>
            
            <div class="step">
                <div class="step-number">1</div>
                <div>Tải ứng dụng <strong>Google Authenticator</strong> trên điện thoại của bạn:</div>
            </div>

            <div class="app-buttons">
                <a href="https://apps.apple.com/app/google-authenticator/id388497605" target="_blank" class="app-btn">
                    <i class="fab fa-apple"></i> App Store
                </a>
                <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="app-btn">
                    <i class="fab fa-google-play"></i> Google Play
                </a>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div>Mở ứng dụng và quét mã QR bên dưới:</div>
            </div>
        </div>

        <div class="qr-section">
            <div class="qr-code">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code">
            </div>
            
            <div class="qr-label">Hoặc nhập mã thủ công:</div>
            <div class="secret-key">{{ $secret }}</div>
        </div>

        <form action="{{ route('2fa.enable') }}" method="POST">
            @csrf
            
            <div class="instructions">
                <div class="step">
                    <div class="step-number">3</div>
                    <div>Nhập mã 6 chữ số từ ứng dụng để xác nhận:</div>
                </div>
            </div>

            <div class="input-section">
                <label class="input-label">Mã xác thực 6 số</label>
                <input type="text" 
                       class="otp-input"
                       name="one_time_password" 
                       placeholder="000000"
                       maxlength="6"
                       pattern="[0-9]{6}"
                       inputmode="numeric"
                       required
                       autofocus
                       autocomplete="off">
                @error('one_time_password')
                    <div class="alert alert-danger" style="margin-top: 10px;">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-check-circle"></i>
                Kích hoạt xác thực 2 bước
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-arrow-left"></i>
                Đăng xuất
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <script>
        // Auto-submit when 6 digits are entered
        const otpInput = document.querySelector('.otp-input');
        otpInput.addEventListener('input', function(e) {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Auto-submit when 6 digits
            if (this.value.length === 6) {
                this.form.submit();
            }
        });
    </script>
</body>
</html>
