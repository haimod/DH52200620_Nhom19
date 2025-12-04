@extends('layouts.main')

@section('title', 'Trang chủ')

@section('content')
<div class="main-content">

    <!-- Header -->
    <div class="page-header mb-4">
        <h1>Trang chủ</h1>
    </div>

    <!-- Profile Card -->
    <div class="profile-header-card mb-4">
        <div class="d-flex align-items-center">
            <div class="profile-avatar">
                {{ substr(Auth::user()->hoTen ?? 'U', 0, 1) }}
            </div>
            <div class="profile-info flex-grow-1 ms-3">
                <h2 class="profile-name">{{ Auth::user()->hoTen ?? 'Chưa cập nhật tên' }}</h2>
                <div class="d-flex align-items-center gap-3 mb-2 flex-wrap">
                    <span class="meta-text"><span class="fw-bold text-secondary">Mã NV:</span> <span class="text-primary fw-bold">{{ Auth::user()->maNV ?? '---' }}</span></span>
                    <span class="meta-text"><span class="fw-bold text-secondary">Phòng ban:</span> <span class="text-dark fw-bold">{{ Auth::user()->phongBan ?? '---' }}</span></span>
                    <span class="role-badge">{{ Auth::user()->vaiTro ?? 'Thực tập sinh' }}</span>
                </div>
                <div class="d-flex align-items-center gap-4 text-muted small">
                    <span><i class="fa-regular fa-envelope me-1"></i> {{ Auth::user()->email }}</span>
                    <span><i class="fa-solid fa-phone me-1"></i> {{ Auth::user()->soDienThoai }}</span>
                </div>
            </div>
            <div class="profile-status-badge ms-3">
                <i class="fa-solid fa-circle-check me-1"></i> Đang hoạt động
            </div>
        </div>
    </div>

    <!-- Thiết bị Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h2 class="mb-2">Danh sách thiết bị</h2>
            <form action="/" method="GET" class="d-flex gap-2 flex-wrap">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="all">-- Tất cả --</option>
                    <option value="Available" {{ $status == 'Available' ? 'selected' : '' }}>Khả dụng</option>
                    <option value="In_Use" {{ $status == 'In_Use' ? 'selected' : '' }}>Đang mượn</option>
                    <option value="Maintenance" {{ $status == 'Maintenance' ? 'selected' : '' }}>Bảo trì</option>
                    <option value="Broken" {{ $status == 'Broken' ? 'selected' : '' }}>Hỏng</option>
                </select>
                <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Nhập mã hoặc tên...">
                <button type="submit" class="btn btn-primary">Tìm</button>
            </form>
        </div>

        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã TB</th>
                        <th>Tên thiết bị</th>
                        <th>Loại</th>
                        <th>Phòng</th>
                        <th>Tình trạng</th>
                        <th>Hạn bảo hành</th>
                        <th style="width: 160px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($thietbi as $tb)
                    <tr>
                        <td>{{ $tb->maTB }}</td>
                        <td>{{ $tb->tenTB }}</td>
                        <td>{{ $tb->maLoai }}</td>
                        <td>{{ $tb->maPhong ?? 'Kho' }}</td>
                        <td>
                            @if($tb->tinhTrang == 'Available')
                                <span class="badge bg-success">Khả dụng</span>
                            @elseif($tb->tinhTrang == 'In_Use')
                                <span class="badge bg-warning text-dark">Đang mượn</span>
                            @elseif($tb->tinhTrang == 'Maintenance')
                                <span class="badge bg-info text-dark">Bảo trì</span>
                            @else
                                <span class="badge bg-danger">{{ $tb->tinhTrang }}</span>
                            @endif
                        </td>
                        <td>{{ $tb->hanBaoHanh ? \Carbon\Carbon::parse($tb->hanBaoHanh)->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if($tb->tinhTrang == 'Available')
                                <button class="btn btn-primary btn-sm w-100 borrow-btn" data-matb="{{ $tb->maTB }}" data-tentb="{{ $tb->tenTB }}">
                                    <i class="fa-solid fa-clock"></i> Mượn
                                </button>
                            @elseif($tb->tinhTrang == 'In_Use')
                                @php
                                    $phieu = $tb->muonMoiNhat->phieuMuon ?? null;
                                    $ngayTra = $phieu ? \Carbon\Carbon::parse($phieu->ngayTraDuKien)->format('H:i d/m/Y') : 'Chưa rõ';
                                    $nguoiMuon = $phieu && $phieu->user ? $phieu->user->hoTen : 'Ai đó';
                                @endphp
                                <button class="btn btn-warning btn-sm text-dark w-100 info-btn" data-tentb="{{ $tb->tenTB }}" data-nguoimuon="{{ $nguoiMuon }}" data-ngaytra="{{ $ngayTra }}">
                                    <i class="fa-regular fa-calendar-check"></i> Xem lịch
                                </button>
                            @else
                                <button class="btn btn-secondary btn-sm w-100" disabled>Hỏng/Bảo trì</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">Chưa có thiết bị nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $thietbi->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div id="toast-success" class="toast-toast">
    <i class="fa-solid fa-circle-check"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Borrow Modal -->
<div id="borrowModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h4 class="mb-3 text-primary"><i class="fa-regular fa-clock"></i> Mượn Thiết Bị</h4>
        
        <div class="alert alert-info">
            Bạn đang chọn mượn: <strong id="modalTenTB"></strong>
        </div>

        <form action="{{ route('borrow.store') }}" method="POST">
            @csrf
            <input type="hidden" name="thietbi" id="modalMaTB">
            
            <div class="mb-3">
                <label class="fw-bold">Thời gian mượn:</label>
                <input type="datetime-local" name="ngayMuon" value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Dự kiến trả:</label>
                <input type="datetime-local" name="ngayTraDuKien" value="{{ \Carbon\Carbon::now()->addHours(4)->format('Y-m-d\TH:i') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Ghi chú:</label>
                <textarea name="lyDo" class="form-control" placeholder="Mục đích sử dụng..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Xác nhận mượn</button>
        </form>
    </div>
</div>

<!-- Info Modal -->
<div id="infoModal" class="modal">
    <div class="modal-content text-center">
        <span class="close">&times;</span>
        <div class="mb-2 text-warning" style="font-size: 40px;"><i class="fa-solid fa-hourglass-half"></i></div>
        <h4 class="mb-3">Đang sử dụng</h4>

        <div class="alert alert-light border text-start">
            <p class="mb-1">💻 <strong><span id="infoTenTB"></span></strong></p>
            <hr class="my-2">
            <p class="mb-1">👤 Người mượn: <strong class="text-primary" id="infoNguoiMuon"></strong></p>
        </div>

        <div class="p-3 rounded bg-warning-subtle text-warning-emphasis border border-warning">
            <p class="mb-0 fw-bold small text-uppercase">Dự kiến trả lúc</p>
            <h3 class="mb-0 fw-bold" id="infoNgayTra"></h3>
        </div>

        <button class="btn btn-secondary w-100 mt-3">Đóng</button>
    </div>
</div>



@section('scripts')
<script src="{{ asset('js/home.js') }}"></script>
@endsection

@endsection
