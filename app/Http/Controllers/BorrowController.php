<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhieuMuon;
use App\Models\ChiTietMuon;
use App\Models\ThietBi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Đảm bảo đã có dòng này

class BorrowController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate
        $request->validate([
            'thietbi' => 'required|exists:thietbi,maTB',
            'ngayMuon' => 'required|date',
            'ngayTraDuKien' => 'required|date|after:ngayMuon',
            'lyDo' => 'nullable|string|max:255', // Form gửi lên tên là lyDo
        ]);

        // Kiểm tra thiết bị bận
        $isBusy = ThietBi::where('maTB', $request->thietbi)
                         ->where('tinhTrang', '!=', 'Available')
                         ->exists();

        
        try {
            DB::transaction(function () use ($request) {
                // A. Tạo phiếu mượn
                // Nguyên tắc của Eloquent tạo 1 bảng ghi insert vào PhieuMuon
                $phieuMuon = PhieuMuon::create([
                    'maNV' => Auth::user()->maNV,
                    
                    // SỬA 1: Dùng Carbon để ép kiểu ngày tháng cho chuẩn MySQL
                    'ngayMuon' => Carbon::parse($request->ngayMuon),
                    'ngayTraDuKien' => Carbon::parse($request->ngayTraDuKien),
                    
                    // SỬA 2: Lấy đúng tên biến từ Form (lyDo thay vì ghiChu)
                    // Lưu ý: Trong Database bảng phieu_muon PHẢI CÓ cột 'ghiChu' nhé!
                    'ghiChu' => $request->lyDo, 
                    
                    'trangThai' => 'Active',
                ]);

                // B. Lưu chi tiết
               
                    ChiTietMuon::create([
                      'maPM' => $phieuMuon->maPM,
                    'maTB' => $request->thietbi,

                    'soLuongMuon' => 1,
                    ]);

                    // C. Update trạng thái thiết bị
                    ThietBi::where('maTB', $request->thietbi)->update(['tinhTrang' => 'In_Use']);
                
            });

            return back()->with('success', 'Đăng ký mượn thành công!');

        } catch (\Exception $e) {
            // SỬA 3: In lỗi ra màn hình để biết tại sao không lưu được
            dd($e->getMessage()); 
           // return back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại!');

        }
    }

public function index(Request $request)
    {
        // 1. Khởi tạo Query: Lấy phiếu của người đang đăng nhập
        $query = PhieuMuon::where('maNV', Auth::user()->maNV);

        // 2. Xử lý Lọc theo Trạng thái (nếu người dùng chọn)
        if ($request->filled('status')) {
            $query->where('trangThai', $request->status);
        }

        // 3. Xử lý Tìm kiếm từ khóa (nếu người dùng nhập)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            
            $query->where(function($q) use ($keyword) {
                // Tìm theo Mã phiếu
                $q->where('maPM', 'like', "%{$keyword}%")
                  // Hoặc tìm theo Ghi chú
                  ->orWhere('ghiChu', 'like', "%{$keyword}%")
                  // Hoặc tìm theo Tên thiết bị (kỹ thuật tìm trong bảng liên kết)
                  ->orWhereHas('chiTietMuon.thietbi', function($q2) use ($keyword){
                      $q2->where('tenTB', 'like', "%{$keyword}%");
                  });
            });
        }

        // 4. Lấy dữ liệu, nạp sẵn quan hệ (Eager Loading) và phân trang
        $danhSach = $query->with('chiTietMuon.thietbi') // Load sẵn tên thiết bị để không bị chậm
                          ->orderBy('ngayMuon', 'desc') // Mới nhất lên đầu
                          ->paginate(5)
                          ->withQueryString(); // Giữ lại tham số tìm kiếm khi bấm trang 2, 3...

        // Trả về View (Bạn kiểm tra xem file view của bạn tên là 'borrow.index' hay 'history.index' nhé)
        // Dựa vào code bạn gửi thì mình để là 'borrow.index'
        return view('borrow.index', compact('danhSach'));
    }

public function returnIndex(Request $request)
{
    // Lấy các phiếu ĐANG MƯỢN (Active) của nhân viên này
    $query = PhieuMuon::where('maNV', Auth::user()->maNV)
                      ->where('trangThai', 'Active');

    // 1. Lọc theo từ khóa (Tìm mã phiếu hoặc tên thiết bị)
    if ($request->filled('keyword')) {
        $key = $request->keyword;
        $query->where(function($q) use ($key) {
            $q->where('maPM', 'like', "%$key%")
              ->orWhereHas('chiTietMuon.thietbi', function($q2) use ($key){
                  $q2->where('tenTB', 'like', "%$key%");
              });
        });
    }

    // 2. Lọc theo loại (Nếu chọn 'qua_han')
    if ($request->type == 'qua_han') {
        $query->where('ngayTraDuKien', '<', now());
    }

    // Lấy dữ liệu và phân trang
    $dangMuon = $query->with('chiTietMuon.thietbi')
                      ->orderBy('ngayTraDuKien', 'asc') // Hạn gần nhất xếp trên đầu
                      ->paginate(10)
                      ->withQueryString();

    return view('borrow.return', compact('dangMuon'));
}


public function returnDevice($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Tìm phiếu mượn theo ID
                // Phải đúng là phiếu của người đang đăng nhập và đang trạng thái Active
                $phieu = PhieuMuon::where('maPM', $id)
                            ->where('maNV', Auth::user()->maNV)
                            ->where('trangThai', 'Active')
                            ->firstOrFail();

                // Cập nhật phiếu mượn -> Đã trả (Closed)
                $phieu->update([
                    'trangThai' => 'Closed',
                    'ngayTraThucTe' => Carbon::now()
                ]);

                // Lấy danh sách các máy trong phiếu này để cập nhật trạng thái
                $maThietBis = $phieu->chiTietMuon->pluck('maTB');
                
                // Cập nhật bảng ThietBi -> Trả về trạng thái 'Available' (Rảnh)
                ThietBi::whereIn('maTB', $maThietBis)->update(['tinhTrang' => 'Available']);
            });

            return back()->with('success', 'Đã trả thiết bị thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
// Hàm returnDevice (xử lý trả) giữ nguyên như mình đã gửi ở câu trước nhé!
}