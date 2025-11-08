<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use App\Models\User;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Hiển thị trang setup 2FA (lần đầu đăng nhập)
     */
    public function showSetup()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Nếu đã enable 2FA rồi, redirect về verify
        if ($user->google2fa_enabled) {
            return redirect()->route('2fa.verify');
        }

        // Tạo secret key mới
        $secret = $this->google2fa->generateSecretKey();
        
        // Lưu temporary secret vào session
        session(['2fa_temp_secret' => $secret]);

        // Tạo QR code URL
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('auth.2fa-setup', compact('secret', 'qrCodeUrl'));
    }

    /**
     * Xác nhận và kích hoạt 2FA
     */
    public function enableTwoFactor(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $secret = session('2fa_temp_secret');

        if (!$secret) {
            return back()->with('error', 'Phiên làm việc đã hết hạn. Vui lòng thử lại!');
        }

        // Verify OTP
        $valid = $this->google2fa->verifyKey($secret, $request->one_time_password);

        if (!$valid) {
            return back()->with('error', 'Mã xác thực không đúng. Vui lòng thử lại!');
        }

        // Lưu secret vào database và enable 2FA
        $user->google2fa_secret = encrypt($secret);
        $user->google2fa_enabled = true;
        $user->google2fa_verified_at = now();
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }
        $user->save();

        // Tạo remember token
        $rememberToken = Str::random(60);
        $user->remember_token = $rememberToken;
        $user->save();

        // Lưu vào cookie
        cookie()->queue('2fa_remember', $rememberToken, 43200); // 30 days

        // Đánh dấu session đã verify 2FA
        session(['2fa_verified' => true]);

        // Xóa temporary secret
        session()->forget('2fa_temp_secret');

        // Redirect về trang chủ hoặc admin tùy role
        $redirectRoute = $user->role === 'admin' ? route('admin.index') : route('home');
        
        return redirect()->intended($redirectRoute)->with('success', 'Xác thực 2 bước đã được kích hoạt thành công!');
    }

    /**
     * Hiển thị trang nhập mã OTP
     */
    public function showVerify()
    {
        return view('auth.2fa-verify');
    }

    /**
     * Xác minh mã OTP
     */
    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!$user->google2fa_enabled) {
            return redirect()->route('2fa.setup');
        }

        $secret = decrypt($user->google2fa_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->one_time_password);

        if (!$valid) {
            return back()->with('error', 'Mã xác thực không đúng. Vui lòng thử lại!');
        }

        // Cập nhật verified time và email_verified_at
        $user->google2fa_verified_at = now();
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }
        $user->save();

        // Tạo remember token mới
        $rememberToken = Str::random(60);
        $user->remember_token = $rememberToken;
        $user->save();

        // Lưu vào cookie
        cookie()->queue('2fa_remember', $rememberToken, 43200); // 30 days

        // Đánh dấu session đã verify 2FA
        session(['2fa_verified' => true]);

        return redirect()->intended(route('home'))->with('success', 'Xác thực thành công!');
    }

    /**
     * Vô hiệu hóa 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Mật khẩu không đúng!');
        }

        // Disable 2FA
        $user->google2fa_secret = null;
        $user->google2fa_enabled = false;
        $user->google2fa_verified_at = null;
        $user->remember_token = null;
        $user->save();

        // Xóa cookie
        cookie()->queue(cookie()->forget('2fa_remember'));

        return back()->with('success', 'Xác thực 2 bước đã được vô hiệu hóa!');
    }
}
