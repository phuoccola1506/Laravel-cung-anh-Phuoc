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

        // Nếu chưa enable 2FA, yêu cầu setup
        if (!$user->google2fa_enabled) {
            // Skip cho các route 2FA
            if (!$request->routeIs('2fa.*')) {
                return redirect()->route('2fa.setup');
            }
            return $next($request);
        }

        // Kiểm tra session đã verify chưa (ưu tiên cao nhất)
        if (session('2fa_verified')) {
            return $next($request);
        }

        // Kiểm tra remember token trong cookie
        $cookieToken = $request->cookie('2fa_remember');
        
        if ($cookieToken && $cookieToken === $user->remember_token) {
            // Cookie hợp lệ, cho phép truy cập
            session(['2fa_verified' => true]);
            return $next($request);
        }

        // Chưa verify, redirect đến trang verify
        if (!$request->routeIs('2fa.*')) {
            return redirect()->route('2fa.verify');
        }

        return $next($request);
    }
}
