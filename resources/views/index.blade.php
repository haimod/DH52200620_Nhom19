@extends('layouts.main')

@section('title', 'Trang chủ')

@section('content')
<div class="main-content">

    <!-- Banner Chào mừng -->
    <div class="welcome-banner mb-4 p-4 rounded-3 shadow-sm d-flex justify-content-between align-items-center bg-white">
        <div>
            <h4 class="mb-1 fw-bold text-primary">Xin chào, {{ Auth::user()->hoTen ?? 'Nhân viên' }}! 👋</h4>
            <p class="mb-0 text-muted small">
                <i class="fa-solid fa-id-badge me-1"></i> {{ Auth::user()->maNV }} 
                <span class="mx-2">|</span> 
                <i class="fa-solid fa-building-user me-1"></i> {{ Auth::user()->phongBan }}
            </p>
        </div>
        <div class="d-none d-md-block text-end">
            <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                <i class="fa-solid fa-circle-check me-1"></i> Hệ thống sẵn sàng
            </span>
        </div>
    </div>

    <!-- Danh sách thiết bị -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-list-ul me-2"></i>Danh sách thiết bị</h5>
                
                <!-- Bộ lọc & Tìm kiếm -->
                <form action="/" method="GET" class="d-flex gap-2 flex-wrap flex-grow-1 justify-content-end">
                    <select name="status" class="form-select form-select-sm" style="max-width: 150px;" onchange="this.form.submit()">
                        <option value="all">-- Trạng thái --</option>
                        <option value="Available" {{ $status == 'Available' ? 'selected' : '' }}>🟢 Khả dụng</option>
                        <option value="In_Use" {{ $status == 'In_Use' ? 'selected' : '' }}>🟡 Đang mượn</option>
                        <option value="Maintenance" {{ $status == 'Maintenance' ? 'selected' : '' }}>🔵 Bảo trì</option>
                        <option value="Broken" {{ $status == 'Broken' ? 'selected' : '' }}>🔴 Hỏng</option>
                    </select>
                    <div class="input-group input-group-sm" style="max-width: 250px;">
                        <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Tìm tên, mã...">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                    @if($keyword || $status != 'all')
                        <a href="/" class="btn btn-sm btn-outline-secondary" title="Xóa lọc"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0 table-responsive"> 
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light sticky-top"> 
                    <tr>
                        <th>Mã TB</th>
                        <th>Tên thiết bị</th>
                        <th>Loại</th>
                        <th>Vị trí</th>
                        <th>Tình trạng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($thietbi as $tb)
                    <tr>
                        <td class="fw-bold text-secondary">{{ $tb->maTB }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-2 bg-light rounded p-1 text-center" style="width: 30px; height: 30px;">
                                    <i class="fa-solid fa-laptop text-secondary small"></i>
                                </div>
                                <span class="fw-semibold">{{ $tb->tenTB }}</span>
                            </div>
                        </td>
                        <td>{{ $tb->maLoai }}</td>
                        <td><small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i>{{ $tb->viTri ?? 'Kho Trung Tâm' }}</small></td>
                        <td>
                            @if($tb->tinhTrang == 'Available')
                                @if($tb->lichDatTruoc->count() > 0)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                        <i class="fa-regular fa-clock me-1"></i> Có lịch đặt trước
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="fa-solid fa-check me-1"></i> Sẵn sàng
                                    </span>
                                @endif
                            @elseif($tb->tinhTrang == 'In_Use')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">
                                    <i class="fa-solid fa-user-clock me-1"></i> Đang mượn
                                </span>
                            @elseif($tb->tinhTrang == 'Maintenance')
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill">
                                    <i class="fa-solid fa-screwdriver-wrench me-1"></i> Bảo trì
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Hỏng
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($tb->tinhTrang == 'Available')
                                <button class="btn btn-primary btn-sm px-3 shadow-sm borrow-btn" 
                                    data-matb="{{ $tb->maTB }}" 
                                    data-tentb="{{ $tb->tenTB }}"
                                    data-uuid="{{ $tb->id }}"> 
                                    Mượn ngay
                                </button>
                            @elseif($tb->tinhTrang == 'In_Use')
                                @php
                                    $phieu = $tb->muonMoiNhat->phieuMuon ?? null;
                                    $ngayTraDisplay = $phieu ? \Carbon\Carbon::parse($phieu->ngayTraDuKien)->format('H:i d/m/Y') : 'Chưa rõ';
                                    $ngayTraISO = $phieu ? \Carbon\Carbon::parse($phieu->ngayTraDuKien)->format('Y-m-d\TH:i:s') : '';
                                    $nguoiMuon = $phieu && $phieu->user ? $phieu->user->hoTen : 'Ai đó';
                                @endphp
                                <button class="btn btn-outline-warning text-dark btn-sm px-3 info-btn" 
                                    data-matb="{{ $tb->maTB }}" 
                                    data-tentb="{{ $tb->tenTB }}" 
                                    data-uuid="{{ $tb->id }}" 
                                    data-nguoimuon="{{ $nguoiMuon }}" 
                                    data-ngaytra="{{ $ngayTraDisplay }}"
                                    data-ngaytra-iso="{{ $ngayTraISO }}">
                                    Xem & Đặt lịch
                                </button>
                            @else
                                <button class="btn btn-secondary btn-sm px-3" disabled>Khóa</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0">Không tìm thấy thiết bị nào.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-center">
                {{ $thietbi->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- TOAST THÔNG BÁO -->
@if(session('success'))
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast show bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast show bg-danger text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif

<!-- MODAL 1: ĐĂNG KÝ MƯỢN -->
<div id="borrowModal" class="modal">
    <div class="modal-content shadow-lg border-0" style="max-width: 450px;">
        <span class="close position-absolute top-0 end-0 p-3 fs-4" style="cursor: pointer;">&times;</span>
        
        <div class="text-center mb-4">
            <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-2" style="width: 60px; height: 60px;">
                <i class="fa-regular fa-calendar-plus fs-3"></i>
            </div>
            <h4 class="fw-bold">Đăng ký mượn</h4>
            <p class="text-muted small">Thiết bị: <strong id="modalTenTB" class="text-dark"></strong></p>
        </div>

        <form action="{{ route('borrow.store') }}" method="POST">
            @csrf
            <!-- QUAN TRỌNG: Đổi name='thietbi' thành name='thiet_bi_id' để gửi UUID -->
            <input type="hidden" name="thiet_bi_id" id="modalThietBiId">
            
            <div class="mb-3 p-3 bg-warning-subtle rounded border border-warning-subtle">
                <h6 class="fw-bold small text-warning-emphasis mb-2"><i class="fa-solid fa-triangle-exclamation"></i> Chú ý các khung giờ đã có người đặt:</h6>
                <ul id="borrowScheduleList" class="list-group list-group-flush small bg-transparent" style="max-height: 100px; overflow-y: auto;">
                    <li class="list-group-item bg-transparent text-center text-muted">Đang kiểm tra lịch...</li>
                </ul>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label small fw-bold text-secondary">Bắt đầu mượn</label>
                    <input type="datetime-local" name="ngayMuon" id="ngayMuon" class="form-control bg-light" required>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-bold text-secondary">Dự kiến trả</label>
                    <input type="datetime-local" name="ngayTraDuKien" id="ngayTraDuKien" class="form-control bg-light" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" onclick="setQuickTime(1)">+1 Giờ</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" onclick="setQuickTime(2)">+2 Giờ</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" onclick="setQuickTime(4)">+4 Giờ</button>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Mục đích sử dụng</label>
                <textarea name="lyDo" class="form-control bg-light" rows="2" placeholder="VD: Dùng cho cuộc họp team..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                Xác nhận đăng ký
            </button>
        </form>
    </div>
</div>

<!-- MODAL 2: THÔNG TIN & ĐẶT LỊCH -->
<div id="infoModal" class="modal">
    <div class="modal-content border-0 shadow-lg" style="max-width: 450px;">
        <span class="close position-absolute top-0 end-0 p-3 fs-4" style="cursor: pointer;">&times;</span>
        
        <h5 class="fw-bold mb-3 text-center text-warning"><i class="fa-solid fa-calendar-check me-2"></i>Thông tin & Đặt lịch</h5>
        
        <p class="text-muted small text-center">
            Thiết bị: <strong id="infoTenTB" class="text-dark"></strong>
        </p>

        <div class="bg-light p-3 rounded mb-3 border">
            <h6 class="fw-bold small text-muted">🔴 ĐANG SỬ DỤNG BỞI:</h6>
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold" id="infoNguoiMuon">...</span>
                <span class="badge bg-warning text-dark">Trả lúc: <span id="infoNgayTra">...</span></span>
            </div>
            <div class="mt-2 small text-secondary">
                <i class="fa-solid fa-circle-info me-1"></i> 
                Bạn có thể đặt lịch mượn sau thời gian trả bên trên.
            </div>
        </div>

        <div class="mb-3">
            <h6 class="fw-bold small text-muted border-bottom pb-2"><i class="fa-solid fa-calendar-days"></i> Các lịch đã đặt trước:</h6>
            <ul id="infoScheduleList" class="list-group list-group-flush small" style="max-height: 120px; overflow-y: auto;">
                <li class="list-group-item bg-transparent text-center text-muted">Đang tải...</li>
            </ul>
        </div>

        <form action="{{ route('borrow.store') }}" method="POST">
            @csrf
            <!-- QUAN TRỌNG: Đổi name='thietbi' thành name='thiet_bi_id' -->
            <input type="hidden" name="thiet_bi_id" id="infoThietBiId">
            
            <div class="mb-3">
                <label class="fw-bold small">📅 Chọn thời gian bạn muốn đặt:</label>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="datetime-local" name="ngayMuon" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-6">
                        <input type="datetime-local" name="ngayTraDuKien" class="form-control form-control-sm" required>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <input type="text" name="lyDo" class="form-control form-control-sm" placeholder="Lý do mượn..." required>
            </div>

            <button type="submit" class="btn btn-warning w-100 fw-bold">
                <i class="fa-solid fa-bookmark me-1"></i> Đặt lịch trước
            </button>
        </form>
    </div>
</div>

@section('scripts')
<script>
    function setQuickTime(hours) {
        let startInput = document.getElementById('ngayMuon');
        let endInput = document.getElementById('ngayTraDuKien');
        
        let startTime = startInput.value ? new Date(startInput.value) : new Date();
        
        if (!startInput.value) {
            let localStart = new Date(startTime.getTime() - (startTime.getTimezoneOffset() * 60000));
            startInput.value = localStart.toISOString().slice(0, 16);
        } else {
             startTime = new Date(startInput.value); 
        }

        let endTime = new Date(startTime.getTime() + hours * 60 * 60 * 1000);
        let localEndTime = new Date(endTime.getTime() - (endTime.getTimezoneOffset() * 60000));
        endInput.value = localEndTime.toISOString().slice(0, 16);
    }

    // HÀM GỌI API LỊCH (Vẫn dùng Mã TB để hiển thị lịch dễ hơn)
    function fetchSchedule(maTB, listElementId) {
        let listContainer = document.getElementById(listElementId);
        listContainer.innerHTML = '<li class="list-group-item bg-transparent text-center"><div class="spinner-border spinner-border-sm text-secondary"></div></li>';

        fetch(`/api/get-schedule/${maTB}`)
            .then(response => response.json())
            .then(data => {
                listContainer.innerHTML = '';
                let pendingList = data.filter(item => item.trangThai === 'Pending');

                if (pendingList.length === 0) {
                    listContainer.innerHTML = '<li class="list-group-item bg-transparent text-success text-center small fst-italic">✅ Không có lịch đặt trước nào.</li>';
                } else {
                    pendingList.forEach(item => {
                        let start = new Date(item.ngayMuon).toLocaleString('vi-VN', {hour: '2-digit', minute:'2-digit', day:'2-digit', month:'2-digit'});
                        let end = new Date(item.ngayTraDuKien).toLocaleString('vi-VN', {hour: '2-digit', minute:'2-digit', day:'2-digit', month:'2-digit'});
                        let ten = item.hoTen ? item.hoTen : 'Người dùng';
                        
                        let li = document.createElement('li');
                        li.className = 'list-group-item bg-transparent px-0 py-1 border-bottom';
                        li.innerHTML = `<div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold small text-dark">${ten}</span>
                                            <span class="small text-muted">${start} ➝ ${end}</span>
                                        </div>`;
                        listContainer.appendChild(li);
                    });
                }
            })
            .catch(err => { console.error(err); listContainer.innerHTML = '<li class="text-danger small">Lỗi tải dữ liệu</li>'; });
    }

    document.addEventListener('DOMContentLoaded', function() {
        let now = new Date();
        let localNow = new Date(now.getTime() - (now.getTimezoneOffset() * 60000));
        if(document.getElementById('ngayMuon')) {
            document.getElementById('ngayMuon').value = localNow.toISOString().slice(0, 16);
        }
        
        const borrowModal = document.getElementById('borrowModal');
        const infoModal = document.getElementById('infoModal');

        // --- 1. XỬ LÝ NÚT XANH (MƯỢN NGAY) ---
        document.querySelectorAll('.borrow-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                let uuid = this.dataset.uuid; // Lấy UUID từ data attribute
                let maTB = this.dataset.matb; // Lấy Mã hiển thị
                
                // Điền UUID vào hidden input (để gửi lên server)
                document.getElementById('modalThietBiId').value = uuid; 
                
                document.getElementById('modalTenTB').innerText = this.dataset.tentb;
                document.getElementById('ngayTraDuKien').value = ''; 

                // Vẫn dùng mã TB để gọi API lịch (vì API chưa chắc đã đổi)
                fetchSchedule(maTB, 'borrowScheduleList');

                borrowModal.style.display = 'flex';
            });
        });

        // --- 2. XỬ LÝ NÚT VÀNG (XEM & ĐẶT LỊCH) ---
        document.querySelectorAll('.info-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                let uuid = this.dataset.uuid; // Lấy UUID
                let maTB = this.dataset.matb;

                // Điền UUID vào hidden input
                document.getElementById('infoThietBiId').value = uuid;
                
                document.getElementById('infoTenTB').innerText = this.dataset.tentb;
                document.getElementById('infoNguoiMuon').innerText = this.dataset.nguoimuon;
                document.getElementById('infoNgayTra').innerText = this.dataset.ngaytra;

                fetchSchedule(maTB, 'infoScheduleList');

                // Auto-fill Logic
                try {
                    let rawDate = this.dataset.ngaytraIso; 
                    if (rawDate) {
                        let returnDate = new Date(rawDate);
                        if (!isNaN(returnDate.getTime())) {
                            returnDate.setMinutes(returnDate.getMinutes() + 15);
                            let localDate = new Date(returnDate.getTime() - (returnDate.getTimezoneOffset() * 60000));
                            let startInput = document.querySelector('#infoModal input[name="ngayMuon"]');
                            if(startInput) startInput.value = localDate.toISOString().slice(0, 16);
                        }
                    }
                } catch(e) {}

                infoModal.style.display = 'flex';
            });
        });

        document.querySelectorAll('.close, .btn-secondary').forEach(el => {
            el.addEventListener('click', () => {
                borrowModal.style.display = 'none';
                infoModal.style.display = 'none';
            });
        });
        
        window.onclick = function(event) {
            if (event.target == borrowModal) borrowModal.style.display = "none";
            if (event.target == infoModal) infoModal.style.display = "none";
        }
    });
</script>
@endsection

@endsection