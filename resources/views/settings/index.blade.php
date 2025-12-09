@extends('layouts.main')
@section('title', 'Cài đặt hệ thống')

@section('content')
<div class="container-fluid py-4">

    <h3 class="fw-bold text-primary mb-4">⚙️ Cài đặt tài khoản & Hệ thống</h3>

    <div class="row">
        <!-- SIDEBAR MENU -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="nav flex-column nav-pills" id="settings-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active mb-2" id="tab-profile" data-bs-toggle="pill" data-bs-target="#content-profile" type="button">
                            <i class="fa-solid fa-user-pen me-2"></i> Hồ sơ cá nhân
                        </button>
                        <button class="nav-link mb-2" id="tab-security" data-bs-toggle="pill" data-bs-target="#content-security" type="button">
                            <i class="fa-solid fa-lock me-2"></i> Đổi mật khẩu
                        </button>
                        @if(auth()->user()->role == 'admin')
                        <button class="nav-link mb-2" id="tab-system" data-bs-toggle="pill" data-bs-target="#content-system" type="button">
                            <i class="fa-solid fa-sliders me-2"></i> Cấu hình hệ thống
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT -->
        <div class="col-md-9">
            <div class="tab-content" id="settings-tabContent">

                <!-- Hồ sơ cá nhân -->
                <div class="tab-pane fade show active" id="content-profile" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Thông tin cá nhân</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('settings.updateProfile') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex align-items-center mb-4">
                                    <div class="me-3">
                                        @if(auth()->user()->avatar)
                                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-3" style="width:80px;height:80px;">
                                                {{ substr(auth()->user()->hoTen ?? auth()->user()->name ?? 'U', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-label fw-bold">Ảnh đại diện</label>
                                        <input type="file" name="avatar" class="form-control form-control-sm">
                                        <small class="text-muted">Định dạng: JPG, PNG. Tối đa 2MB.</small>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Họ và tên</label>
                                        <input type="text" name="name" class="form-control" value="{{ auth()->user()->hoTen ?? auth()->user()->name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Số điện thoại</label>
                                        <input type="text" name="phone" class="form-control" value="{{ auth()->user()->soDienThoai ?? auth()->user()->sdt ?? auth()->user()->phone ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phòng ban / Lớp</label>
                                        <input type="text" class="form-control bg-light" value="{{ auth()->user()->phongBan ?? auth()->user()->department ?? '' }}" readonly>
                                    </div>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-save me-1"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Đổi mật khẩu -->
                <div class="tab-pane fade" id="content-security" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Bảo mật tài khoản</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('settings.changePassword') }}" method="POST">
                                @csrf
                                <!-- Mật khẩu hiện tại -->
                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu hiện tại</label>
                                    <div class="input-group">
                                        <input type="password" name="current_password" id="currentPass" class="form-control" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('currentPass', this)">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('current_password')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <!-- Mật khẩu mới -->
                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu mới</label>
                                    <div class="input-group">
                                        <input type="password" name="new_password" id="newPass" class="form-control" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('newPass', this)">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('new_password')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <!-- Nhập lại mật khẩu mới -->
                                <div class="mb-4">
                                    <label class="form-label">Nhập lại mật khẩu mới</label>
                                    <div class="input-group">
                                        <input type="password" name="new_password_confirmation" id="confirmPass" class="form-control" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPass', this)">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fa-solid fa-key me-1"></i> Đổi mật khẩu
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Cấu hình hệ thống (Admin) -->
                @if(auth()->user()->role == 'admin')
                <div class="tab-pane fade" id="content-system" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Cấu hình hệ thống</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('settings.updateSystem') }}" method="POST">
                                @csrf
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="allow_borrow" id="allowBorrow" checked>
                                    <label class="form-check-label fw-bold" for="allowBorrow">Cho phép mượn thiết bị</label>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label>Số ngày mượn tối đa</label>
                                        <input type="number" name="max_borrow_days" class="form-control" value="7">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Số thiết bị tối đa/ người</label>
                                        <input type="number" name="max_devices_per_user" class="form-control" value="3">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Email nhận thông báo (Admin)</label>
                                    <input type="email" name="admin_email" class="form-control" value="admin@school.edu.vn">
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa-solid fa-check me-1"></i> Lưu cấu hình
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<!-- Toast thông báo -->
@if(session('success'))
<div class="toast-notification success">
    <i class="fa-solid fa-check-circle me-1"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="toast-notification error">
    <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ session('error') }}
</div>
@endif
@if ($errors->any())
<div class="toast-notification error">
    <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<style>
.toast-notification {
    position: fixed; top:20px; right:20px; z-index:10000;
    padding:15px 25px; border-radius:8px; color:#fff;
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
    animation: slideIn 0.3s;
}
.toast-notification.success { background:#198754; }
.toast-notification.error { background:#dc3545; }
@keyframes slideIn { from{transform:translateX(100%);} to{transform:translateX(0);} }
</style>

<script>
    // Ẩn thông báo sau 4s
    setTimeout(() => {
        document.querySelectorAll('.toast-notification').forEach(el => el.style.display='none');
    }, 4000);

    // Hàm ẩn/hiện mật khẩu
    function togglePassword(fieldId, btn) {
        let input = document.getElementById(fieldId);
        let icon = btn.querySelector('i');
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash'); // Đổi icon thành mắt gạch chéo
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye'); // Đổi lại icon mắt thường
        }
    }
</script>

@endsection