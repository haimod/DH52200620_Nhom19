<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // 1. Hiển thị form đăng nhập
    public function showLoginForm()
    {
        if (Auth::check()) {
            // Kiểm tra lại lần nữa khi user đã login nhưng truy cập trang login
            if (Auth::user()->trangThai == 'Blocked') {
                $this->logout(request());
                return redirect()->route('login')->with('error', 'Tài khoản của bạn đang bị khóa.');
            }
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    // 2. Xử lý đăng nhập
    public function login(Request $request)
    {
        // Validate dữ liệu từ form
        $request->validate([
            'tenDangNhap' => 'required|string',
            'password' => 'required|string',
        ]);

        // Thông tin đăng nhập
        $credentials = [
            'tenDangNhap' => $request->tenDangNhap,
            'password' => $request->password,
        ];

        // Thử đăng nhập
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            
            $user = Auth::user();
            
            // --- QUAN TRỌNG: KIỂM TRA TRẠNG THÁI KHÓA ---
            // Kiểm tra chính xác giá trị 'Blocked' (phân biệt hoa thường nếu cần)
            if ($user->trangThai === 'Blocked') {
                // Đăng xuất ngay lập tức
                Auth::logout();
                
                // Hủy toàn bộ session để đảm bảo sạch sẽ
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->with('error', 'Tài khoản của bạn đang bị vô hiệu hóa. Vui lòng liên hệ Admin.');
            }
            // ---------------------------------------------

            $request->session()->regenerate();

            // Kiểm tra quyền để điều hướng
            if (in_array($user->vaiTro, ['Admin', 'admin', 'QuanTri'])) {
                return redirect()->route('admin.dashboard'); 
            }

            return redirect()->intended(route('home'));
        }

        // Đăng nhập thất bại
        return back()->withErrors([
            'tenDangNhap' => 'Tên đăng nhập hoặc mật khẩu không chính xác.',
        ])->onlyInput('tenDangNhap');
    }

    // 3. Xử lý đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}