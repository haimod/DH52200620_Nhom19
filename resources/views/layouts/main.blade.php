<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý tài sản')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f5f7fa; margin: 0; padding: 0; overflow-x: hidden; }
        
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
            top: 70px;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }

        .sidebar-item {
            padding: 12px 24px;
            color: #555;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }

        .sidebar-item:hover, .sidebar-item.active {
            background-color: #f8f9fa;
            color: #0d6efd;
            border-left-color: #0d6efd;
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

        /* Fix dropdown thông báo bị đè */
.dropdown-menu {
    z-index: 2000 !important;
}

    </style>
</head>
<body>

    <div class="app-header">
        
        <div class="header-title">
            <h2 style="font-size: 18px; margin: 0; color: #333; font-weight: 700; display: flex; align-items: center;">
                <i class="fa-solid fa-cube text-primary me-2"></i> QUẢN LÝ TÀI SẢN
            </h2>
        </div>

        <div class="header-right" style="display: flex; align-items: center; gap: 15px;">
            
            <!-- === QUẢ CHUÔNG THÔNG BÁO (ĐÃ SỬA LOGIC ACCORDION) === -->
            <div class="dropdown">
                <!-- Thêm data-bs-auto-close="outside" để không bị đóng menu khi bấm xem chi tiết -->
                <button class="btn btn-light rounded-circle position-relative border" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-regular fa-bell text-secondary"></i>
                    @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                            {{ Auth::user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" style="width: 350px; max-height: 450px; overflow-y: auto;">
                    <li class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light sticky-top">
                        <h6 class="mb-0 fw-bold text-primary">Thông báo</h6>
                        @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                            <a href="{{ route('notifications.markAllRead') }}" class="small text-decoration-none">Đọc tất cả</a>
                        @endif
                    </li>

                    @if(Auth::check())
                        @forelse(Auth::user()->notifications as $notification)
                            <li>
                                <div class="list-group-item list-group-item-action p-3 border-bottom {{ $notification->read_at ? 'bg-white' : 'bg-primary-subtle' }}">
                                    <!-- Header của thông báo -->
                                    <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0 fw-bold small text-danger">
                                            <i class="fa-solid {{ $notification->data['icon'] ?? 'fa-bell' }} me-1 text-primary"></i> 
                                            {{ $notification->data['title'] ?? 'Thông báo' }}
                                        </h6>
                                        <small class="text-muted text-nowrap ms-2" style="font-size: 10px;">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>

                                    <!-- Khu vực nội dung chi tiết (Accordion) -->
                                    <div class="collapse mt-2" id="notif-{{ $notification->id }}">
                                        <div class="card card-body p-2 bg-light border-0 small text-secondary">
                                            {{ $notification->data['content'] ?? '' }}
                                            
                                            
                                        </div>
                                    </div>
                                    
                                    <!-- Các nút hành động -->
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <!-- Nút Xem nội dung (Mở Collapse) -->
                                        <button class="btn btn-sm py-0 px-2 btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#notif-{{ $notification->id }}" aria-expanded="false" style="font-size: 11px;">
                                            Xem nội dung
                                        </button>
                                        
                                        <!-- Nút Đã đọc -->
                                        @if(!$notification->read_at)
                                            <a href="{{ route('notifications.markRead', $notification->id) }}" class="btn btn-link text-muted p-0 text-decoration-none" style="font-size: 11px;">
                                                <i class="fa-solid fa-check me-1"></i> Đã đọc
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-4 text-muted">
                                <i class="fa-regular fa-bell-slash fa-2x mb-2 opacity-50"></i>
                                <p class="small mb-0">Không có thông báo nào.</p>
                            </li>
                        @endforelse
                    @endif
                </ul>
            </div>
            <!-- ============================== -->
            
            <div class="vr h-50 my-auto text-secondary mx-1"></div>

            <div class="user-info" style="display: flex; align-items: center; gap: 10px;">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                         alt="Avatar" 
                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #eee;">
                @else
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px;">
                        {{ substr(Auth::user()->hoTen ?? Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                @endif

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
               style="text-decoration: none; display: flex; align-items: center;">
                <i class="fa-solid fa-chart-pie me-2"></i> Trang chủ
            </a>

            <a href="{{ route('borrow.index') }}" 
               class="sidebar-item {{ request()->routeIs('borrow.index') ? 'active' : '' }}"
               style="text-decoration: none; display: flex; align-items: center;">
                <i class="fa-solid fa-desktop me-2"></i> 
                Danh sách Mượn-Trả
            </a>
             
            <a href="{{ route('return.index') }}" 
               class="sidebar-item {{ request()->routeIs('return.index') ? 'active' : '' }}"
               style="text-decoration: none; display: flex; align-items: center;">
                <i class="fa-solid fa-upload me-2"></i>
                Trả thiết bị
            </a>

            <a href="{{ route('support.index') }}" 
               class="sidebar-item {{ request()->routeIs('support.*') ? 'active' : '' }}"
               style="text-decoration: none; display: flex; align-items: center;">
                <i class="fa-solid fa-headset me-2"></i> Hỗ trợ & Trợ giúp
            </a>
            
            <a href="{{ route('settings.index') }}" 
               class="sidebar-item {{ request()->routeIs('settings.*') ? 'active' : '' }}"
               style="text-decoration: none; display: flex; align-items: center;">
                <i class="fa-solid fa-gear me-2"></i> Cài đặt
            </a>
        </div>

        <div class="main-content">
            @yield('content')
        </div>
    
    </div>
 @include('layouts.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html> 