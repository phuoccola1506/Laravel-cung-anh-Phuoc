<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Nếu chưa đăng nhập, bỏ qua
        if (!$user) {
            return $next($request);
        }

        // Nếu chưa enable 2FA, yêu cầu setup lần đầu
        if (!$user->google2fa_enabled) {
            // Skip cho các route 2FA
            if (!$request->routeIs('2fa.*')) {
                return redirect()->route('2fa.setup');
            }
            return $next($request);
        }

        // Đã enable 2FA rồi -> Cho phép truy cập luôn, không cần verify OTP
        // Chỉ verify 1 lần khi setup, các lần sau không cần OTP
        session(['2fa_verified' => true]);
        
        return $next($request);
    }
}
