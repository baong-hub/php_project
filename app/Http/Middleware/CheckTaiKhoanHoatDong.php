<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTaiKhoanHoatDong
{
    /**
     * Tài khoản bị admin khóa trong lúc đang đăng nhập sẽ bị đăng xuất ngay
     * ở request tiếp theo (web) hoặc bị từ chối (API).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->trang_thai) {
            $message = 'Tài khoản của bạn đã bị khóa.';

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
