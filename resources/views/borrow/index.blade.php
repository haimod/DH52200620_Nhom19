@extends('layouts.main')

@section('title', 'Lịch sử mượn')

@section('content')
<div class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">📜 Lịch sử mượn trả</h3>
        
        <div class="d-flex gap-3">
            <div class="card border-0 shadow-sm px-3 py-2 d-flex flex-row align-items-center gap-2">
                <div class="rounded-circle bg-primary bg-opacity-10 p-2 text-primary">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div>
                    <div class="small text-muted fw-bold">ĐANG MƯỢN</div>
                    <div class="h5 mb-0 fw-bold text-primary">{{ $danhSach->where('trangThai', 'Active')->count() }}</div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm px-3 py-2 d-flex flex-row align-items-center gap-2">
                <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success">
                    <i class="fa-solid fa-check-double"></i>
                </div>
                <div>
                    <div class="small text-muted fw-bold">ĐÃ TRẢ</div>
                    <div class="h5 mb-0 fw-bold text-success">{{ $danhSach->where('trangThai', 'Closed')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body py-3">
<form action="{{ route('borrow.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <!-- SỬA LẠI: Thêm nút submit rõ ràng để bấm được -->
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm tên thiết bị, ghi chú..." value="{{ request('keyword') }}">
                        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>🔵 Đang mượn</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>🟠 Đã đặt trước</option>
                        <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>🟢 Đã trả</option>
                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>⚪ Đã hủy</option>
                        
                    </select>
                </div>
                <div class="col-md-7 text-end">
                    @if(request('keyword') || request('status'))
                        <a href="{{ route('borrow.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa-solid fa-rotate-left me-1"></i> Xóa lọc</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-secondary small text-uppercase">
                        <th>Mã Phiếu</th>
                        <th>Ngày tạo</th>
                        <th>Thiết bị</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhSach as $phieu)
                        @php
                            $isQuaHan = $phieu->trangThai == 'Active' && \Carbon\Carbon::now() > \Carbon\Carbon::parse($phieu->ngayTraDuKien);
                            $soLuongTB = $phieu->chiTietMuon->count();
                            $tbDauTien = $phieu->chiTietMuon->first()->thietbi->tenTB ?? 'Không xác định';
                        @endphp

                        <tr class="{{ $isQuaHan ? 'bg-danger bg-opacity-10' : '' }}">
                            <td>
                                <span class="fw-bold text-primary">#{{ $phieu->maPM }}</span>
                            </td>
                            
                            <td class="text-muted small">
                                {{ $phieu->created_at ? $phieu->created_at->format('d/m/Y') : '-' }}
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-light rounded p-2 me-2">
                                        <i class="fa-solid fa-laptop text-secondary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $tbDauTien }}</div>
                                        @if($soLuongTB > 1)
                                            <div class="small text-muted">+ {{ $soLuongTB - 1 }} thiết bị khác</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="small">
                                    <div><i class="fa-solid fa-arrow-right-to-bracket text-success me-1"></i> {{ \Carbon\Carbon::parse($phieu->ngayMuon)->format('H:i d/m/Y') }}</div>
                                    <div class="{{ $isQuaHan ? 'text-danger fw-bold' : 'text-muted' }}">
                                        <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> {{ \Carbon\Carbon::parse($phieu->ngayTraDuKien)->format('H:i d/m/Y') }}
                                        @if($isQuaHan) <span class="badge bg-danger ms-1">Quá hạn</span> @endif
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if($phieu->trangThai == 'Active')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Đang mượn</span>
                                @elseif($phieu->trangThai == 'Pending')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">Đã đặt trước</span>
                                @elseif($phieu->trangThai == 'Closed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Đã trả</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">{{ $phieu->trangThai }}</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <button 
                                    class="btn btn-sm btn-light border" 
                                    onclick="openDetailModal(this)"
                                    data-phieu='@json($phieu)'
                                    title="Xem chi tiết">
                                    <i class="fa-solid fa-eye text-secondary"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open fa-2x mb-3 opacity-50"></i>
                                <p class="mb-0">Chưa có lịch sử mượn trả nào.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-center">
            {{ $danhSach->withQueryString()->links() }}
        </div>
    </div>
</div>

<div id="detailModal" class="modal">
    <div class="modal-content border-0 shadow-lg" style="width: 500px;">
        <span class="close position-absolute top-0 end-0 p-3 fs-4" onclick="closeDetailModal()" style="cursor: pointer;">&times;</span>
        
        <div class="text-center mb-4">
            <h4 class="fw-bold text-primary">Chi tiết phiếu mượn</h4>
            <div id="d_status_badge" class="mt-2"></div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-6">
                <div class="p-3 bg-light rounded border">
                    <small class="text-muted d-block text-uppercase">Mã phiếu</small>
                    <span class="fw-bold fs-5" id="d_ma">...</span>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 bg-light rounded border">
                    <small class="text-muted d-block text-uppercase">Ngày tạo</small>
                    <span class="fw-bold" id="d_created">...</span>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="small text-muted text-uppercase fw-bold mb-2">Thời gian</label>
            <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                <span><i class="fa-regular fa-clock me-2 text-success"></i>Bắt đầu:</span>
                <span class="fw-bold" id="d_muon">...</span>
            </div>
            <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                <span><i class="fa-regular fa-clock me-2 text-danger"></i>Kết thúc:</span>
                <span class="fw-bold" id="d_tra">...</span>
            </div>
        </div>

        <div class="mb-3">
            <label class="small text-muted text-uppercase fw-bold mb-2">Danh sách thiết bị</label>
            <ul id="d_list_tb" class="list-group">
                </ul>
        </div>

        <div class="mb-3">
            <label class="small text-muted text-uppercase fw-bold">Ghi chú</label>
            <p id="d_note" class="fst-italic text-secondary bg-light p-2 rounded">Không có ghi chú</p>
        </div>

        <button class="btn btn-secondary w-100" onclick="closeDetailModal()">Đóng</button>
    </div>
</div>

<style>
    .modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:9999; }
    .modal-content { background:#fff; padding:25px; border-radius:10px; position:relative; animation:fadeIn .2s; }
    @keyframes fadeIn { from{opacity:0; transform:scale(.9);} to{opacity:1; transform:scale(1);} }
</style>

@section('scripts')
<script>
    // Hàm mở modal chi tiết
    function openDetailModal(el) {
        // Lấy dữ liệu
        let phieu = JSON.parse(el.getAttribute('data-phieu'));

        document.getElementById("d_ma").innerText = '#' + phieu.maPM; // Nếu cột DB là maPM
        // Hoặc nếu dùng UUID hiển thị ngắn: '#' + phieu.id.substring(0, 8);
        
        document.getElementById("d_created").innerText = new Date(phieu.created_at).toLocaleDateString('vi-VN');
        
        // Format ngày giờ
        let start = new Date(phieu.ngayMuon).toLocaleString('vi-VN', {hour:'2-digit', minute:'2-digit', day:'2-digit', month:'2-digit', year:'numeric'});
        let end = new Date(phieu.ngayTraDuKien).toLocaleString('vi-VN', {hour:'2-digit', minute:'2-digit', day:'2-digit', month:'2-digit', year:'numeric'});
        
        document.getElementById("d_muon").innerText = start;
        document.getElementById("d_tra").innerText = end;
        document.getElementById("d_note").innerText = phieu.ghiChu || 'Không có ghi chú';

        // Render trạng thái
        let statusHtml = '';
        if(phieu.trangThai == 'Active') statusHtml = '<span class="badge bg-primary px-3 py-2">Đang mượn</span>';
        else if(phieu.trangThai == 'Pending') statusHtml = '<span class="badge bg-warning text-dark px-3 py-2">Đã đặt trước</span>';
        else if(phieu.trangThai == 'Closed') statusHtml = '<span class="badge bg-success px-3 py-2">Đã hoàn thành</span>';
        else statusHtml = '<span class="badge bg-secondary px-3 py-2">'+phieu.trangThai+'</span>';
        document.getElementById("d_status_badge").innerHTML = statusHtml;

        // --- PHẦN SỬA LỖI UNDEFINED ---
        let listHtml = '';
        // Lưu ý: Laravel convert quan hệ 'chiTietMuon' thành 'chi_tiet_muon' trong JSON
        if(phieu.chi_tiet_muon && phieu.chi_tiet_muon.length > 0) {
            phieu.chi_tiet_muon.forEach(ct => {
                // 1. Sửa 'ct.thietbi' thành 'ct.thiet_bi' (snake_case)
                let thietBi = ct.thiet_bi; 
                let tenTB = thietBi ? thietBi.tenTB : 'Thiết bị đã xóa';
                // 2. Lấy mã TB từ đối tượng thietBi, không lấy trực tiếp từ ct
                let maTB = thietBi ? thietBi.maTB : 'N/A';

                listHtml += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fa-solid fa-laptop me-2 text-secondary"></i>
                            <span class="fw-bold">${tenTB}</span>
                        </div>
                        <span class="badge bg-light text-dark border">${maTB}</span>
                    </li>
                `;
            });
        } else {
            listHtml = '<li class="list-group-item text-center text-muted">Không có dữ liệu thiết bị</li>';
        }
        document.getElementById("d_list_tb").innerHTML = listHtml;

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

@endsection