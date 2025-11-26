<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý tài sản')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <div class="header">
        <div class="header-left">
            <h2>HỆ THỐNG QUẢN LÝ TÀI SẢN</h2>
        </div>
        <div class="header-right">
            <div class="user-info">
                <div class="user-avatar">AD</div>
                <span>Nguyễn Văn A</span>
            </div>
            <div class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </div>
        </div>
    </div>

    <div class="app-container">
        
        <div class="sidebar">
            <div class="sidebar-item active">
                <i class="fa-solid fa-chart-pie"></i> Trang chủ
            </div>
            <div class="sidebar-item">
                <i class="fa-solid fa-desktop"></i> Danh sách thiết bị
            </div>
            <div class="sidebar-item">
                <i class="fa-solid fa-file-pen"></i> Yêu cầu mượn
            </div>
            <div class="sidebar-item">
                <i class="fa-solid fa-clock-rotate-left"></i> Lịch sử
            </div>
            <div class="sidebar-item">
                <i class="fa-solid fa-gear"></i> Cài đặt
            </div>
        </div>

        <div class="main-content">
            @yield('content')
        </div>

    </div>

</body>
</html>