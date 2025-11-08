@extends('layouts.clean', ['cssClass' => 'page-login'])

@section('title', 'Login')

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
                        <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=600&h=800&fit=crop"
                            alt="Shopping">
                    </div>
                </div>

                <div class="auth-right">
                    <div class="auth-box">
                        <h2>Đăng Nhập</h2>
                        <p class="auth-subtitle">Chào mừng bạn quay trở lại!</p>

                        <form class="auth-form" id="loginForm">
                            @csrf
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
                                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                                    <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-options">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="remember" name="remember">
                                    <span>Ghi nhớ đăng nhập</span>
                                </label>
                                <a href="#" class="forgot-password">Quên mật khẩu?</a>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block btn-large">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập
                            </button>
                        </form>

                        <div class="divider">
                            <span>Hoặc đăng nhập với</span>
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
                            Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
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
