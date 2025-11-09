<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\WelcomeMail;

class AuthController extends Controller
{
    /**
     * Hiển thị trang đăng nhập
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ], [
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không đúng định dạng',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors()->first()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            // Kiểm tra user có tồn tại và active không
            $user = User::where('email', $request->email)->first();
            
            if ($user && $user->active != 1) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên!'
                    ], 403);
                }
                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên!',
                ])->withInput();
            }

            // Attempt login
            $credentials = $request->only('email', 'password');
            $remember = $request->has('remember');

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                // Kiểm tra role để redirect
                /** @var User $user */
                $user = Auth::user();
                
                // Kiểm tra nếu chưa setup 2FA -> Yêu cầu setup lần đầu
                if (!$user->google2fa_enabled) {
                    $redirectRoute = route('2fa.setup');
                } else {
                    // Đã setup 2FA rồi -> Cho vào luôn, không cần verify OTP
                    $redirectRoute = $user->role === 'admin' ? route('admin.index') : route('home');
                    session(['2fa_verified' => true]);
                    
                    // Cập nhật email_verified_at nếu chưa có
                    if (!$user->email_verified_at) {
                        $user->email_verified_at = now();
                        $user->save();
                    }
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Đăng nhập thành công!',
                        'user' => $user,
                        'redirect' => $redirectRoute
                    ]);
                }

                return redirect()->intended($redirectRoute)
                    ->with('success', 'Đăng nhập thành công!');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không chính xác!'
                ], 401);
            }

            return back()->withErrors([
                'email' => 'Email hoặc mật khẩu không chính xác!',
            ])->withInput();

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * Hiển thị trang đăng ký
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký
     */
    public function register(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|regex:/^[0-9]{10,11}$/|unique:users,phone',
                'password' => 'required|min:6|confirmed',
            ], [
                'name.required' => 'Vui lòng nhập họ tên',
                'name.max' => 'Họ tên không được quá 255 ký tự',
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không đúng định dạng',
                'email.unique' => 'Email đã được sử dụng',
                'phone.required' => 'Vui lòng nhập số điện thoại',
                'phone.regex' => 'Số điện thoại phải có 10-11 chữ số',
                'phone.unique' => 'Số điện thoại đã được sử dụng',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors()->first(),
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Send welcome email
            try {
                Mail::to($user->email)->send(new WelcomeMail($user));
            } catch (\Exception $mailException) {
                // Log error but don't fail registration
                Log::error('Failed to send welcome email: ' . $mailException->getMessage());
            }

            // Auto login after register
            Auth::login($user);
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đăng ký thành công!',
                    'user' => $user,
                    'redirect' => route('2fa.setup')
                ], 201);
            }

            return redirect()->route('2fa.setup')
                ->with('success', 'Đăng ký thành công! Vui lòng thiết lập xác thực 2 bước để bảo mật tài khoản.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất thành công!',
                'redirect' => route('home')
            ]);
        }

        return redirect()->route('home')
            ->with('success', 'Đăng xuất thành công!');
    }

    /**
     * Kiểm tra trạng thái đăng nhập (API)
     */
    public function check()
    {
        if (Auth::check()) {
            return response()->json([
                'authenticated' => true,
                'user' => Auth::user()
            ]);
        }

        return response()->json([
            'authenticated' => false
        ]);
    }
}
