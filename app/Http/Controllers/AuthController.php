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
        $credentials = [
            'tenDangNhap' => $request->tenDangNhap,
            'password' => $request->password
        ];

        // Kiểm tra đăng nhập
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/'); // hoặc /home
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
