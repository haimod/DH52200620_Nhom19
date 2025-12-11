<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        // Validate
        $request->validate([
            'tenDangNhap' => 'required',
            'password' => 'required'
        ]);

                    // Thông tin đưa vào Auth::attempt
                if (Auth::attempt([
                'tenDangNhap' => $request->tenDangNhap,
                'password' => $request->password
                ] ) ) {
                $request->session()->regenerate();

            // Nếu là admin -> Vào trang Dashboard Admin
                 if (Auth::user()->vaiTro === 'admin') {
                return redirect()->route('admin.dashboard');
            }

                return redirect()->intended('/');
            }


        return back()->withErrors([
            'tenDangNhap' => 'Sai tên đăng nhập hoặc mật khẩu.'
        ])->withInput();
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
