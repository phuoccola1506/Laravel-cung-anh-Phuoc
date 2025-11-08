@extends('layouts.clean', ['cssClass' => 'page-signup'])

@section('title', 'Signup')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/auth.js') }}"></script>
@endpush

@section('childContent')
    <main>

        <div class="auth-page">
            <div class="auth-container">
                <div class="auth-left">
                    <div class="auth-brand">
                        <h1>Tech<span>Shop</span></h1>
                        <p>Nền tảng mua sắm công nghệ hàng đầu Việt Nam</p>
                    </div>
                    <div class="auth-image">
                        <img src="https://images.unsplash.com/photo-1556742044-3c52d6e88c62?w=600&h=800&fit=crop"
                            alt="Shopping">
                    </div>
                </div>

                <div class="auth-right">
                    <div class="auth-box">
                        <h2>Đăng Ký</h2>
                        <p class="auth-subtitle">Tạo tài khoản mới để bắt đầu mua sắm!</p>

                        <form class="auth-form" id="registerForm">
                            <div class="form-group">
                                <label for="name">Họ và tên</label>
                                <div class="input-group">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="name" name="name" placeholder="Nguyễn Văn A"
                                        required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <div class="input-group">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" placeholder="example@email.com"
                                        required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password">Mật khẩu</label>
                                <div class="input-group">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="password" name="password" placeholder="••••••••"
                                        minlength="6" required>
                                    <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="form-hint">Mật khẩu phải có ít nhất 6 ký tự</small>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Xác nhận mật khẩu</label>
                                <div class="input-group">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        placeholder="••••••••" required>
                                    <button type="button" class="toggle-password"
                                        onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-options">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="agree" required>
                                    <span>Tôi đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính
                                            sách bảo mật</a></span>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block btn-large">
                                <i class="fas fa-user-plus"></i> Đăng ký
                            </button>
                        </form>

                        <div class="divider">
                            <span>Hoặc đăng ký với</span>
                        </div>

                        <div class="social-login">
                            <button class="btn-social btn-google">
                                <i class="fab fa-google"></i> Google
                            </button>
                            <button class="btn-social btn-facebook">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </button>
                        </div>

                        <p class="auth-footer">
                            Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
                        </p>

                        <a href="{{ route('home') }}" class="back-home">
                            <i class="fas fa-arrow-left"></i> Về trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </main>
@endsection
