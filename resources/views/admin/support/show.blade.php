@extends('layouts.admin')
@section('title', 'Chi tiết Hỗ trợ #' . $ticket->id)
@section('header_title', 'Chi tiết Yêu cầu')

@section('content')
<style>
    /* CSS cho bong bóng chat để tránh lỗi cú pháp trong thẻ style */
    .chat-bubble-me { border-bottom-right-radius: 4px !important; }
    .chat-bubble-other { border-bottom-left-radius: 4px !important; }
</style>

<div class="container-fluid p-4 h-100 d-flex flex-column">
    
    <div class="row g-4 flex-grow-1">
        <!-- CỘT TRÁI: THÔNG TIN VÉ -->
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <a href="{{ route('admin.support.index') }}" class="btn btn-link text-muted p-0 text-decoration-none mb-3">
                        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách
                    </a>

                    <h6 class="fw-bold text-muted text-uppercase mb-3 border-bottom pb-2">Thông tin vé #{{ $ticket->id }}</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Người yêu cầu</small>
                        <div class="fw-bold">{{ $ticket->user->hoTen ?? 'N/A' }}</div>
                        <div class="small text-muted">{{ $ticket->user->email ?? '' }}</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Vấn đề</small>
                        <div class="fw-bold text-primary">{{ $ticket->subject }}</div>
                        <span class="badge bg-light text-dark border mt-1">{{ $ticket->type }}</span>
                    </div>

                    <div class="mb-4">
                        <small class="text-muted d-block">Trạng thái hiện tại</small>
                        @if($ticket->status == 'pending')
                            <span class="badge bg-warning text-dark w-100 py-2">Chờ xử lý</span>
                        @elseif($ticket->status == 'processing')
                            <span class="badge bg-info text-dark w-100 py-2">Đang xử lý</span>
                        @elseif($ticket->status == 'resolved')
                            <span class="badge bg-success w-100 py-2">Đã giải quyết</span>
                        @else
                            <span class="badge bg-secondary w-100 py-2">Đã đóng</span>
                        @endif
                    </div>

                    @if($ticket->status != 'closed')
                        <!-- Nút mở Modal Đóng yêu cầu -->
                        <button class="btn btn-outline-dark w-100 mb-2" data-bs-toggle="modal" data-bs-target="#closeTicketModal">
                            <i class="fa-solid fa-check me-2"></i> Đánh dấu hoàn tất
                        </button>
                    @endif
                    
                    <!-- Nút mở Modal Xóa yêu cầu -->
                    <button class="btn btn-outline-danger w-100 border-0 btn-sm" data-bs-toggle="modal" data-bs-target="#deleteTicketModal">
                        <i class="fa-solid fa-trash me-2"></i> Xóa yêu cầu
                    </button>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: KHUNG CHAT -->
        <div class="col-md-8 col-lg-9">
            <div class="card border-0 shadow-sm h-100 d-flex flex-column">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-comments me-2"></i>Lịch sử trao đổi</h6>
                </div>

                <!-- VÙNG HIỂN THỊ TIN NHẮN -->
                <div class="card-body bg-light overflow-auto d-flex flex-column gap-3" style="height: 500px;" id="chatBox">
                    @foreach($ticket->messages as $msg)
                        @php
                            $isMe = $msg->user_id == Auth::id(); 
                            $isAdminUser = false;
                            if ($msg->user && in_array($msg->user->vaiTro, ['Admin', 'QuanTri', 'admin'])) {
                                $isAdminUser = true;
                            }
                        @endphp

                        <div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                            <div style="max-width: 75%;">
                                <div class="d-flex align-items-end gap-2 {{ $isMe ? 'flex-row-reverse' : '' }}">
                                    
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm {{ $isAdminUser ? 'bg-primary' : 'bg-secondary' }}" 
                                         style="width: 35px; height: 35px; flex-shrink: 0;"
                                         title="{{ $msg->user->hoTen ?? 'User' }}">
                                        {{ substr($msg->user->hoTen ?? 'U', 0, 1) }}
                                    </div>
                                    
                                    <div class="p-3 rounded-3 shadow-sm {{ $isMe ? 'bg-primary text-white chat-bubble-me' : 'bg-white text-dark chat-bubble-other' }}">
                                        @if(!$isMe)
                                            <div class="fw-bold small mb-1 opacity-75">
                                                {{ $msg->user->hoTen ?? 'Unknown' }}
                                                @if($isAdminUser) <span class="badge bg-info text-dark ms-1" style="font-size: 0.6rem;">ADMIN</span> @endif
                                            </div>
                                        @endif
                                        
                                        <div style="white-space: pre-line;">{{ $msg->message }}</div>
                                        
                                        <div class="text-end mt-1 {{ $isMe ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.7rem;">
                                            {{ $msg->created_at->format('H:i d/m') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- KHUNG NHẬP TIN NHẮN -->
                <div class="card-footer bg-white p-3 border-top">
                    @if($ticket->status != 'closed')
                        <form action="{{ route('admin.support.reply', $ticket->id) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="message" class="form-control border-primary" placeholder="Nhập phản hồi..." required autocomplete="off">
                                <button class="btn btn-primary px-4 fw-bold" type="submit">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Gửi
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-secondary mb-0 text-center small">
                            <i class="fa-solid fa-lock me-1"></i> Yêu cầu này đã đóng.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL XÁC NHẬN ĐÓNG YÊU CẦU -->
<div class="modal fade" id="closeTicketModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4 text-center">
                <div class="bg-success-subtle text-success rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-check-double fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Đóng yêu cầu này?</h5>
                <p class="text-muted mb-4">Sau khi đóng, người dùng sẽ không thể gửi thêm tin nhắn.<br>Bạn có chắc chắn muốn tiếp tục?</p>
                
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Hủy bỏ</button>
                    <form action="{{ route('admin.support.close', $ticket->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-dark px-4 fw-bold">Xác nhận Đóng</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL XÁC NHẬN XÓA YÊU CẦU -->
<div class="modal fade" id="deleteTicketModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4 text-center">
                <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-trash-can fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Xóa vĩnh viễn?</h5>
                <p class="text-muted mb-4">Toàn bộ nội dung trao đổi trong yêu cầu này sẽ bị xóa và không thể khôi phục.</p>
                
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Hủy bỏ</button>
                    <form action="{{ route('admin.support.destroy', $ticket->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Xóa ngay</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tự động cuộn xuống cuối khung chat khi tải trang
    document.addEventListener("DOMContentLoaded", function() {
        var chatBox = document.getElementById("chatBox");
        if(chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>
@endsection