@extends('layouts.main')

@section('title', 'Lịch sử mượn')

@section('content')
<div class="main-content">
    <div class="page-header mb-4 d-flex justify-content-between align-items-center">
        <h1>📜 Lịch sử mượn thiết bị</h1>
    </div>

    {{-- ========================================================== --}}
    {{-- 1. PHẦN MỚI THÊM: THANH TÌM KIẾM & THỐNG KÊ --}}
    {{-- ========================================================== --}}
    <div class="row mb-3">
        <div class="col-md-5 d-flex align-items-center gap-2">
            <span class="badge bg-primary p-2">Đang mượn: {{ $danhSach->where('trangThai', 'Active')->count() }}</span>
            <span class="badge bg-success p-2">Đã trả: {{ $danhSach->where('trangThai', 'Closed')->count() }}</span>
        </div>

        <div class="col-md-7">
            <form action="" method="GET" class="d-flex gap-2 justify-content-end">
                <select name="status" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                    <option value="">-- Trạng thái --</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Đang mượn</option>
                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Đã trả</option>
                </select>
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm tên thiết bị..." value="{{ request('keyword') }}">
                    <button class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
        </div>
    </div>
    {{-- ========================================================== --}}


    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã Phiếu</th>
                        <th>Thiết bị mượn</th>
                        <th>Trạng thái</th>
                        <th>Ghi chú</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhSach as $phieu)
                   
                    @php
                        $isQuaHan = false;
                        // Nếu đang mượn (Active) VÀ Thời gian hiện tại > Ngày trả dự kiến
                        if($phieu->trangThai == 'Active' && \Carbon\Carbon::now() > \Carbon\Carbon::parse($phieu->ngayTraDuKien)){
                            $isQuaHan = true;
                        }
                    @endphp

                    {{-- Thêm class 'table-danger' (màu đỏ nhạt) nếu quá hạn --}}
                    <tr class="{{ $isQuaHan ? 'table-danger' : '' }}">
                        <td>
                            <span class="fw-bold">#{{ $phieu->maPM }}</span>
                            {{-- Hiện badge cảnh báo nếu quá hạn --}}
                            @if($isQuaHan)
                                <div class="badge bg-danger mt-1" style="font-size: 10px;">Quá hạn</div>
                            @endif
                        </td>

                        <td>
                            @foreach($phieu->chiTietMuon as $ct)
                                <div class="badge bg-light text-dark border mb-1">
                                    {{ $ct->thietbi->tenTB ?? 'TB đã xóa' }} 
                                    <small class="text-muted">({{ $ct->maTB }})</small>
                                </div><br>
                            @endforeach
                        </td>

                        <td>
                            @if($phieu->trangThai == 'Active')
                                <span class="badge bg-primary">Đang mượn</span>
                            @elseif($phieu->trangThai == 'Closed')
                                <span class="badge bg-success">Đã trả</span>
                            @else
                                <span class="badge bg-secondary">{{ $phieu->trangThai }}</span>
                            @endif
                        </td>

                        <td>{{ $phieu->ghiChu ?? '-' }}</td>

                        {{-- NÚT CHI TIẾT (GIỮ NGUYÊN CODE CŨ CỦA BẠN) --}}
                        <td>
                            <button 
                                class="btn btn-sm btn-info text-white"
                                onclick="openModalFromButton(this)"

                                data-ma="{{ $phieu->maPM }}"
                                data-muon="{{ \Carbon\Carbon::parse($phieu->ngayMuon)->format('H:i d/m/Y') }}"
                                data-tra="{{ \Carbon\Carbon::parse($phieu->ngayTraDuKien)->format('H:i d/m/Y') }}"
                                data-status="{{ $phieu->trangThai }}"
                                data-note="{{ $phieu->ghiChu ?? '-' }}"

                                data-thietbi="
                                    @foreach($phieu->chiTietMuon as $ct)
                                        {{ $ct->thietbi->tenTB ?? 'TB đã xóa' }} ({{ $ct->maTB }})<br>
                                    @endforeach
                                "
                            >
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open fa-2x mb-2"></i>
                            <p>Không tìm thấy phiếu mượn nào.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer d-flex justify-content-center">
            {{-- Giữ nguyên phân trang và thêm appends để giữ bộ lọc khi qua trang 2 --}}
            {{ $danhSach->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- ========== MODAL CHI TIẾT PHIẾU (GIỮ NGUYÊN) ========== --}}
<div id="detailModal" class="modal">
    <div class="modal-content" style="width: 500px;">
        <span class="close" onclick="closeDetailModal()">&times;</span>
        <h3 class="mb-3 text-primary"><i class="fa-solid fa-circle-info"></i> Chi tiết phiếu</h3>

        <div class="alert alert-light border">
            <div class="d-flex justify-content-between">
                <p><strong>Mã phiếu:</strong> <span id="d_ma" class="fw-bold"></span></p>
                <p><span id="d_trangthai"></span></p>
            </div>
            <hr class="my-2">
            <p><strong>Ngày mượn:</strong> <span id="d_muon"></span></p>
            <p><strong>Ngày trả dự kiến:</strong> <span id="d_tra" class="text-danger fw-bold"></span></p>

            <p class="mt-2"><strong>Thiết bị mượn:</strong></p>
            <div id="d_tb" class="ms-3 p-2 bg-light rounded border"></div>

            <p class="mt-3"><strong>Ghi chú:</strong></p>
            <p id="d_note" class="ms-3 fst-italic text-muted"></p>
        </div>

        <button class="btn btn-secondary w-100 mt-2" onclick="closeDetailModal()">Đóng</button>
    </div>
</div>


{{-- ===== CSS MODAL (GIỮ NGUYÊN) ===== --}}
<style>
    .modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
        justify-content:center; align-items:center; z-index:9999; }
    .modal-content { background:#fff; padding:25px; border-radius:10px; position:relative; animation:fadeIn .2s; }
    .modal .close { position:absolute; top:10px; right:15px; cursor:pointer; font-size:24px; color:#999; }
    .modal .close:hover { color:#333; }
    @keyframes fadeIn { from{opacity:0; transform:scale(.9);} to{opacity:1; transform:scale(1);} }
</style>


{{-- ===== JS MODAL (GIỮ NGUYÊN) ===== --}}
<script>
function openModalFromButton(btn) {
    document.getElementById("d_ma").innerText = btn.dataset.ma;
    document.getElementById("d_muon").innerText = btn.dataset.muon;
    document.getElementById("d_tra").innerText = btn.dataset.tra;
    document.getElementById("d_note").innerText = btn.dataset.note;

    document.getElementById("d_tb").innerHTML = btn.dataset.thietbi;

    // trạng thái
    let tt = btn.dataset.status;
    let html = "";
    if (tt === "Active") html = '<span class="badge bg-primary">Đang mượn</span>';
    else if (tt === "Closed") html = '<span class="badge bg-success">Đã trả</span>';
    else html = '<span class="badge bg-secondary">'+tt+'</span>';

    document.getElementById("d_trangthai").innerHTML = html;

    document.getElementById("detailModal").style.display = "flex";
}

function closeDetailModal() {
    document.getElementById("detailModal").style.display = "none";
}

window.onclick = function(e) {
    if (e.target.id === "detailModal") closeDetailModal();
}
</script>

@endsection