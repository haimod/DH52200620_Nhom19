@extends('layouts.admin') 
@section('title', 'Admin Dashboard')
@section('header_title', 'Tổng quan hệ thống')

@section('content')
<div class="container-fluid p-4">

    <!-- CSS RIÊNG CHO TRANG DASHBOARD -->
    <style>
        /* Hiệu ứng hover cho Card */
        .hover-card { transition: all 0.3s ease; border: 1px solid transparent; }
        .hover-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; border-color: #cfe2ff; }
        
        .stat-card { border-left: 4px solid; transition: transform 0.2s; }
        .stat-card:hover { transform: scale(1.02); }

        /* Avatar */
        .avatar-circle { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; color: white; }
    </style>

    <!-- Heading & Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0">Dashboard Quản trị</h2>
            <p class="text-muted mb-0 mt-1">
                Chào Admin! Hệ thống có <span class="badge bg-danger rounded-pill">{{ $stats['open_tickets'] ?? 0 }} tin nhắn hỗ trợ</span> đang chờ xử lý.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-light border shadow-sm text-secondary bg-white">
                <i class="fa-solid fa-file-export me-2"></i>Xuất Excel
            </button>
            <button class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus me-2"></i>Nhập tài sản mới
            </button>
        </div>
    </div>

    <!-- 4 Thẻ Thống Kê (Stat Cards) -->
    <div class="row g-4 mb-4">
        <!-- 1. Tổng Users -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase text-muted small fw-bold">Tổng nhân sự</span>
                        <i class="fa-solid fa-ellipsis text-muted"></i>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="fw-bold text-dark mb-0">{{ $stats['total_users'] ?? 0 }}</h2>
                            <small class="text-success fw-bold"><i class="fa-solid fa-user-check"></i> Đang hoạt động</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                            <i class="fa-solid fa-users fa-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Tổng Tài sản -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase text-muted small fw-bold">Tổng thiết bị</span>
                        <i class="fa-solid fa-ellipsis text-muted"></i>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="fw-bold text-dark mb-0">{{ $stats['total_devices'] ?? 0 }}</h2>
                            <small class="text-muted">Trong kho & cấp phát</small>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded p-3">
                            <i class="fa-solid fa-laptop fa-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Yêu cầu Mượn/Trả (MỚI THÊM) -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase text-muted small fw-bold">Yêu cầu Mượn/Trả</span>
                        <a href="{{ route('admin.borrow.index') }}" class="text-muted"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <!-- Biến đếm số lượng chờ duyệt -->
                            <h2 class="fw-bold text-dark mb-0">{{ $stats['pending_borrows'] ?? 0 }}</h2>
                            <small class="text-warning fw-bold"><i class="fa-regular fa-clock"></i> Đang chờ duyệt</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                            <i class="fa-solid fa-file-signature fa-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Hỗ trợ (Open Tickets) -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card border-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase text-muted small fw-bold">Yêu cầu Hỗ trợ</span>
                        <i class="fa-solid fa-ellipsis text-muted"></i>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="fw-bold text-dark mb-0">{{ $stats['open_tickets'] ?? 0 }}</h2>
                            <small class="text-muted">Chưa xử lý</small>
                        </div>
                        <div class="bg-secondary bg-opacity-10 text-secondary rounded p-3">
                            <i class="fa-solid fa-headset fa-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout chính: Bảng dữ liệu (8) & Widget (4) -->
    <div class="row g-4 mb-4">
        <!-- Cột Trái: Bảng Hỗ trợ & Báo cáo từ Nhân viên -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2">MỚI NHẤT</span>
                        <h6 class="mb-0 fw-bold">Hỗ trợ & Báo cáo cần xử lý</h6>
                    </div>
                    <a href="{{ route('support.index') }}" class="text-decoration-none small fw-bold">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small text-uppercase text-muted">
                            <tr>
                                <th class="ps-4">Nhân viên</th>
                                <th>Vấn đề / Yêu cầu</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTickets as $ticket)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @if($ticket->user->avatar)
                                            <img src="{{ asset('storage/' . $ticket->user->avatar) }}" class="rounded-circle me-2" width="35" height="35" style="object-fit: cover;">
                                        @else
                                            <div class="avatar-circle bg-primary me-2" style="width:35px;height:35px;font-size:12px;">{{ substr($ticket->user->hoTen ?? 'U', 0, 1) }}</div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $ticket->user->hoTen }}</div>
                                            <div class="small text-muted">{{ $ticket->user->phongBan }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $ticket->subject }}</span>
                                    <br>
                                    <span class="small text-muted text-truncate" style="max-width: 250px; display: inline-block;">#ID: {{ $ticket->id }} - {{ $ticket->type }}</span>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $ticket->created_at->diffForHumans() }}</span>
                                </td>
                                <td><span class="badge bg-warning text-dark">Đang xử lý</span></td>
                                <td class="text-end pe-4">
                                    <a href="#" class="btn btn-sm btn-light border me-1 text-primary"><i class="fa-solid fa-comment-dots"></i> Chat</a>
                                    <button class="btn btn-sm btn-light border text-success"><i class="fa-solid fa-check"></i> Xong</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Không có yêu cầu hỗ trợ nào mới.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cột Phải: Widget Tab (Thông báo / Cấp tài khoản) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <ul class="nav nav-tabs card-header-tabs" id="adminTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small text-dark" id="notify-tab" data-bs-toggle="tab" data-bs-target="#notify" type="button" role="tab"><i class="fa-solid fa-bullhorn me-2"></i>Thông báo</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <!-- Active tab Cấp tài khoản lên trước để bạn dễ nhìn -->
                            <button class="nav-link active fw-bold small text-primary" id="account-tab" data-bs-toggle="tab" data-bs-target="#account" type="button" role="tab"><i class="fa-solid fa-user-plus me-2"></i>Cấp tài khoản</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="adminTabsContent">
                        
                        <!-- TAB 1: GỬI THÔNG BÁO (Ẩn) -->
                        <div class="tab-pane fade" id="notify" role="tabpanel">
                            <p class="text-muted small mb-3">Gửi thông báo đến ứng dụng của toàn bộ nhân viên.</p>
                            <form>
                                <div class="mb-2">
                                    <input type="text" class="form-control form-control-sm" placeholder="Tiêu đề (Vd: Bảo trì hệ thống)">
                                </div>
                                <div class="mb-2">
                                    <textarea class="form-control form-control-sm" rows="3" placeholder="Nội dung thông báo..."></textarea>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm w-100 fw-bold"><i class="fa-solid fa-paper-plane me-2"></i>Phát thông báo</button>
                            </form>
                        </div>

                        <!-- TAB 2: CẤP TÀI KHOẢN -->
                        <div class="tab-pane fade show active" id="account" role="tabpanel">
                            <p class="text-muted small mb-3">Tạo tài khoản nhân viên mới vào hệ thống.</p>
                            <form> <!-- Form tĩnh, chưa có action -->
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold mb-1">Họ và tên</label>
                                        <input type="text" class="form-control form-control-sm" placeholder="Nhập họ tên...">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold mb-1">Mã Nhân viên</label>
                                        <input type="text" class="form-control form-control-sm" placeholder="Vd: NV001">
                                    </div>
                                </div>
                                
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold mb-1">Email</label>
                                        <input type="email" class="form-control form-control-sm" placeholder="email@company.com">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold mb-1">Số điện thoại</label>
                                        <input type="text" class="form-control form-control-sm" placeholder="09xxxxxxx">
                                    </div>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold mb-1">Phòng ban</label>
                                        <select class="form-select form-select-sm">
                                            <option selected disabled>-- Chọn --</option>
                                            <option>Kinh doanh</option>
                                            <option>Kỹ thuật</option>
                                            <option>Nhân sự</option>
                                            <option>Hành chính</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold mb-1">Vai trò</label>
                                        <select class="form-select form-select-sm">
                                            <option value="NhanVien">Nhân viên</option>
                                            <option value="admin">Quản trị viên</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold mb-1">Mật khẩu mặc định</label>
                                    <input type="text" class="form-control form-control-sm bg-light" value="123456" readonly>
                                    <div class="form-text small" style="font-size: 10px;">Nhân viên sẽ được yêu cầu đổi mật khẩu sau khi đăng nhập.</div>
                                </div>

                                <button type="button" class="btn btn-success btn-sm w-100 fw-bold py-2">
                                    <i class="fa-solid fa-user-plus me-2"></i>Tạo tài khoản
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection