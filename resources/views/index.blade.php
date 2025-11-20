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
<!-- 
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Tổng số thiết bị</h3>
                    <div class="number">124</div>
                </div>
                <div class="stat-card">
                    <h3>Thiết bị khả dụng</h3>
                    <div class="number" style="color: #28a745;">87</div>
                </div>
                <div class="stat-card">
                    <h3>Đang được mượn</h3>
                    <div class="number" style="color: #ffc107;">32</div>
                </div>
                <div class="stat-card">
                    <h3>Bảo trì / Hư hỏng</h3>
                    <div class="number" style="color: #dc3545;">5</div>
                </div>
            </div> -->

            <div class="card">
                <div class="card-header">
                    <h2>Danh sách thiết bị</h2>
                    
                    <div class="card-tools">
                        <div class="search-box">
                            <select class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="available">Khả dụng</option>
                                <option value="in-use">Đang mượn</option>
                                <option value="maintenance">Bảo trì</option>
                            </select>
                        </div>

                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Tìm theo tên hoặc mã TB..." onkeyup="searchTable()">
                            <span class="search-icon">🔍</span>
                        </div>

                        <button class="btn btn-primary">+ Mượn thiết bị</button>
                    </div>
                </div>
                <table>
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
                        <tr>
                            <td>TB001</td>
                            <td>Laptop Dell Latitude 5420</td>
                            <td>Laptop</td>
                            <td>Phòng IT</td>
                            <td><span class="status-badge status-available">Khả dụng</span></td>
                            <td>15/08/2025</td>
                        </tr>
                     
                    </tbody>
                </table>
            </div>

            <div class="card">
                <div class="card-header">
             
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>