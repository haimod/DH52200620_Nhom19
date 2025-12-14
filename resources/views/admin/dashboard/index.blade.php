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
        
        /* Toast notification */
        .toast-notification { position: fixed; top: 20px; right: 20px; z-index: 10000; padding: 15px 25px; border-radius: 8px; color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideIn 0.3s; }
        .toast-notification.success { background: #198754; }
        .toast-notification.error { background: #dc3545; }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }

        /* Clickable row */
        .clickable-row { cursor: pointer; transition: background-color 0.1s; }
        .clickable-row:hover { background-color: #f8f9fa; }
        
        /* Custom pagination size */
        .pagination { justify-content: center; margin-bottom: 0; }
        .page-item .page-link { font-size: 0.85rem; padding: 0.25rem 0.5rem; }
    </style>

    <!-- Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0">Dashboard Quản trị</h2>
            <p class="text-muted mb-0 mt-1">
                Chào Admin! Hệ thống có <span class="badge bg-danger rounded-pill">{{ $stats['open_tickets'] ?? 0 }} tin nhắn hỗ trợ</span> đang chờ xử lý.
            </p>
        </div>
    </div>

    <!-- 1. THẺ THỐNG KÊ (Hàng trên cùng) -->
    <div class="row g-4 mb-4">
        <!-- Tổng Users -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase text-muted small fw-bold">Tổng nhân sự</span>
                        <i class="fa-solid fa-users text-primary opacity-50"></i>
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
        <!-- Tổng Tài sản -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase text-muted small fw-bold">Tổng thiết bị</span>
                        <i class="fa-solid fa-laptop text-success opacity-50"></i>
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
        <!-- Mượn/Trả -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase text-muted small fw-bold">Yêu cầu Mượn</span>
                        <i class="fa-solid fa-file-signature text-warning opacity-50"></i>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="fw-bold text-dark mb-0">{{ $stats['pending_borrows'] ?? 0 }}</h2>
                            <small class="text-warning fw-bold">Đang chờ duyệt</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                            <i class="fa-solid fa-file-signature fa-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hỗ trợ -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card border-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase text-muted small fw-bold">Yêu cầu Hỗ trợ</span>
                        <i class="fa-solid fa-headset text-secondary opacity-50"></i>
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

    <!-- 2. HÀNG GIỮA -->
    <div class="row g-4 mb-4">
        <!-- Cột Trái: Bảng Hỗ trợ -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary">Hỗ trợ cần xử lý</h6>
                    <a href="{{ route('admin.support.index') }}" class="text-decoration-none small fw-bold">Xem tất cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small text-uppercase text-muted">
                            <tr>
                                <th class="ps-4">Nhân viên</th>
                                <th>Vấn đề</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTickets ?? [] as $ticket)
                            {{-- Sử dụng data-href thay vì onclick để tránh lỗi cú pháp --}}
                            <tr class="clickable-row" data-href="{{ route('admin.support.show', $ticket->id) }}">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        {{-- Kiểm tra kỹ ticket->user --}}
                                        @if($ticket->user)
                                            @if($ticket->user->avatar)
                                                <img src="{{ asset('storage/' . $ticket->user->avatar) }}" class="rounded-circle me-2" width="35" height="35" style="object-fit: cover;">
                                            @else
                                                <div class="avatar-circle bg-primary me-2">{{ substr($ticket->user->hoTen ?? 'U', 0, 1) }}</div>
                                            @endif
                                            <div>
                                                <div class="fw-bold small">{{ $ticket->user->hoTen }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem">{{ $ticket->user->phongBan }}</div>
                                            </div>
                                        @else
                                            <div class="avatar-circle bg-secondary me-2">?</div>
                                            <div>
                                                <div class="fw-bold small text-muted">Người dùng đã xóa</div>
                                                <div class="text-muted" style="font-size: 0.75rem">---</div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td><span class="fw-bold text-dark small">{{ Str::limit($ticket->subject, 40) }}</span></td>
                                <td><span class="text-muted small" style="font-size: 0.75rem">{{ $ticket->created_at->diffForHumans() }}</span></td>
                                <td>
                                    @if($ticket->status == 'pending')
                                        <span class="badge bg-warning text-dark" style="font-size: 0.7rem">Chờ xử lý</span>
                                    @else
                                        <span class="badge bg-info text-dark" style="font-size: 0.7rem">Đang xử lý</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted small">Không có yêu cầu nào mới.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white py-2 border-top-0">
                     @if(isset($recentTickets) && method_exists($recentTickets, 'links'))
                         {{ $recentTickets->links() }}
                     @endif
                </div>
            </div>
        </div>

        <!-- Cột Phải: Gửi thông báo -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-paper-plane me-2 text-primary"></i>Gửi thông báo</h6>
                </div>
                <div class="card-body">
                    {{-- SỬA LỖI: Cập nhật route thành admin.notify.send --}}
                    <form action="{{ route('admin.notify.send') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            {{-- Thêm name="title" --}}
                            <input type="text" name="title" class="form-control form-control-sm" placeholder="Tiêu đề..." required>
                        </div>
                        <div class="mb-2">
                            {{-- Thêm name="content" --}}
                            <textarea name="content" class="form-control form-control-sm" rows="3" placeholder="Nội dung..." required></textarea>
                        </div>
                        {{-- Đổi type="submit" --}}
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">Gửi ngay</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. HÀNG DƯỚI: THỐNG KÊ TÀI SẢN -->
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-chart-pie me-2 text-success"></i>Tình trạng Tài sản</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-5 align-items-center">
                        <div class="col-md-5 border-end text-center">
                            <div class="d-flex justify-content-around mb-4">
                                <div><h2 class="fw-bold text-warning mb-0">{{ $stats['in_use'] ?? 0 }}</h2><small class="text-muted fw-bold">ĐANG MƯỢN</small></div>
                                <div><h2 class="fw-bold text-success mb-0">{{ $stats['available'] ?? 0 }}</h2><small class="text-muted fw-bold">TRONG KHO</small></div>
                            </div>
                            <!-- Thanh tỉ lệ (Sử dụng @style để fix lỗi IDE báo đỏ) -->
                            <div class="progress" style="height: 10px;">
                                @php
                                    $total = ($stats['total_devices'] ?? 0) > 0 ? $stats['total_devices'] : 1;
                                    $usePercent = ($stats['in_use'] / $total) * 100;
                                    $availPercent = ($stats['available'] / $total) * 100;
                                    $brokenPercent = ($stats['broken'] / $total) * 100;
                                @endphp
                                <div class="progress-bar bg-warning" role="progressbar" @style(['width: ' . $usePercent . '%']) title="Đang mượn"></div>
                                <div class="progress-bar bg-success" role="progressbar" @style(['width: ' . $availPercent . '%']) title="Sẵn sàng"></div>
                                <div class="progress-bar bg-danger" role="progressbar" @style(['width: ' . $brokenPercent . '%']) title="Hỏng"></div>
                            </div>
                            <div class="mt-2 text-center small text-muted">
                                <span class="me-3"><i class="fa-solid fa-circle text-warning small me-1"></i>Đang mượn</span>
                                <span class="me-3"><i class="fa-solid fa-circle text-success small me-1"></i>Trong kho</span>
                                <span><i class="fa-solid fa-circle text-danger small me-1"></i>Hỏng ({{ $stats['broken'] ?? 0 }})</span>
                            </div>
                        </div>
                        <div class="col-md-7 ps-md-5">
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <h6 class="text-muted small fw-bold mb-3">HOẠT ĐỘNG</h6>
                                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Hôm nay</span><span class="fw-bold">{{ $activity['today'] ?? 0 }}</span></div>
                                    <div class="d-flex justify-content-between"><span class="text-muted">Tháng này</span><span class="fw-bold">{{ $activity['month'] ?? 0 }}</span></div>
                                </div>
                                <div class="col-sm-6">
                                    <h6 class="text-muted small fw-bold mb-3 text-danger">CẢNH BÁO</h6>
                                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Sắp trễ</span><span class="badge bg-warning text-dark">{{ isset($nearDeadline) ? $nearDeadline->count() : 0 }}</span></div>
                                    <div class="d-flex justify-content-between"><span class="text-danger">Quá hạn</span><span class="badge bg-danger">{{ isset($overdue) ? $overdue->count() : 0 }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TOAST -->
    @if(session('success')) <div class="toast-notification success"><i class="fa-solid fa-check-circle me-1"></i> {{ session('success') }}</div> @endif
    @if(session('error')) <div class="toast-notification error"><i class="fa-solid fa-triangle-exclamation me-1"></i> {{ session('error') }}</div> @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => { document.querySelectorAll('.toast-notification').forEach(el => el.style.display='none'); }, 4000);
        
        // Xử lý click dòng bảng an toàn
        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('a') && !e.target.closest('button')) {
                    window.location.href = this.dataset.href;
                }
            });
        });
    });
</script>
@endsection