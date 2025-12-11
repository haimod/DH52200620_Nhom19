<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Nếu đã đăng nhập VÀ có vai trò là 'admin'
        if (Auth::check() && Auth::user()->vaiTro == 'admin') { // Hoặc 'role' tùy tên cột trong DB của bạn
            return $next($request);
        }

        // Nếu không phải admin, đá về trang chủ và báo lỗi
        return redirect('/')->with('error', 'Bạn không có quyền truy cập trang Quản trị!');
    }
}