<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🔐 Đăng nhập hệ thống</h1>
        </div>

        @if($errors->any())
        <div class="error-message">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input 
                    type="text" 
                    id="username" 
                    name="tenDangNhap" 
                    required 
                    autocomplete="username"
                    placeholder="Nhập tên đăng nhập">
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="Nhập mật khẩu">
            </div>

            <button type="submit" class="submit-btn">Đăng nhập</button>

            <div class="forgot-password">
                <a href="/forgot-password">Quên mật khẩu?</a>
            </div>
        </form>
    </div>
</body>
</html>
