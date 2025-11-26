@extends('layouts.main')

@section('title', 'Trang chủ')

@section('content')
<div class="main-content">
    <div class="page-header">
        <h1>Trang chủ</h1>
        <p>Tổng quan về tình trạng thiết bị và hoạt động mượn trả</p>
    </div>

    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Danh sách thiết bị</h2>
            
            <form action="/" method="GET" style="display: flex; gap: 10px;">
                <select name="status" style="padding: 5px;" onchange="this.form.submit()">
                    <option value="all">-- Tất cả --</option>
                    <option value="Available" {{ $status == 'Available' ? 'selected' : '' }}>Khả dụng</option>
                    <option value="In_Use" {{ $status == 'In_Use' ? 'selected' : '' }}>Đang mượn</option>
                    <option value="Maintenance" {{ $status == 'Maintenance' ? 'selected' : '' }}>Bảo trì</option>
                    <option value="Broken" {{ $status == 'Broken' ? 'selected' : '' }}>Hỏng</option>
                </select>

                <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Nhập mã hoặc tên..." style="padding: 5px;">

                <button type="submit" style="padding: 5px 10px; cursor: pointer;">Tìm</button>
            </form>
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
                            <td>{{ $tb->hanBaoHanh ? \Carbon\Carbon::parse($tb->hanBaoHanh)->format('d/m/Y') : '-' }}</td>
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