<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Admin Portal</title>
    
    <!-- CSS Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; margin: 0; }
        
        /* Sidebar Admin - Màu tối để phân biệt */
        .admin-sidebar {
            width: 260px;
            height: 100vh;
            background: #1e293b; /* Màu xanh đen sang trọng */
            color: #fff;
            position: fixed;
            top: 0; left: 0;
            display: flex; flex-direction: column;
            z-index: 1000;
        }

        .admin-sidebar .brand {
            height: 70px;
            display: flex; align-items: center; padding: 0 24px;
            font-size: 18px; font-weight: 700; color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: #0f172a;
        }

        .admin-sidebar .nav-link {
            color: #94a3b8;
            padding: 12px 24px;
            font-weight: 500;
            display: flex; align-items: center;
            transition: all 0.3s;
            text-decoration: none;
            border-left: 3px solid transparent;
        }

        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.05);
            border-left-color: #3b82f6; /* Xanh dương */
        }

        .admin-sidebar .nav-link i { width: 24px; text-align: center; margin-right: 10px; }

        /* Main Content */
        .admin-content { margin-left: 260px; padding-bottom: 50px; }
        
        .admin-header {
            height: 70px; background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 30px;
            position: sticky; top: 0; z-index: 999;
        }
    </style>
</head>
<body>

    <!-- 1. SIDEBAR ADMIN -->
    <div class="admin-sidebar">
        <div class="brand">
            <i class="fa-solid fa-shield-halved me-2 text-primary"></i> QUẢN TRỊ VIÊN
        </div>
        
        <div class="py-3 overflow-auto flex-grow-1">
            <small class="text-uppercase text-muted fw-bold px-4 mb-2 d-block" style="font-size: 11px;">Thống kê</small>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Tổng quan
            </a>

            <small class="text-uppercase text-muted fw-bold px-4 mb-2 mt-3 d-block" style="font-size: 11px;">Quản lý</small>
            <a href="{{ route('admin.devices.index') }}" class="nav-link {{ request()->routeIs('admin.devices.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-laptop"></i> Thiết bị & Tài sản
            </a>
                        <a href="{{ route('admin.borrow.index') }}" 
            class="nav-link {{ request()->routeIs('admin.borrow.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-signature"></i> Quản lý Mượn/Trả
            </a>
                     <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> Nhân sự
            </a>

            <small class="text-uppercase text-muted fw-bold px-4 mb-2 mt-3 d-block" style="font-size: 11px;">Hệ thống</small>

            <a href="{{ route('admin.support.index') }}" class="nav-link {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                <i class="fa-solid fa-headset"></i> Trung tâm hỗ trợ
            </a>
            <a href="{{ route('settings.index') }}" class="nav-link"><i class="fa-solid fa-sliders"></i> Cấu hình</a>
        </div>
    </div>

    <!-- 2. MAIN CONTENT -->
    <div class="admin-content">
        <!-- Header chung cho mọi trang admin -->
        <div class="admin-header">
            <!-- Tiêu đề trang (Sẽ thay đổi theo từng view) -->
            <h5 class="fw-bold text-dark m-0">@yield('header_title', 'Dashboard')</h5>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Nút thông báo -->
               

                <div class="vr h-50 my-auto text-secondary"></div>

                <!-- Thông tin Admin -->
                <div class="d-flex align-items-center gap-2">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/'.Auth::user()->avatar) }}" class="rounded-circle border" width="35" height="35" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:35px;height:35px;font-size:14px;">
                            {{ substr(Auth::user()->hoTen ?? 'A', 0, 1) }}
                        </div>
                    @endif
                    
                    <div class="d-none d-lg-block text-end" style="line-height: 1.2;">
                        <span class="d-block fw-bold text-dark small">{{ Auth::user()->hoTen }}</span>
                        <span class="d-block text-muted" style="font-size: 10px;">{{ Auth::user()->vaiTro ?? 'Super Admin' }}</span>
                    </div>
                </div>

                <!-- Nút Logout -->
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="btn btn-light text-danger border btn-sm shadow-sm ms-2" title="Đăng xuất">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Nội dung thay đổi -->
        @yield('content')
        
    </div>
@include('layouts.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts')
</body>
</html>