<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Hiển thị form
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

        // Lấy thông tin đăng nhập
        $credentials = [
            'tenDangNhap' => $request->tenDangNhap,
            'password' => $request->password
        ];

        // Thử đăng nhập
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'tenDangNhap' => 'Sai tên đăng nhập hoặc mật khẩu.',
        ]);
    }

    // Xử lý đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
