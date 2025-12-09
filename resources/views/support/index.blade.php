@extends('layouts.main')
@section('title', 'Hỗ trợ kỹ thuật')

@section('content')

<div class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">🛠️ Trung tâm hỗ trợ IT</h3>
        <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#newTicketModal">
            <i class="fa-solid fa-plus me-2"></i> Gửi yêu cầu mới
        </button>
    </div>

    <!-- 1. CÁC THANH THÔNG TIN LIÊN HỆ (Giữ nguyên) -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 text-primary">
                        <i class="fa-solid fa-phone-volume fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Hotline Kỹ Thuật</small>
                        <div class="fw-bold fs-5 text-dark">0988.123.456</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 text-success">
                        <i class="fa-solid fa-envelope fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Email Hỗ Trợ</small>
                        <div class="fw-bold fs-5 text-dark">it-support@company.com</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3 text-warning">
                        <i class="fa-solid fa-location-dot fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase">Văn phòng IT</small>
                        <div class="fw-bold fs-5 text-dark">Tầng 3 - Phòng 305</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. KHUNG LÀM VIỆC CHÍNH -->
    <div class="row" style="height: 600px;">
        
        <!-- CỘT TRÁI: DANH SÁCH YÊU CẦU TỪ DATABASE -->
        <div class="col-md-4 h-100">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 fw-bold border-bottom">
                    <i class="fa-solid fa-list-ul me-2"></i> Lịch sử yêu cầu
                </div>
                <div class="list-group list-group-flush overflow-auto h-100">
                    @forelse($tickets as $t)
                        @php 
                            // Kiểm tra xem phiếu này có đang được chọn không để highlight
                            $isActive = $activeTicket && $activeTicket->id == $t->id;
                        @endphp
                        
                        <!-- Link bấm vào để chuyển sang xem chi tiết phiếu đó -->
                        <a href="{{ route('support.index', ['id' => $t->id]) }}" class="list-group-item list-group-item-action py-3 {{ $isActive ? 'bg-light border-start border-4 border-primary' : '' }}">
                            <div class="d-flex w-100 justify-content-between mb-1">
                                <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 70%;">{{ $t->subject }}</h6>
                                <small class="text-muted" style="font-size: 11px;">{{ $t->created_at->format('H:i d/m') }}</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">#ID: {{ $t->id }}</span>
                                
                                <!-- Hiển thị trạng thái màu sắc -->
                                @if($t->status == 'pending')
                                    <span class="badge bg-warning text-dark" style="font-size: 11px;">Đang xử lý</span>
                                @elseif($t->status == 'resolved')
                                    <span class="badge bg-success" style="font-size: 11px;">Đã xong</span>
                                @elseif($t->status == 'closed')
                                    <span class="badge bg-secondary" style="font-size: 11px;">Đã đóng</span>
                                @else
                                    <span class="badge bg-info" style="font-size: 11px;">{{ $t->status }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <!-- Nếu chưa có phiếu nào -->
                        <div class="text-center py-5 text-muted">
                            <i class="fa-regular fa-folder-open fa-2x mb-3 opacity-25"></i>
                            <p>Bạn chưa gửi yêu cầu hỗ trợ nào.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: KHUNG CHAT CHI TIẾT -->
        <div class="col-md-8 h-100">
            <div class="card border-0 shadow-sm h-100 d-flex flex-column">
                
                @if($activeTicket)
                    <!-- Tiêu đề Ticket đang xem -->
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-0 text-primary">
                                #{{ $activeTicket->id }} - {{ $activeTicket->subject }}
                            </h5>
                            <small class="text-muted">
                                Người tạo: {{ $activeTicket->user->hoTen ?? $activeTicket->user->name }} | 
                                Trạng thái: <span class="fw-bold text-uppercase">{{ $activeTicket->status }}</span>
                            </small>
                        </div>
                    </div>

                    <!-- Nội dung Chat -->
                    <div class="card-body bg-light overflow-auto flex-grow-1" id="messageContainer" style="background-color: #f8f9fa;">
                        @foreach($messages as $msg)
                            @if($msg->user_id == Auth::id())
                                <!-- Tin nhắn của TÔI (Bên phải) -->
                                <div class="d-flex flex-column align-items-end mb-3">
                                    <div class="bg-primary text-white p-3 rounded-3 shadow-sm" style="max-width: 75%; border-bottom-right-radius: 0;">
                                        {!! nl2br(e($msg->message)) !!}
                                    </div>
                                    <small class="text-muted mt-1 me-1" style="font-size: 11px;">
                                        {{ $msg->created_at->format('H:i d/m/Y') }}
                                    </small>
                                </div>
                            @else
                                <!-- Tin nhắn của ADMIN/Người khác (Bên trái) -->
                                <div class="d-flex flex-column align-items-start mb-3">
                                    <div class="bg-white text-dark p-3 rounded-3 shadow-sm border" style="max-width: 75%; border-top-left-radius: 0;">
                                        <span class="fw-bold text-danger d-block mb-1" style="font-size: 12px;">Admin / Support</span>
                                        {!! nl2br(e($msg->message)) !!}
                                    </div>
                                    <small class="text-muted mt-1 ms-1" style="font-size: 11px;">
                                        {{ $msg->created_at->format('H:i d/m/Y') }}
                                    </small>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Ô nhập liệu Chat -->
                    <div class="card-footer bg-white p-3 border-top">
                        @if($activeTicket->status != 'closed')
                            <form action="{{ route('support.reply') }}" method="POST">
                                @csrf
                                <input type="hidden" name="ticket_id" value="{{ $activeTicket->id }}">
                                <div class="input-group">
                                    <input type="text" name="message" class="form-control" placeholder="Nhập phản hồi..." required autocomplete="off">
                                    <button class="btn btn-primary px-4" type="submit">
                                        <i class="fa-solid fa-paper-plane me-2"></i> Gửi
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="text-center text-muted small">
                                <i class="fa-solid fa-lock me-1"></i> Yêu cầu này đã đóng, không thể gửi thêm tin nhắn.
                            </div>
                        @endif
                    </div>

                @else
                    <!-- Màn hình chờ khi chưa chọn phiếu nào -->
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                        <i class="fa-regular fa-comments fa-4x mb-3 opacity-25"></i>
                        <h5>Chọn một yêu cầu bên trái để xem chi tiết</h5>
                        <p class="small">Hoặc bấm nút "Gửi yêu cầu mới" ở góc trên</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

<!-- Modal Tạo Yêu Cầu Mới (Giữ nguyên form nhập) -->
<div class="modal fade" id="newTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tạo yêu cầu hỗ trợ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('support.send') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề vấn đề <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" placeholder="Ví dụ: Máy in kẹt giấy..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Loại yêu cầu</label>
                        <select name="type" class="form-select">
                            <option value="hardware">Phần cứng / Thiết bị</option>
                            <option value="software">Phần mềm / Cài đặt</option>
                            <option value="network">Mạng / Internet</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chi tiết</label>
                        <textarea name="message" class="form-control" rows="4" placeholder="Mô tả kỹ hơn để chúng tôi hỗ trợ nhanh nhất..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
                    </div>
                </form>
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

<style>
    .toast-notification {
        position: fixed; top:20px; right:20px; z-index:10000;
        padding:15px 25px; border-radius:8px; color:#fff;
        box-shadow:0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s;
    }
    .toast-notification.success { background:#198754; }
    @keyframes slideIn { from{transform:translateX(100%);} to{transform:translateX(0);} }
</style>

<script>
    setTimeout(() => {
        document.querySelectorAll('.toast-notification').forEach(el => el.style.display='none');
    }, 4000);

    // Tự động cuộn xuống cuối khung chat
    var msgContainer = document.getElementById('messageContainer');
    if(msgContainer) {
        msgContainer.scrollTop = msgContainer.scrollHeight;
    }
</script>

@endsection