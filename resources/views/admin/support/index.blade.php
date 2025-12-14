@extends('layouts.admin')
@section('title', 'Trung tâm Hỗ trợ (Admin)')
@section('header_title', 'Danh sách Yêu cầu Hỗ trợ')

@section('content')
<div class="container-fluid p-4">
    
    <!-- Bộ lọc trạng thái -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark m-0">Quản lý Yêu cầu</h5>
        <form method="GET" action="{{ route('admin.support.index') }}" class="d-flex gap-2">
            <select name="status" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Chờ xử lý</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>🛠 Đang xử lý</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>✅ Đã giải quyết</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>🔒 Đã đóng</option>
            </select>
        </form>
    </div>

    <!-- Bảng danh sách -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Mã #</th>
                        <th>Người gửi</th>
                        <th>Chủ đề & Loại</th>
                        <th>Trạng thái</th>
                        <th>Cập nhật</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr class="{{ $ticket->status == 'pending' ? 'bg-warning bg-opacity-10 fw-bold' : '' }}">
                        <td class="ps-4 text-muted">#{{ $ticket->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-2" style="width:35px;height:35px;">
                                    {{ substr($ticket->user->hoTen ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-dark">{{ $ticket->user->hoTen ?? 'Unknown' }}</div>
                                    <small class="text-muted fw-normal">{{ $ticket->user->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-dark">{{ $ticket->subject }}</div>
                            <span class="badge bg-light text-secondary border mt-1">{{ $ticket->type }}</span>
                            @if($ticket->messages_count > 0)
                                <span class="badge bg-secondary ms-1">{{ $ticket->messages_count }} tin nhắn</span>
                            @endif
                        </td>
                        <td>
                            @if($ticket->status == 'pending')
                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                            @elseif($ticket->status == 'processing')
                                <span class="badge bg-info text-dark">Đang xử lý</span>
                            @elseif($ticket->status == 'resolved')
                                <span class="badge bg-success">Đã xong</span>
                            @else
                                <span class="badge bg-secondary">Đã đóng</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $ticket->updated_at->diffForHumans() }}
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.support.show', $ticket->id) }}" class="btn btn-primary btn-sm shadow-sm">
                                <i class="fa-solid fa-comments me-1"></i> Xử lý
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-inbox fa-2x mb-2 opacity-50"></i><br>Không có yêu cầu nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-center">
            {{ $tickets->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection