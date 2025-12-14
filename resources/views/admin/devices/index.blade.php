@extends('layouts.admin')
@section('title', 'Quản lý thiết bị')
@section('header_title', 'Kho thiết bị & Tài sản')

@section('content')
{{-- Định nghĩa các trạng thái chuẩn để dùng cho bộ lọc và hiển thị --}}
@php
    $statuses = [
        'Available'   => '✅ Sẵn sàng',
        'In_Use'      => '👤 Đang sử dụng',
        'Maintenance' => '🛠️ Bảo trì',
        'Broken'      => '❌ Hỏng',
        'Liquidated'  => '📦 Đã thanh lý',
    ];
@endphp

<div class="container-fluid p-4">

    <!-- THANH CÔNG CỤ -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <form action="{{ route('admin.devices.index') }}" method="GET" class="d-flex gap-2">
            <!-- Tìm kiếm từ khóa -->
            <div class="input-group shadow-sm" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="keyword" class="form-control border-start-0 ps-0" 
                       placeholder="Tìm mã, tên, serial..." value="{{ request('keyword') }}">
            </div>
            
            <!-- Lọc theo Tình trạng -->
            <select name="status" class="form-select shadow-sm w-auto fw-bold text-primary" onchange="this.form.submit()">
                <option value="">-- Tất cả tình trạng --</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </form>
        
        <!-- Nút Thêm Mới -->
       <div class="d-flex gap-2">
    <a href="{{ route('admin.devices.export') }}" class="btn btn-success fw-bold shadow-sm text-white">
        <i class="fa-solid fa-file-excel me-2"></i> Xuất Báo Cáo
    </a>

    <button class="btn btn-primary fw-bold shadow-sm" onclick="openModal('add')">
        <i class="fa-solid fa-plus me-2"></i> Nhập kho mới
    </button>
</div>
        
    </div>

    <!-- BẢNG DANH SÁCH -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small fw-bold text-muted">
                        <tr>
                            <th class="ps-4">Mã Tài Sản</th>
                            <th>Tên Thiết bị</th>
                            <th>Vị trí</th>
                            <th>Số Serial</th>
                            <th>Tình trạng</th>
                            <th>Hạn Bảo Hành</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $d)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-primary font-monospace">{{ $d->maTB }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $d->tenTB }}</div>
                                <div class="small text-muted">Mua: {{ $d->ngayMua ? date('d/m/Y', strtotime($d->ngayMua)) : '---' }}</div>
                            </td>
                            <td>
                               <small class="text-muted fw-bold">
                                    <i class="fa-solid fa-map-pin me-1 text-danger"></i> {{ $d->viTri ?? 'Kho Trung Tâm' }}
                                </small>
                            </td>
                            <td>
                                <span class="font-monospace small text-muted">{{ $d->soSerial ?? '---' }}</span>
                            </td>
                            <td>
                                @if(array_key_exists($d->tinhTrang, $statuses))
                                    <span class="badge bg-opacity-10 text-dark border" style="background-color: #f8f9fa;">
                                        {{ $statuses[$d->tinhTrang] }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">{{ $d->tinhTrang }}</span>
                                @endif
                            </td>
                            <td>
                                @if($d->hanBaoHanh)
                                    @if(\Carbon\Carbon::parse($d->hanBaoHanh)->isPast())
                                        <span class="text-danger small fw-bold"><i class="fa-solid fa-circle-exclamation"></i> Hết hạn</span>
                                    @else
                                        <span class="text-success small fw-bold">{{ date('d/m/Y', strtotime($d->hanBaoHanh)) }}</span>
                                    @endif
                                @else
                                    <span class="text-muted small">---</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <!-- Nút Sửa -->
                                    <button class="btn btn-light btn-sm text-primary border edit-btn" 
                                            title="Sửa"
                                            data-device="{{ json_encode($d) }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    
                                    <!-- Nút Xóa (Đã sửa lại cách gọi để tránh lỗi cú pháp) -->
                                    <button class="btn btn-light btn-sm text-danger border ms-1" 
                                            title="Xóa"
                                            data-name="{{ $d->maTB }}"
                                            data-url="{{ route('admin.devices.destroy', $d->id) }}"
                                            onclick="openDeleteModal(this)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open fa-2x mb-2"></i>
                                <p>Không tìm thấy thiết bị nào.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-center">
                {{ $devices->withQueryString()->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</div>

<!-- MODAL CHUNG (THÊM / SỬA) -->
<div class="modal fade" id="deviceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Nhập kho thiết bị mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="deviceForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="methodField" value="POST"> 
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tên thiết bị <span class="text-danger">*</span></label>
                            <input type="text" name="tenTB" id="tenTB" class="form-control" placeholder="Vd: Laptop Dell XPS 13" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Mã tài sản <span class="text-danger">*</span></label>
                            <input type="text" name="maTB" id="maTB" class="form-control" placeholder="Vd: LP-001" required>
                            <div class="form-text small" id="maTBHelp">Mã duy nhất, không thể trùng lặp.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Loại thiết bị</label>
                            <input type="text" name="loaiTB" id="loaiTB" class="form-control" placeholder="Nhập loại (Vd: Laptop, PC, Máy in...)" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tình trạng / Trạng thái</label>
                            <select name="tinhTrang" id="tinhTrang" class="form-select">
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Số Serial / Service Tag</label>
                        <input type="text" name="soSerial" id="soSerial" class="form-control" placeholder="Nhập số serial của máy...">
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ngày mua</label>
                            <input type="date" name="ngayMua" id="ngayMua" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Hạn bảo hành</label>
                            <input type="date" name="hanBaoHanh" id="hanBaoHanh" class="form-control">
                        </div>
                    </div>

                    <div class="text-end border-top pt-3">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4" id="submitBtn">
                            <i class="fa-solid fa-save me-2"></i>Lưu dữ liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL XÓA (MỚI) -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <i class="fa-solid fa-triangle-exclamation text-danger fa-3x mb-3"></i>
                <h5 class="fw-bold mb-2">Xác nhận xóa?</h5>
                <p class="text-muted small mb-4">Bạn có chắc chắn muốn xóa thiết bị <strong id="deleteDeviceName" class="text-dark">...</strong>? Hành động này không thể hoàn tác.</p>
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light w-50" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger w-50 fw-bold">Xóa ngay</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
@if(session('success'))
<div class="toast-notification success"><i class="fa-solid fa-check-circle me-1"></i> {{ session('success') }}</div>
@endif
<!-- @if($errors->any())
<div class="toast-notification error"><i class="fa-solid fa-triangle-exclamation me-1"></i> Có lỗi xảy ra!</div>
@endif -->
@if ($errors->any())
<div class="toast-notification error" id="errorToastBox">
    <i class="fa-solid fa-triangle-exclamation me-2"></i> Có lỗi xảy ra!
    <ul class="mt-2 mb-0 ps-3">
        @foreach ($errors->all() as $err)
            <li class="small">{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<style>
    .toast-notification { position: fixed; top: 20px; right: 20px; z-index: 10000; padding: 15px 25px; border-radius: 8px; color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideIn 0.3s; }
    .toast-notification.success { background: #198754; }
    .toast-notification.error { background: #dc3545; }
    @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
</style>

<script>
    setTimeout(() => { document.querySelectorAll('.toast-notification').forEach(el => el.style.display='none'); }, 4000);

    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const rawData = this.getAttribute('data-device');
                if (rawData) {
                    try {
                        const data = JSON.parse(rawData);
                        openModal('edit', data);
                    } catch (e) {
                        // Đã bỏ console.error ở đây
                    }
                }
            });
        });
    });

    // Hàm mở Modal Thêm/Sửa
    function openModal(mode, data = null) {
        const modalEl = document.getElementById('deviceModal');
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('deviceForm');
        
        if(mode === 'add') form.reset();

        const title = document.getElementById('modalTitle');
        const methodField = document.getElementById('methodField');
        const submitBtn = document.getElementById('submitBtn');

        const maTB = document.getElementById('maTB');
        const tenTB = document.getElementById('tenTB');
        const loaiTB = document.getElementById('loaiTB');
        const tinhTrang = document.getElementById('tinhTrang');
        const soSerial = document.getElementById('soSerial');
        const ngayMua = document.getElementById('ngayMua');
        const hanBaoHanh = document.getElementById('hanBaoHanh');

        if (mode === 'add') {
            title.innerText = 'Nhập kho thiết bị mới';
            form.action = "{{ route('admin.devices.store') }}";
            methodField.value = "POST";
            submitBtn.innerHTML = '<i class="fa-solid fa-plus me-2"></i>Thêm mới';
            
            maTB.readOnly = false;
            maTB.classList.remove('bg-light');
            document.getElementById('maTBHelp').style.display = 'block';
            ngayMua.value = new Date().toISOString().split('T')[0];

        } else if (mode === 'edit' && data) {
            title.innerText = 'Cập nhật thông tin thiết bị';
            
            // Sửa route cập nhật cho đúng với web.php
            form.action = "/admin/thiet-bi/cap-nhat/" + data.id; 
            
            methodField.value = "PUT";
            submitBtn.innerHTML = '<i class="fa-solid fa-save me-2"></i>Lưu thay đổi';

            maTB.value = data.maTB;
            tenTB.value = data.tenTB;
            loaiTB.value = data.maLoai; 
            tinhTrang.value = data.tinhTrang;
            soSerial.value = data.soSerial;
            ngayMua.value = data.ngayMua ? data.ngayMua.split(' ')[0] : '';
            hanBaoHanh.value = data.hanBaoHanh ? data.hanBaoHanh.split(' ')[0] : '';

            maTB.readOnly = true;
            maTB.classList.add('bg-light');
            document.getElementById('maTBHelp').style.display = 'none';
        }

        modal.show();
    }

    // Hàm mở Modal Xóa (Đã sửa để dùng dataset)
    function openDeleteModal(button) {
        // Lấy dữ liệu từ attribute data-name và data-url của nút bấm
        const name = button.dataset.name;
        const url = button.dataset.url;

        document.getElementById('deleteDeviceName').innerText = name;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endsection