<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực 2 bước - TechShop</title>
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

        .verify-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
            padding: 50px 40px;
            animation: slideUp 0.5s ease;
            text-align: center;
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

        .icon-wrapper {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 15px rgba(102, 126, 234, 0);
            }
        }

        .icon-wrapper i {
            font-size: 45px;
            color: white;
        }

        h1 {
            font-size: 26px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 15px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 35px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
            text-align: left;
        }

        .alert-danger {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }

        .alert i {
            margin-right: 8px;
        }

        .otp-input {
            width: 100%;
            padding: 20px;
            font-size: 32px;
            font-family: 'Courier New', monospace;
            text-align: center;
            letter-spacing: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            outline: none;
            transition: all 0.3s;
            margin-bottom: 25px;
        }

        .otp-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
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

        .help-section {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e0e0e0;
        }

        .help-text {
            font-size: 13px;
            color: #666;
            margin-bottom: 12px;
        }

        .help-text i {
            margin-right: 6px;
            color: #667eea;
        }

        .help-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .help-link:hover {
            text-decoration: underline;
        }

        .logout-link {
            display: inline-block;
            margin-top: 20px;
            color: #999;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .logout-link:hover {
            color: #667eea;
        }

        .logout-link i {
            margin-right: 6px;
        }

        @media (max-width: 480px) {
            .verify-container {
                padding: 40px 30px;
            }

            h1 {
                font-size: 22px;
            }

            .icon-wrapper {
                width: 80px;
                height: 80px;
            }

            .icon-wrapper i {
                font-size: 35px;
            }

            .otp-input {
                font-size: 28px;
                letter-spacing: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="icon-wrapper">
            <i class="fas fa-mobile-alt"></i>
        </div>

        <h1>Xác thực 2 bước</h1>
        <p class="subtitle">
            Vui lòng nhập mã 6 số từ ứng dụng<br>Google Authenticator
        </p>

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('2fa.verify.post') }}" method="POST">
            @csrf
            
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
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror

            <button type="submit" class="submit-btn">
                <i class="fas fa-check-circle"></i>
                Xác thực
            </button>
        </form>

        <div class="help-section">
            <p class="help-text">
                <i class="fas fa-info-circle"></i>
                Mã xác thực thay đổi mỗi 30 giây
            </p>
            <p class="help-text">
                Chưa có mã? 
                <a href="{{ route('2fa.setup') }}" class="help-link">Thiết lập ngay</a>
            </p>

            <a href="{{ route('logout') }}" 
               class="logout-link"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                Đăng xuất
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <script>
        const otpInput = document.querySelector('.otp-input');
        
        // Only allow numbers
        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Auto-submit when 6 digits
            if (this.value.length === 6) {
                this.form.submit();
            }
        });

        // Focus on load
        otpInput.focus();
    </script>
</body>
</html>
