@extends('layouts.admin')
@section('title', 'Quản lý Mượn Trả')
@section('header_title', 'Điều phối Mượn & Trả')

@section('content')
<div class="container-fluid p-4">

    <!-- TAB MENU -->
    <ul class="nav nav-tabs mb-4" id="borrowTabs" role="tablist">
        <!-- Tab 1: Chờ duyệt -->
        <li class="nav-item">
            <button class="nav-link active fw-bold py-2" data-bs-toggle="tab" data-bs-target="#tabPending">
                ⏳ Yêu cầu mới <span class="badge bg-warning text-dark ms-1">{{ $pending->count() }}</span>
            </button>
        </li>
        <!-- Tab 2: Đang mượn / Chờ trả -->
        <li class="nav-item">
            <button class="nav-link fw-bold py-2" data-bs-toggle="tab" data-bs-target="#tabOngoing">
                🚀 Đang hoạt động <span class="badge bg-primary ms-1">{{ $ongoing->count() }}</span>
            </button>
        </li>
        <!-- Tab 3: Lịch sử -->
        <li class="nav-item">
            <button class="nav-link fw-bold py-2 text-muted" data-bs-toggle="tab" data-bs-target="#tabHistory">
                📜 Lịch sử
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        <!-- === TAB 1: CHỜ DUYỆT === -->
        <div class="tab-pane fade show active" id="tabPending">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Người yêu cầu</th>
                                <th>Thiết bị</th>
                                <th>Lý do & Thời gian</th>
                                <th class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pending as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $item->user->hoTen ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $item->user->email ?? '' }}</small>
                                </td>
                                <td>
                                    @foreach($item->chiTietMuon as $ct)
                                        <div class="mb-1">
                                            <i class="fa-solid fa-laptop text-secondary me-1"></i> 
                                            {{ $ct->thietBi->tenTB ?? 'Unknown' }} 
                                            <span class="badge bg-light text-dark border ms-1">{{ $ct->thietBi->maTB ?? '---' }}</span>
                                            @if(($ct->thietBi->tinhTrang ?? '') != 'Available')
                                                <span class="badge bg-danger">Máy đang bận!</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    <div class="small mb-1">📅 {{ date('d/m/Y', strtotime($item->ngayMuon)) }} <i class="fa-solid fa-arrow-right mx-1 text-muted"></i> {{ date('d/m/Y', strtotime($item->ngayTraDuKien)) }}</div>
                                    <div class="text-muted small fst-italic">"{{ $item->ghiChu }}"</div>
                                </td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('admin.borrow.approve', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm fw-bold shadow-sm" title="Duyệt đơn này">
                                            <i class="fa-solid fa-check"></i> Duyệt
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.borrow.reject', $item->id) }}" method="POST" class="d-inline ms-1">
                                        @csrf
                                        <button class="btn btn-outline-danger btn-sm border-0" onclick="return confirm('Bạn chắc chắn muốn từ chối yêu cầu này?')" title="Từ chối">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted"><i class="fa-solid fa-clipboard-check fa-2x mb-2"></i><br>Không có yêu cầu mới.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- === TAB 2: ĐANG MƯỢN / CHỜ TRẢ === -->
        <div class="tab-pane fade" id="tabOngoing">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Trạng thái</th>
                                <th>Người mượn</th>
                                <th>Thiết bị</th>
                                <th>Hạn trả</th>
                                <th class="text-end pe-4">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ongoing as $item)
                            <tr class="{{ $item->trangThai == 'Waiting_Return' ? 'bg-warning bg-opacity-10' : '' }}">
                                <td class="ps-4">
                                    @if($item->trangThai == 'Waiting_Return')
                                        <span class="badge bg-warning text-dark border border-warning">
                                            <i class="fa-solid fa-bell fa-shake me-1"></i> Yêu cầu trả
                                        </span>
                                    @else
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">Đang mượn</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $item->user->hoTen ?? 'N/A' }}</div>
                                    <div class="small text-muted">{{ $item->user->email ?? '' }}</div>
                                </td>
                                <td>
                                    @foreach($item->chiTietMuon as $ct)
                                        <div>
                                            {{ $ct->thietBi->tenTB ?? 'Unknown' }} 
                                            <small class="text-muted">({{ $ct->thietBi->maTB ?? '---' }})</small>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @if(\Carbon\Carbon::parse($item->ngayTraDuKien)->isPast())
                                        <span class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> Quá hạn</span>
                                        <div class="small text-danger">{{ date('d/m/Y', strtotime($item->ngayTraDuKien)) }}</div>
                                    @else
                                        <span class="text-success">{{ date('d/m/Y', strtotime($item->ngayTraDuKien)) }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-primary btn-sm shadow-sm btn-return" 
                                            data-id="{{ $item->id }}"
                                            data-username="{{ $item->user->hoTen ?? 'N/A' }}">
                                        <i class="fa-solid fa-file-signature me-1"></i> Nhận trả
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fa-solid fa-box-open fa-2x mb-2"></i><br>Không có thiết bị nào đang mượn.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- === TAB 3: LỊCH SỬ === -->
        <div class="tab-pane fade" id="tabHistory">
            
            <!-- FORM TÌM KIẾM & LỌC (SỬ DỤNG GET REQUEST) -->
            <form method="GET" action="{{ url()->current() }}" class="row g-2 mb-3 align-items-center">
                <!-- Giữ lại tham số tab active nếu cần xử lý bằng JS phụ -->
                <input type="hidden" name="tab" value="history">

                <div class="col-md-4">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control border-start-0 ps-0" 
                               value="{{ request('keyword') }}" 
                               placeholder="Tìm tên người, thiết bị...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select shadow-sm" onchange="this.form.submit()">
                        <option value="">📁 Tất cả trạng thái</option>
                        <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>✅ Đã trả</option>
                        <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>🚫 Đã từ chối</option>
                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>⚪ Đã hủy</option>
                    </select>
                </div>
               
                <div class="col-md-3 text-end text-muted small pt-2">
                    Tổng cộng: <strong>{{ $history->total() }}</strong> bản ghi
                </div>
            </form>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Ngày cập nhật</th>
                                <th>Người mượn</th>
                                <th>Thiết bị</th>
                                <th>Kết quả</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $item)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $item->updated_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold">{{ $item->user->hoTen ?? 'N/A' }}</td>
                                <td>
                                    @foreach($item->chiTietMuon as $ct)
                                        <div class="small">{{ $ct->thietBi->tenTB ?? 'Unknown' }}</div>
                                    @endforeach
                                </td>
                                <td>
                                    @if($item->trangThai == 'Closed')
                                        <span class="badge bg-success"><i class="fa-solid fa-check"></i> Đã trả</span>
                                    @elseif($item->trangThai == 'Rejected')
                                        <span class="badge bg-secondary"><i class="fa-solid fa-ban"></i> Đã từ chối</span>
                                    @elseif($item->trangThai == 'Cancelled')
                                        <span class="badge bg-light text-muted border">Đã hủy</span>
                                    @else
                                        <span class="badge bg-light text-dark border">{{ $item->trangThai }}</span>
                                    @endif
                                </td>
                                <td class="small text-muted fst-italic text-truncate" style="max-width: 200px;">
                                    {{ $item->ghiChu }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted border-top">
                                    <i class="fa-solid fa-magnifying-glass mb-2"></i><br>Không tìm thấy kết quả nào phù hợp.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PHÂN TRANG LARAVEL -->
                <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-center">
                    <!-- appends(request()->query()) giúp giữ lại các tham số tìm kiếm khi chuyển trang -->
                    {{ $history->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL NHẬN TRẢ -->
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-box-open me-2 text-primary"></i>Xác nhận nhận trả thiết bị</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="alert alert-info py-2 small mb-3 border-0 bg-opacity-10 bg-info text-info">
                    <i class="fa-solid fa-user me-1"></i> Người mượn: <strong id="returnUserName">...</strong>
                </div>
                <form id="returnForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tình trạng máy sau khi kiểm tra <span class="text-danger">*</span></label>
                        <select name="condition" class="form-select border-primary" required>
                            <option value="Normal">✅ Nguyên vẹn (Nhập lại kho)</option>
                            <option value="Broken">🛠️ Hư hỏng (Chuyển đi bảo trì)</option>
                            <option value="Lost">❌ Mất thất lạc (Thanh lý/Đền bù)</option>
                        </select>
                        <div class="form-text small">
                            Lưu ý: Nếu chọn "Hư hỏng", thiết bị sẽ tự động được chuyển sang kho bảo trì.
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Ghi chú của Admin</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Vd: Máy xước nhẹ, phụ kiện đầy đủ, pin còn 80%..."></textarea>
                    </div>
                    <div class="text-end border-top pt-3">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="fa-solid fa-check me-1"></i> Xác nhận Đã Trả
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TOAST & CSS -->
@if(session('success'))
<div class="toast-notification success"><i class="fa-solid fa-check-circle me-1"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="toast-notification error"><i class="fa-solid fa-triangle-exclamation me-1"></i> {{ session('error') }}</div>
@endif

<style>
    .toast-notification { position: fixed; top: 20px; right: 20px; z-index: 10000; padding: 15px 25px; border-radius: 8px; color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideIn 0.3s; }
    .toast-notification.success { background: #198754; }
    .toast-notification.error { background: #dc3545; }
    @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tự động ẩn thông báo sau 4s
        setTimeout(() => { document.querySelectorAll('.toast-notification').forEach(el => el.style.display='none'); }, 4000);

        // Bắt sự kiện click cho nút "Nhận trả"
        const returnButtons = document.querySelectorAll('.btn-return');
        returnButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const userName = this.getAttribute('data-username');
                openReturnModal(id, userName);
            });
        });

        // Giữ tab Lịch sử luôn active nếu có tham số 'tab=history' trên URL (khi tìm kiếm xong)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'history' || urlParams.get('page')) {
            const triggerEl = document.querySelector('button[data-bs-target="#tabHistory"]');
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
        }
    });

    function openReturnModal(id, userName) {
        document.getElementById('returnUserName').innerText = userName;
        document.getElementById('returnForm').action = "/admin/borrow/return/" + id;
        new bootstrap.Modal(document.getElementById('returnModal')).show();
    }
</script>
@endsection