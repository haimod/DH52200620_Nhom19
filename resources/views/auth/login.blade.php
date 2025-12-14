<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <!-- FontAwesome cho icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Link file CSS của bạn -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <style>
        /* CSS Bổ sung cho Alert để hiển thị đẹp ngay cả khi chưa có file css ngoài */
        body { font-family: 'Segoe UI', sans-serif; background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); width: 100%; max-width: 400px; }
        .login-header h1 { text-align: center; color: #1f2937; margin-bottom: 30px; font-size: 24px; font-weight: 700; }
        
        /* Style cho thông báo lỗi/thành công */
        .alert { padding: 12px 16px; margin-bottom: 20px; border-radius: 6px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert ul { margin: 0; padding-left: 20px; }
        
        /* Form elements */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #374151; font-weight: 500; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; transition: border-color 0.2s; font-size: 15px; }
        .form-group input:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .submit-btn { width: 100%; padding: 12px; background-color: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background-color 0.2s; font-size: 15px; }
        .submit-btn:hover { background-color: #1d4ed8; }
        .forgot-password { text-align: center; margin-top: 15px; font-size: 14px; }
        .forgot-password a { color: #6b7280; text-decoration: none; }
        .forgot-password a:hover { color: #2563eb; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🔐 Đăng nhập</h1>
        </div>

        {{-- 1. HIỂN THỊ LỖI TỪ SESSION (Tài khoản bị khóa, sai thông tin chung) --}}
        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- 2. HIỂN THỊ THÔNG BÁO THÀNH CÔNG --}}
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- 3. HIỂN THỊ LỖI VALIDATION (Bỏ trống, sai định dạng) --}}
        @if ($errors->any())
            <div class="alert alert-danger" style="display: block;">
                <ul style="margin: 5px 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input 
                    type="text" 
                    id="username" 
                    name="tenDangNhap" 
                    required 
                    autocomplete="username"
                    placeholder="Nhập tên đăng nhập"
                    value="{{ old('tenDangNhap') }}"> {{-- Giữ lại giá trị cũ khi nhập sai --}}
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
                <a href="#">Quên mật khẩu?</a>
            </div>
        </form>
    </div>
</body>
</html>