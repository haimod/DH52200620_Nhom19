@extends('layouts.admin')
@section('title', 'Quản lý Nhân sự')
@section('header_title', 'Danh sách Nhân sự')

@section('content')
<div class="container-fluid p-4">

    <!-- TOOLBAR: TÌM KIẾM & THÊM MỚI -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2" style="max-width: 500px;">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="keyword" class="form-control border-start-0 ps-0" 
                       value="{{ request('keyword') }}" placeholder="Tìm tên, email, mã NV...">
            </div>
            <select name="role" class="form-select shadow-sm w-auto" onchange="this.form.submit()">
                <option value="">Tất cả vai trò</option>
                <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Quản trị viên</option>
                <option value="User" {{ request('role') == 'User' ? 'selected' : '' }}>Nhân viên/User</option>
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
                        <th class="ps-4">Nhân viên</th>
                        <th>Mã NV & SĐT</th> <!-- Cột mới -->
                        <th>Vai trò & Phòng ban</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
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
                                    <div class="fw-bold text-dark">{{ $user->hoTen }}</div>
                                    <div class="text-muted small"><i class="fa-regular fa-envelope me-1"></i> {{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $user->maNV ?? '---' }}</div>
                            <small class="text-muted">{{ $user->sdt ?? '---' }}</small>
                        </td>
                        <td>
                            @if($user->vaiTro == 'Admin')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger mb-1">Quản trị viên</span>
                            @else
                                <span class="badge bg-info bg-opacity-10 text-info border border-info mb-1">Nhân viên</span>
                            @endif
                            <div class="small text-muted">{{ $user->phongBan ?? 'Chưa cập nhật' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Hoạt động</span>
                        </td>
                        <td class="text-end pe-4">
                            <!-- Nút Sửa: Cập nhật thêm data attributes -->
                            <button class="btn btn-light btn-sm border shadow-sm me-1 btn-edit"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->hoTen }}"
                                    data-manv="{{ $user->maNV }}"
                                    data-email="{{ $user->email }}"
                                    data-sdt="{{ $user->sdt }}"
                                    data-role="{{ $user->vaiTro }}"
                                    data-dept="{{ $user->phongBan }}">
                                <i class="fa-solid fa-pen text-primary"></i>
                            </button>
                            
                            <!-- Nút Xóa -->
                            @if(auth()->id() != $user->id)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-light btn-sm border shadow-sm text-danger" onclick="return confirm('Bạn chắc chắn muốn xóa nhân sự này? Dữ liệu mượn trả liên quan có thể bị ảnh hưởng.')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
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
        <!-- Phân trang -->
        <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-center">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- MODAL TẠO MỚI (Đã cập nhật các trường mới) -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.users.store') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-user-plus fs-3"></i>
                    </div>
                    <h4 class="fw-bold">Thêm nhân sự mới</h4>
                    <p class="text-muted small">Nhập đầy đủ thông tin hồ sơ nhân viên</p>
                </div>

                <!-- Row 1: Họ tên + Mã NV -->
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="hoTen" class="form-control bg-light" required placeholder="Vd: Nguyễn Văn A">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">Mã Nhân viên <span class="text-danger">*</span></label>
                        <input type="text" name="maNV" class="form-control bg-light" required placeholder="Vd: NV001">
                    </div>
                </div>

                <!-- Row 2: Email + SĐT -->
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control bg-light" required placeholder="email@company.com">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">Số điện thoại</label>
                        <input type="text" name="sdt" class="form-control bg-light" placeholder="0909xxxxxx">
                    </div>
                </div>

                <!-- Row 3: Phòng ban + Vai trò -->
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">Phòng ban</label>
                        <select name="phongBan" class="form-select bg-light">
                            <option value="">-- Chọn --</option>
                            <option value="Kinh doanh">Kinh doanh</option>
                            <option value="Kỹ thuật">Kỹ thuật</option>
                            <option value="Nhân sự">Nhân sự</option>
                            <option value="Hành chính">Hành chính</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">Vai trò</label>
                        <select name="vaiTro" class="form-select bg-light">
                            <option value="User">Nhân viên</option>
                            <option value="Admin">Quản trị viên</option>
                        </select>
                    </div>
                </div>

                <!-- Row 4: Mật khẩu mặc định -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Mật khẩu mặc định <span class="text-danger">*</span></label>
                    <input type="text" name="password" class="form-control bg-light" required value="123456" placeholder="Nhập mật khẩu khởi tạo">
                    <div class="form-text small">Mật khẩu này sẽ được gửi cho nhân viên để đăng nhập lần đầu.</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-check me-1"></i> Lưu nhân sự
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL CHỈNH SỬA (Đã cập nhật các trường mới) -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editUserForm" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            @method('PUT')
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-info-subtle text-info rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-user-pen fs-3"></i>
                    </div>
                    <h4 class="fw-bold">Cập nhật thông tin</h4>
                    <p class="text-muted small">Chỉnh sửa thông tin hồ sơ nhân sự</p>
                </div>

                <!-- Row 1 -->
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="hoTen" id="editName" class="form-control bg-light" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">Mã Nhân viên <span class="text-danger">*</span></label>
                        <input type="text" name="maNV" id="editMaNV" class="form-control bg-light" required>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="editEmail" class="form-control bg-light" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">Số điện thoại</label>
                        <input type="text" name="sdt" id="editSDT" class="form-control bg-light">
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">Phòng ban</label>
                        <select name="phongBan" id="editDept" class="form-select bg-light">
                            <option value="">-- Chọn --</option>
                            <option value="Kinh doanh">Kinh doanh</option>
                            <option value="Kỹ thuật">Kỹ thuật</option>
                            <option value="Nhân sự">Nhân sự</option>
                            <option value="Hành chính">Hành chính</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">Vai trò</label>
                        <select name="vaiTro" id="editRole" class="form-select bg-light">
                            <option value="User">Nhân viên</option>
                            <option value="Admin">Quản trị viên</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Đặt lại mật khẩu</label>
                    <input type="text" name="password" class="form-control bg-light" placeholder="Nhập nếu muốn đổi mật khẩu mới">
                </div>

                <button type="submit" class="btn btn-info text-white w-100 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ALERT TOAST -->
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

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tự động ẩn thông báo
        setTimeout(() => { document.querySelectorAll('.toast-notification').forEach(el => el.style.display='none'); }, 4000);

        // Xử lý nút Edit
        const editButtons = document.querySelectorAll('.btn-edit');
        const editModalEl = document.getElementById('editUserModal');
        const editForm = document.getElementById('editUserForm');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Lấy dữ liệu từ data attributes
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const manv = this.getAttribute('data-manv'); // Mới
                const email = this.getAttribute('data-email');
                const sdt = this.getAttribute('data-sdt'); // Mới
                const role = this.getAttribute('data-role');
                const dept = this.getAttribute('data-dept');

                // Update form action
                editForm.action = `/admin/users/${id}`;

                // Fill inputs
                document.getElementById('editName').value = name;
                document.getElementById('editMaNV').value = manv; // Mới
                document.getElementById('editEmail').value = email;
                document.getElementById('editSDT').value = sdt; // Mới
                document.getElementById('editRole').value = role;
                document.getElementById('editDept').value = dept;

                // Show Modal
                if (typeof bootstrap !== 'undefined') {
                    const editModal = bootstrap.Modal.getOrCreateInstance(editModalEl);
                    editModal.show();
                }
            });
        });
    });
</script>
@endsection