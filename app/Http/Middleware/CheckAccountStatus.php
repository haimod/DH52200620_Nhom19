<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kiểm tra xem đã đăng nhập chưa
        if (Auth::check()) {
            $user = Auth::user();

            // 2. KIỂM TRA ĐÚNG CỘT 'trangThai' VÀ GIÁ TRỊ 'Blocked'
            // Code cũ của bạn sai ở chỗ dùng $user->status
            if ($user->trangThai == 'Blocked') {
                
                // 3. Nếu bị khóa -> Đăng xuất ngay lập tức
                Auth::logout();

                // 4. Xóa session để không còn lưu trạng thái đăng nhập
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // 5. Đá về trang login kèm thông báo
                return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ Admin.');
            }
        }

        return $next($request);
    }
}