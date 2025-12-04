@extends('layouts.main')
@section('title', 'Trả thiết bị')

@section('content')
<div class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Trả thiết bị</h3>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form action="" method="GET" class="row">
                <div class="col-md-6">
                    <input type="text" name="keyword" class="form-control" 
                           placeholder="Nhập mã phiếu hoặc tên máy..." 
                           value="{{ request('keyword') }}">
                </div>
                <div class="col-md-4">
                    <select name="type" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Tất cả --</option>
                        <option value="qua_han" {{ request('type') == 'qua_han' ? 'selected' : '' }}>
                            ⚠️ Chỉ hiện cái quá hạn
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tìm</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã Phiếu</th>
                        <th>Ngày mượn</th>
                        <th>Thiết bị</th>
                        <th>Hạn trả</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
<tbody>
    @forelse($dangMuon as $item)
        @php
            // 1. Tính toán ngày
            $hanTra = \Carbon\Carbon::parse($item->ngayTraDuKien);
            $isQuaHan = \Carbon\Carbon::now() > $hanTra;

            // 2. TẠO CLASS (Thay vì tạo Style)
            // Nếu quá hạn: chữ đỏ (text-danger) + in đậm (fw-bold)
            // Nếu không: để trống
            $classHienThi = $isQuaHan ? 'text-danger fw-bold' : '';

            // 3. Link trả
            $linkTra = route('return.action', $item->maPM);
        @endphp

        <tr>
            <td><b>#{{ $item->maPM }}</b></td>
            
            <td>{{ date('H:i d/m/Y', strtotime($item->ngayMuon)) }}</td>
            
            <td>
                @foreach($item->chiTietMuon as $ct)
                    <p class="mb-0">- {{ $ct->thietbi->tenTB }} ({{ $ct->maTB }})</p>
                @endforeach
            </td>
            
            {{-- SỬA Ở ĐÂY: Dùng class thay vì style --}}
            {{-- Bỏ luôn chữ 'format:' trong hàm format() cho gọn code --}}
            <td class="{{ $classHienThi }}">
                {{ $hanTra->format('H:i d/m/Y') }}
            </td>

            <td>
                @if($isQuaHan)
                    <span class="badge bg-danger">Quá hạn</span>
                @else
                    <span class="badge bg-success">Đang mượn</span>
                @endif
            </td>

            <td>
                <button type="button" class="btn btn-warning btn-sm"
                        onclick="hienModalTra('{{ $item->maPM }}', '{{ $linkTra }}')">
                    Trả đồ
                </button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">Không có dữ liệu.</td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>
        <div class="card-footer text-center">
            {{ $dangMuon->withQueryString()->links() }}
        </div>
    </div>
</div>

<div id="modalXacNhan" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="background: #fff; width: 400px; margin: 100px auto; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.3);">
        
        <h4>Xác nhận trả</h4>
        <hr>
        <p>Bạn có chắc muốn trả phiếu mượn <b id="textMaPhieu" style="color: blue;"></b> không?</p>
        
        <form id="formTra" method="POST" action="">
            @csrf
            <div class="text-end mt-3">
                <button type="button" class="btn btn-secondary" onclick="dongModal()">Hủy</button>
                <button type="submit" class="btn btn-warning">Đồng ý</button>
            </div>
        </form>

    </div>
</div>

@if(session('success'))
    <div id="thongBao" style="position: fixed; top: 20px; right: 20px; background: #28a745; color: #fff; padding: 15px 25px; border-radius: 5px; z-index: 10000; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
        <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
    </div>
    
    <script>
        // Tự động tắt sau 4 giây
        setTimeout(function() {
            document.getElementById('thongBao').style.display = 'none';
        }, 4000);
    </script>
@endif

@if(session('error'))
    <div id="thongBaoLoi" style="position: fixed; top: 20px; right: 20px; background: #dc3545; color: #fff; padding: 15px 25px; border-radius: 5px; z-index: 10000;">
        <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
    </div>

    <script>
        setTimeout(function() {
            document.getElementById('thongBaoLoi').style.display = 'none';
        }, 4000);
    </script>
@endif


<script>
    // Hàm hiện Modal
    function hienModalTra(maPhieu, linkAction) {
        // Điền mã phiếu vào chữ
        document.getElementById('textMaPhieu').innerText = '#' + maPhieu;
        // Điền link xử lý vào form
        document.getElementById('formTra').action = linkAction;
        // Hiện modal lên
        document.getElementById('modalXacNhan').style.display = 'block';
    }

    // Hàm đóng Modal
    function dongModal() {
        document.getElementById('modalXacNhan').style.display = 'none';
    }

    // Bấm ra ngoài vùng trắng thì đóng
    window.onclick = function(event) {
        var modal = document.getElementById('modalXacNhan');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

@endsection