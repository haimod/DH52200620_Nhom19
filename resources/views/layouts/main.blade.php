<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý tài sản')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        body { background-color: #f5f7fa; margin: 0; padding: 0; overflow-x: hidden; }
        
        /* Layout Flexbox */
        .app-container {
            display: flex;
            min-height: calc(100vh - 70px); /* Trừ đi chiều cao header */
        }

        /* Sidebar cố định bên trái */
       .sidebar {
    width: 250px;
    background-color: #fff;
    padding: 20px 0;
    box-shadow: 2px 0 4px rgba(0,0,0,0.1);
    
    position: sticky;
    top: 70px; /* cách header 70px, nếu header cao hơn thì chỉnh lại */
    height: calc(100vh - 70px); /* chiều cao còn lại */
    overflow-y: auto; /* nếu sidebar dài hơn màn hình */
}

        /* Nội dung chính bên phải */
        .main-content {
            flex: 1;
            padding: 30px;
        }

        /* Header Style */
        .app-header {
            background: #fff;
            padding: 0 30px;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
    </style>
</head>
<body>
<!-- Script riêng từng trang -->
    @yield('scripts')


    <div class="app-header">
        
        <div class="header-title">
            <h2 style="font-size: 18px; margin: 0; color: #333; font-weight: 700;">QUẢN LÝ TÀI SẢN</h2>
        </div>

        <div class="header-right" style="display: flex; align-items: center; gap: 15px;">
            
            <div class="user-info" style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px;">
                    {{ substr(Auth::user()->hoTen ?? 'U', 0, 1) }}
                </div>

                <div style="display: flex; flex-direction: column; line-height: 1.3;">
                    <span style="font-weight: bold; font-size: 14px; color: #333;">
                        {{ Auth::user()->hoTen ?? 'Chưa có tên' }}
                    </span>
                    <span style="font-size: 12px; color: #666;">
                        {{ Auth::user()->maNV ?? '---' }} • <span style="color: #28a745; font-weight: 600;">{{ Auth::user()->phongBan ?? 'IT' }}</span>
                    </span>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-sm btn-light text-danger" title="Đăng xuất" style="border: 1px solid #ffebeb;">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>

        </div>
    </div>
    <div class="app-container">
        
        <div class="sidebar">
            <a href="{{ route('home') }}" 
            class="sidebar-item {{ request()->routeIs('home') ? 'active' : '' }}"
            style="text-decoration: none; color: inherit; display: flex; align-items: center;">
                <i class="fa-solid fa-chart-pie me-2"></i> Trang chủ
            </a>

            <a href="{{ route('borrow.index') }}" 
            class="sidebar-item {{ request()->routeIs('borrow.index') ? 'active' : '' }}"
            style="text-decoration: none; color: inherit; display: flex; align-items: center;">
                <i class="fa-solid fa-desktop me-2"></i> 
                Danh sách Mượn-Trả
            </a>
             
            <a href="{{ route('return.index') }}" 
            class="sidebar-item {{ request()->routeIs('return.index') ? 'active' : '' }}"
            style="text-decoration: none; color: inherit; display: flex; align-items: center;">
                <i class="fa-solid fa-rotate-left me-2"></i> 
                Trả thiết bị
            </a>

            <div class="sidebar-item">
                <i class="fa-solid fa-clock-rotate-left me-2"></i> Hỗ trợ
            </div>
            
            <div class="sidebar-item">
                <i class="fa-solid fa-gear me-2"></i> Cài đặt
            </div>
    </div>

        <div class="main-content">
            @yield('content')
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function openModal(id){
            document.getElementById('borrowModal'+id).style.display = 'flex';
        }
        function closeModal(id){
            document.getElementById('borrowModal'+id).style.display = 'none';
        }
    </script>
</body>
</html>