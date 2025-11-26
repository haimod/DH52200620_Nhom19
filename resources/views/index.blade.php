@extends('layouts.main')

@section('title', 'Trang chủ')

@section('content')
<div class="main-content">
    <div class="page-header">
        <h1>Trang chủ</h1>
        <p>Tổng quan về tình trạng thiết bị và hoạt động mượn trả</p>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Danh sách thiết bị</h2>
        </div>
        <div class="card-body">
            <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Mã thiết bị</th>
                        <th>Tên thiết bị</th>
                        <th>Loại</th>
                        <th>Phòng</th>
                        <th>Tình trạng</th>
                        <th>Hạn bảo hành</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($thietbi as $tb)
                        <tr>
                            <td>{{ $tb->maTB }}</td>
                            <td>{{ $tb->tenTB }}</td>
                            <td>{{ $tb->maLoai }}</td>
                            <td>{{ $tb->maPhong ?? 'Chưa có' }}</td>
                            <td>
                                @if($tb->tinhTrang == 'Available')
                                    <span class="status-available">Khả dụng</span>
                                @elseif($tb->tinhTrang == 'In_Use')
                                    <span class="status-in-use">Đang mượn</span>
                                @elseif($tb->tinhTrang == 'Maintenance')
                                    <span class="status-maintenance">Bảo trì</span>
                                @else
                                    <span class="status-broken">{{ $tb->tinhTrang }}</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($tb->hanBaoHanh)->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center">Chưa có thiết bị nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
