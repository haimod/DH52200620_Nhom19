@extends('layouts.main')
@section('title', 'Trả & Hủy lịch')

@section('content')
<div class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">Danh sách Mượn & Đặt trước</h3>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="" method="GET" class="row g-2">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="keyword" class="form-control" 
                               placeholder="Tìm theo mã phiếu, tên máy..." 
                               value="{{ request('keyword') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="type" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="active" {{ request('type') == 'active' ? 'selected' : '' }}>🟢 Đang mượn (Active)</option>
                        <option value="pending" {{ request('type') == 'pending' ? 'selected' : '' }}>📅 Đặt trước (Pending)</option>
                        <option value="qua_han" {{ request('type') == 'qua_han' ? 'selected' : '' }}>⚠️ Quá hạn</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã Phiếu</th>
                        <th>Thiết bị</th>
                        <th>Thời gian mượn</th>
                        <th>Hạn trả</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dangMuon as $item)
                        @php
                            $ngayTra = \Carbon\Carbon::parse($item->ngayTraDuKien);
                            $ngayMuon = \Carbon\Carbon::parse($item->ngayMuon);
                            
                            // Check quá hạn (Chỉ tính nếu đang Active)
                            $isQuaHan = $item->trangThai == 'Active' && \Carbon\Carbon::now() > $ngayTra;
                            
                            // Link xử lý (Cần 2 route khác nhau: Trả và Hủy)
                            // Giả sử bạn dùng chung route delete hoặc update status
                            $linkAction = route('return.action', $item->maPM); 
                        @endphp

                        <tr>
                            <td class="fw-bold text-secondary">#{{ $item->maPM }}</td>
                            
                            <td>
                                @foreach($item->chiTietMuon as $ct)
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-laptop me-2 text-muted"></i>
                                        <div>
                                            <div class="fw-bold">{{ $ct->thietbi->tenTB }}</div>
                                            <div class="small text-muted">{{ $ct->maTB }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </td>
                            
                            <td>
                                {{ $ngayMuon->format('H:i d/m/Y') }}
                                @if($item->trangThai == 'Pending')
                                    <br><small class="text-primary fst-italic">(Dự kiến)</small>
                                @endif
                            </td>
                            
                            <td class="{{ $isQuaHan ? 'text-danger fw-bold' : '' }}">
                                {{ $ngayTra->format('H:i d/m/Y') }}
                            </td>

                            <td>
                                @if($item->trangThai == 'Pending')
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill">
                                        <i class="fa-regular fa-calendar-check me-1"></i> Đã đặt trước
                                    </span>
                                @elseif($isQuaHan)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i> Quá hạn
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="fa-solid fa-hand-holding me-1"></i> Đang mượn
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($item->trangThai == 'Pending')
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="hienModal('{{ $item->maPM }}', '{{ $linkAction }}', 'cancel')">
                                        <i class="fa-solid fa-xmark"></i> Hủy đặt
                                    </button>
                                @else
                                    <button type="button" class="btn btn-warning btn-sm fw-bold"
                                            onclick="hienModal('{{ $item->maPM }}', '{{ $linkAction }}', 'return')">
                                        <i class="fa-solid fa-rotate-left"></i> Trả đồ
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open fa-2x mb-2"></i>
                                <p>Không có phiếu mượn nào.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-center">
                {{ $dangMuon->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<div id="modalXacNhan" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 400px; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); animation: fadeIn 0.2s;">
        
        <h4 id="modalTitle" class="fw-bold mb-3">Xác nhận</h4>
        <p id="modalContent">Bạn có chắc muốn thực hiện hành động này với phiếu <b id="textMaPhieu" class="text-primary"></b>?</p>
        
        <form id="formAction" method="POST" action="">
            @csrf
            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-secondary" onclick="dongModal()">Đóng</button>
                <button type="submit" id="modalBtnConfirm" class="btn btn-primary">Xác nhận</button>
            </div>
        </form>

    </div>
</div>

@if(session('success'))
    <div id="thongBao" class="toast-notification success">
        <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div id="thongBaoLoi" class="toast-notification error">
        <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
    </div>
@endif

<style>
    /* CSS nhanh cho Toast */
    .toast-notification { position: fixed; top: 20px; right: 20px; padding: 15px 25px; border-radius: 8px; color: #fff; z-index: 10000; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideIn 0.3s; }
    .toast-notification.success { background-color: #198754; }
    .toast-notification.error { background-color: #dc3545; }
    @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
</style>

<script>
    // Hàm hiện Modal thông minh (Xử lý cả 2 trường hợp)
    function hienModal(maPhieu, linkAction, type) {
        let title = document.getElementById('modalTitle');
        let content = document.getElementById('modalContent');
        let btn = document.getElementById('modalBtnConfirm');
        let textMa = document.getElementById('textMaPhieu');

        // Reset text
        textMa.innerText = '#' + maPhieu;
        document.getElementById('formAction').action = linkAction;

        if (type === 'return') {
            title.innerText = "Trả thiết bị";
            title.className = "fw-bold mb-3 text-warning";
            content.innerHTML = `Bạn xác nhận đã trả xong thiết bị của phiếu <b class="text-primary">#${maPhieu}</b>?`;
            btn.className = "btn btn-warning fw-bold";
            btn.innerText = "Đã trả xong";
        } else {
            title.innerText = "Hủy lịch hẹn";
            title.className = "fw-bold mb-3 text-danger";
            content.innerHTML = `Bạn có chắc muốn HỦY lịch đặt trước <b class="text-primary">#${maPhieu}</b> không?`;
            btn.className = "btn btn-danger fw-bold";
            btn.innerText = "Hủy đặt";
        }

        // Hiện modal (dùng flex để căn giữa)
        document.getElementById('modalXacNhan').style.display = 'flex';
    }

    function dongModal() {
        document.getElementById('modalXacNhan').style.display = 'none';
    }

    window.onclick = function(event) {
        let modal = document.getElementById('modalXacNhan');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Auto hide toast
    setTimeout(() => {
        let t1 = document.getElementById('thongBao');
        let t2 = document.getElementById('thongBaoLoi');
        if(t1) t1.style.display = 'none';
        if(t2) t2.style.display = 'none';
    }, 3000);
</script>

@endsection