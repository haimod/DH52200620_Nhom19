<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý Tài sản</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h2>HỆ THỐNG QUẢN LÝ TÀI SẢN DOANH NGHIỆP</h2>
        </div>
        <div class="header-right">
            <div class="user-info">
                <div class="user-avatar">N</div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;">Nguyễn Văn A</div>
                    <div style="font-size: 12px; color: #666;">Nhân viên</div>
                </div>
            </div>
            <div class="logout-btn">Đăng xuất</div>
        </div>
    </div>

    <div class="container">
        <div class="sidebar">
            <div class="sidebar-item active">
                <span>📊</span>
                <span>Trang chủ</span>
            </div>
            <div class="sidebar-item">
                <span>💻</span>
                <span>Danh sách thiết bị</span>
            </div>
            <div class="sidebar-item">
                <span>📝</span>
                <span>Yêu cầu mượn thiết bị</span>
            </div>
            <div class="sidebar-item">
                <span>📅</span>
                <span>Lịch mượn thiết bị</span>
            </div>
            <div class="sidebar-item">
                <span>📜</span>
                <span>Lịch sử mượn trả</span>
            </div>
            <div class="sidebar-item">
                <span>⚙️</span>
                <span>Cài đặt tài khoản</span>
            </div>
        </div>

        <div class="main-content">
            <div class="page-header">
                <h1>Trang chủ</h1>
                <p>Tổng quan về tình trạng thiết bị và hoạt động mượn trả</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Danh sách thiết bị</h2>
                    <div class="card-tools">
                        <div class="search-box">
                            <select class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Available">Khả dụng</option>
                                <option value="In_Use">Đang mượn</option>
                                <option value="Maintenance">Bảo trì</option>
                            </select>
                        </div>
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Tìm theo tên hoặc mã TB..." onkeyup="searchTable()">
                            <span class="search-icon">🔍</span>
                        </div>
                        <button class="btn btn-primary">+ Mượn thiết bị</button>
                    </div>
                </div>

                <div class="card-body">
                    <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Mã thiết bị</th>
                                <th>Tên thiết bị</th>
                                <th>Loại</th>
                                <th>Phòng</th>
                                <th>Tình trạng</th>
                                <th>Hạn bảo hành</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($thietbi as $tb)
                            <tr>
                                <td>{{ $tb->maTB }}</td>
                                <td>{{ $tb->tenTB }}</td>
                                <td>{{ $tb->maLoai }}</td>
                                <td>{{ $tb->maPhong ?? 'Chưa có' }}</td>
                                <td>
                                    @if($tb->tinhTrang == 'Available')
                                        <span class="status-badge status-available">Khả dụng</span>
                                    @elseif($tb->tinhTrang == 'In_Use')
                                        <span class="status-badge status-in-use">Đang mượn</span>
                                    @elseif($tb->tinhTrang == 'Maintenance')
                                        <span class="status-badge status-maintenance">Bảo trì</span>
                                    @else
                                        <span class="status-badge status-broken">{{ $tb->tinhTrang }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($tb->hanBaoHanh)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

   
</body>
</html>
