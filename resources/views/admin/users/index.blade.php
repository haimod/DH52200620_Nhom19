@extends('layouts.admin')
@section('title', 'Quản lý Nhân sự')
@section('header_title', 'Danh sách Nhân sự')

@section('content')
{{-- Xử lý logic URL form edit --}}
@php
    $editFormAction = '';
    if(old('form_type') == 'edit' && old('id')) {
        $editFormAction = route('admin.users.update', ['user' => old('id')]);
    }
@endphp

<div class="container-fluid p-4">

    <!-- TOOLBAR -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2" style="max-width: 500px;">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="keyword" class="form-control border-start-0 ps-0" 
                       value="{{ request('keyword') }}" placeholder="Tìm tên, username, email, mã NV..." 
                       onkeydown="if(event.key === 'Enter'){this.form.submit();}">
            </div>
            
            <select name="role" class="form-select shadow-sm w-auto" onchange="this.form.submit()">
                <option value="">Tất cả vai trò</option>
                <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Quản trị viên</option>
                <option value="NhanVien" {{ request('role') == 'NhanVien' ? 'selected' : '' }}>Nhân viên</option>
            </select>
        </form>

        <button class="btn btn-primary shadow-sm fw-bold px-4" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fa-solid fa-user-plus me-1"></i> Thêm mới
        </button>
    </div>

    <!-- BẢNG DANH SÁCH -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Nhân viên & Tài khoản</th>
                        <th>Mã NV & SĐT</th>
                        <th>Vai trò & Phòng ban</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="{{ $user->trangThai == 'Blocked' ? 'table-secondary text-muted' : '' }}">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/'.$user->avatar) }}" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center me-3 fw-bold border" style="width:40px;height:40px;">
                                        {{ substr($user->hoTen, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold {{ $user->trangThai == 'Blocked' ? 'text-secondary' : 'text-dark' }}">{{ $user->hoTen }}</div>
                                    @if($user->tenDangNhap)
                                        <div class="small fw-bold {{ $user->trangThai == 'Blocked' ? '' : 'text-primary' }}"><i class="fa-solid fa-user me-1"></i> {{ $user->tenDangNhap }}</div>
                                    @endif
                                    <div class="text-muted small"><i class="fa-regular fa-envelope me-1"></i> {{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $user->maNV ?? '---' }}</div>
                            <small class="text-muted">{{ $user->soDienThoai ?? '---' }}</small>
                        </td>
                        <td>
                            @if(in_array($user->vaiTro, ['Admin', 'admin', 'QuanTri']))
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger mb-1">Quản trị viên</span>
                            @else
                                <span class="badge bg-info bg-opacity-10 text-info border border-info mb-1">Nhân viên</span>
                            @endif
                            <div class="small text-muted">{{ $user->phongBan ?? 'Chưa cập nhật' }}</div>
                        </td>
                        <td>
                            <!-- HIỂN THỊ TRẠNG THÁI -->
                            @if($user->trangThai == 'Blocked')
                                <span class="badge bg-secondary"><i class="fa-solid fa-lock me-1"></i> Đã khóa</span>
                            @else
                                <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Hoạt động</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <!-- NÚT KHÓA/MỞ KHÓA -->
                            @if(auth()->id() != $user->id)
                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm shadow-sm me-1 {{ $user->trangThai == 'Blocked' ? 'btn-outline-success' : 'btn-outline-warning text-dark' }}" 
                                            title="{{ $user->trangThai == 'Blocked' ? 'Mở khóa tài khoản' : 'Khóa tài khoản' }}">
                                        <i class="fa-solid {{ $user->trangThai == 'Blocked' ? 'fa-lock-open' : 'fa-lock' }}"></i>
                                    </button>
                                </form>
                            @endif

                            <!-- Nút Sửa -->
                            <button class="btn btn-light btn-sm border shadow-sm me-1 btn-edit"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->hoTen }}"
                                    data-username="{{ $user->tenDangNhap }}"
                                    data-manv="{{ $user->maNV }}"
                                    data-email="{{ $user->email }}"
                                    data-sdt="{{ $user->soDienThoai }}"
                                    data-role="{{ $user->vaiTro }}"
                                    data-dept="{{ $user->phongBan }}"
                                    title="Chỉnh sửa">
                                <i class="fa-solid fa-pen text-primary"></i>
                            </button>
                            
                            <!-- Nút Xóa (Mở Modal Xác Nhận) -->
                            @if(auth()->id() != $user->id)
                                <button class="btn btn-light btn-sm border shadow-sm text-danger btn-delete" 
                                        data-id="{{ $user->id }}" 
                                        data-name="{{ $user->hoTen }}"
                                        title="Xóa nhân sự">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-users-slash fa-2x mb-2"></i><br>Không tìm thấy nhân sự nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-center">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- MODAL TẠO MỚI -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1050;"></button>
            
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create">

                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-user-plus fs-3"></i>
                        </div>
                        <h4 class="fw-bold">Thêm nhân sự mới</h4>
                        <p class="text-muted small">Nhập đầy đủ thông tin hồ sơ nhân viên</p>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="hoTen" class="form-control bg-light @if(old('form_type') == 'create') @error('hoTen') is-invalid @enderror @endif" value="{{ old('form_type') == 'create' ? old('hoTen') : '' }}" required placeholder="Vd: Nguyễn Văn A">
                            @if(old('form_type') == 'create') @error('hoTen') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" name="tenDangNhap" class="form-control bg-light @if(old('form_type') == 'create') @error('tenDangNhap') is-invalid @enderror @endif" value="{{ old('form_type') == 'create' ? old('tenDangNhap') : '' }}" required placeholder="Vd: nguyenvana">
                            @if(old('form_type') == 'create') @error('tenDangNhap') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Mã Nhân viên <span class="text-danger">*</span></label>
                            <input type="text" name="maNV" class="form-control bg-light @if(old('form_type') == 'create') @error('maNV') is-invalid @enderror @endif" value="{{ old('form_type') == 'create' ? old('maNV') : '' }}" required placeholder="Vd: NV001">
                            @if(old('form_type') == 'create') @error('maNV') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control bg-light @if(old('form_type') == 'create') @error('email') is-invalid @enderror @endif" value="{{ old('form_type') == 'create' ? old('email') : '' }}" required placeholder="email@company.com">
                            @if(old('form_type') == 'create') @error('email') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Số điện thoại</label>
                            <input type="text" name="soDienThoai" class="form-control bg-light @if(old('form_type') == 'create') @error('soDienThoai') is-invalid @enderror @endif" value="{{ old('form_type') == 'create' ? old('soDienThoai') : '' }}" placeholder="0909xxxxxx">
                            @if(old('form_type') == 'create') @error('soDienThoai') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Phòng ban</label>
                            <input type="text" name="phongBan" class="form-control bg-light @if(old('form_type') == 'create') @error('phongBan') is-invalid @enderror @endif" value="{{ old('form_type') == 'create' ? old('phongBan') : '' }}" placeholder="Nhập tên phòng ban">
                            @if(old('form_type') == 'create') @error('phongBan') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Vai trò</label>
                            <select name="vaiTro" class="form-select bg-light">
                                <option value="NhanVien" {{ old('form_type') == 'create' && old('vaiTro') == 'NhanVien' ? 'selected' : '' }}>Nhân viên</option>
                                <option value="Admin" {{ old('form_type') == 'create' && old('vaiTro') == 'Admin' ? 'selected' : '' }}>Quản trị viên</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="text" name="password" class="form-control bg-light @if(old('form_type') == 'create') @error('password') is-invalid @enderror @endif" required value="123456" placeholder="Mật khẩu khởi tạo">
                            @if(old('form_type') == 'create') @error('password') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Lưu nhân sự
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CHỈNH SỬA -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1050;"></button>
            
            <form id="editUserForm" method="POST" action="{{ $editFormAction }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ old('id') }}">
                <input type="hidden" name="form_type" value="edit">

                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-info-subtle text-info rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-user-pen fs-3"></i>
                        </div>
                        <h4 class="fw-bold">Cập nhật thông tin</h4>
                        <p class="text-muted small">Chỉnh sửa thông tin hồ sơ nhân sự</p>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="hoTen" id="editName" class="form-control bg-light @if(old('form_type') == 'edit') @error('hoTen') is-invalid @enderror @endif" value="{{ old('form_type') == 'edit' ? old('hoTen') : '' }}" required>
                            @if(old('form_type') == 'edit') @error('hoTen') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" name="tenDangNhap" id="editUsername" class="form-control bg-light @if(old('form_type') == 'edit') @error('tenDangNhap') is-invalid @enderror @endif" value="{{ old('form_type') == 'edit' ? old('tenDangNhap') : '' }}" required>
                            @if(old('form_type') == 'edit') @error('tenDangNhap') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Mã Nhân viên <span class="text-danger">*</span></label>
                            <input type="text" name="maNV" id="editMaNV" class="form-control bg-light @if(old('form_type') == 'edit') @error('maNV') is-invalid @enderror @endif" value="{{ old('form_type') == 'edit' ? old('maNV') : '' }}" required>
                            @if(old('form_type') == 'edit') @error('maNV') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editEmail" class="form-control bg-light @if(old('form_type') == 'edit') @error('email') is-invalid @enderror @endif" value="{{ old('form_type') == 'edit' ? old('email') : '' }}" required>
                            @if(old('form_type') == 'edit') @error('email') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Số điện thoại</label>
                            <input type="text" name="soDienThoai" id="editSDT" class="form-control bg-light @if(old('form_type') == 'edit') @error('soDienThoai') is-invalid @enderror @endif" value="{{ old('form_type') == 'edit' ? old('soDienThoai') : '' }}">
                            @if(old('form_type') == 'edit') @error('soDienThoai') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Phòng ban</label>
                            <input type="text" name="phongBan" id="editDept" class="form-control bg-light @if(old('form_type') == 'edit') @error('phongBan') is-invalid @enderror @endif" value="{{ old('form_type') == 'edit' ? old('phongBan') : '' }}" placeholder="Nhập tên phòng ban">
                            @if(old('form_type') == 'edit') @error('phongBan') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Vai trò</label>
                            <select name="vaiTro" id="editRole" class="form-select bg-light">
                                <option value="NhanVien">Nhân viên</option>
                                <option value="Admin">Quản trị viên</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Mật khẩu mới</label>
                            <input type="text" name="password" class="form-control bg-light @if(old('form_type') == 'edit') @error('password') is-invalid @enderror @endif" placeholder="(Không đổi)">
                            @if(old('form_type') == 'edit') @error('password') <div class="invalid-feedback small">{{ $message }}</div> @enderror @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-info text-white w-100 py-2 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL XÁC NHẬN XÓA (MỚI THÊM) -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4 text-center">
                <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-trash-can fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Xác nhận xóa</h5>
                <p class="text-muted mb-4">Bạn có chắc chắn muốn xóa nhân sự <strong id="deleteUserName" class="text-dark"></strong>?<br>Hành động này không thể hoàn tác.</p>
                
                <form id="deleteUserForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Xóa ngay</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- THÔNG BÁO -->
@if(session('success'))
<div class="toast-notification success"><i class="fa-solid fa-check-circle me-1"></i> {{ session('success') }}</div>
@endif

@if(session('error'))
<div class="toast-notification error"><i class="fa-solid fa-triangle-exclamation me-1"></i> {{ session('error') }}</div>
@endif

@if($errors->any())
<div class="toast-notification error">
    <i class="fa-solid fa-triangle-exclamation me-1"></i> Vui lòng kiểm tra lại thông tin nhập!
</div>
@endif

<style>
    .toast-notification { position: fixed; top: 20px; right: 20px; z-index: 10000; padding: 15px 25px; border-radius: 8px; color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideIn 0.3s; }
    .toast-notification.success { background: #198754; }
    .toast-notification.error { background: #dc3545; }
    @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
</style>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => { document.querySelectorAll('.toast-notification').forEach(el => el.style.display='none'); }, 4000);

        // --- TỰ ĐỘNG MỞ MODAL NẾU CÓ LỖI ---
        const createModalEl = document.getElementById('createUserModal');
        const editModalEl = document.getElementById('editUserModal');

        if (createModalEl && createModalEl.querySelector('.is-invalid')) {
            if (typeof bootstrap !== 'undefined') new bootstrap.Modal(createModalEl).show();
        }

        if (editModalEl && editModalEl.querySelector('.is-invalid')) {
            if (typeof bootstrap !== 'undefined') new bootstrap.Modal(editModalEl).show();
        }

        // --- XỬ LÝ NÚT SỬA ---
        const editButtons = document.querySelectorAll('.btn-edit');
        const editForm = document.getElementById('editUserForm');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                editForm.action = `/admin/users/${id}`;
                editForm.querySelector('input[name="id"]').value = id; 
                document.getElementById('editName').value = this.getAttribute('data-name');
                document.getElementById('editUsername').value = this.getAttribute('data-username');
                document.getElementById('editMaNV').value = this.getAttribute('data-manv');
                document.getElementById('editEmail').value = this.getAttribute('data-email');
                document.getElementById('editSDT').value = this.getAttribute('data-sdt');
                document.getElementById('editRole').value = this.getAttribute('data-role');
                document.getElementById('editDept').value = this.getAttribute('data-dept');

                if (typeof bootstrap !== 'undefined') new bootstrap.Modal(editModalEl).show();
            });
        });

        // --- XỬ LÝ NÚT XÓA (MỞ MODAL) ---
        const deleteButtons = document.querySelectorAll('.btn-delete');
        const deleteModalEl = document.getElementById('deleteUserModal');
        const deleteForm = document.getElementById('deleteUserForm');
        const deleteUserName = document.getElementById('deleteUserName');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                
                deleteForm.action = `/admin/users/${id}`;
                deleteUserName.innerText = name;

                if (typeof bootstrap !== 'undefined') new bootstrap.Modal(deleteModalEl).show();
            });
        });
    });
</script>
@endsection