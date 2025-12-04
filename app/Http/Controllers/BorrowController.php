<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhieuMuon;
use App\Models\ChiTietMuon;
use App\Models\ThietBi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BorrowController extends Controller
{
    // 1. API Lấy lịch bận (Cho Modal)
    public function getSchedule($id)
    {
        $schedules = PhieuMuon::join('chi_tiet_muon', 'phieu_muon.maPM', '=', 'chi_tiet_muon.maPM')
            ->join('users', 'phieu_muon.maNV', '=', 'users.maNV') // Join thêm bảng users
            ->where('chi_tiet_muon.maTB', $id)
            ->whereIn('phieu_muon.trangThai', ['Active', 'Pending']) 
            ->where('phieu_muon.ngayTraDuKien', '>=', now())
            ->select(
                'phieu_muon.ngayMuon', 
                'phieu_muon.ngayTraDuKien', 
                'phieu_muon.trangThai',
                'users.hoTen' // Lấy thêm tên người mượn
            )
            ->orderBy('phieu_muon.ngayMuon', 'asc')
            ->get();

        return response()->json($schedules);
    }

    // 2. Xử lý Mượn / Đặt lịch
    public function store(Request $request)
    {
        $request->validate([
            'thietbi' => 'required|string|exists:thietbi,maTB',
            'ngayMuon' => 'required|date',
            'ngayTraDuKien' => 'required|date|after:ngayMuon',
            'lyDo' => 'nullable|string|max:255',
        ]);

        $start = Carbon::parse($request->ngayMuon);
        $end = Carbon::parse($request->ngayTraDuKien);

        // A. Kiểm tra trùng lịch (Quan trọng)
        $conflict = PhieuMuon::join('chi_tiet_muon', 'phieu_muon.maPM', '=', 'chi_tiet_muon.maPM')
            ->where('chi_tiet_muon.maTB', $request->thietbi)
            ->whereIn('phieu_muon.trangThai', ['Active', 'Pending']) // Check cả đang mượn và đã đặt
            ->where(function ($query) use ($start, $end) {
                // Logic: (StartA < EndB) và (EndA > StartB) là công thức trùng lặp thời gian
                $query->where('phieu_muon.ngayMuon', '<', $end)
                      ->where('phieu_muon.ngayTraDuKien', '>', $start);
            })
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Khoảng thời gian này đã có người khác đặt rồi! Vui lòng chọn giờ khác.');
        }

        // B. Tính toán trạng thái (FIX LỖI CỦA BẠN Ở ĐÂY)
        // Nếu thời gian mượn <= thời gian hiện tại (+5 phút du di) -> Là mượn ngay (Active)
        // Ngược lại -> Là đặt trước (Pending)
        $isBorrowNow = $start <= now()->addMinutes(5);
        $status = $isBorrowNow ? 'Active' : 'Pending';

        try {
            DB::transaction(function () use ($request, $start, $end, $status) {
                // 1. Tạo phiếu
                $phieuMuon = PhieuMuon::create([
                    'maNV' => Auth::user()->maNV,
                    'ngayMuon' => $start,
                    'ngayTraDuKien' => $end,
                    'ghiChu' => $request->lyDo,
                    'trangThai' => $status, // Lưu trạng thái động (Active/Pending)
                ]);

                // 2. Lưu chi tiết
                ChiTietMuon::create([
                    'maPM' => $phieuMuon->maPM,
                    'maTB' => $request->thietbi,
                    'soLuongMuon' => 1,
                ]);

                // 3. Cập nhật thiết bị
                // CHỈ cập nhật In_Use nếu là mượn ngay (Active)
                // Nếu là Pending thì KHÔNG cập nhật (để người hiện tại vẫn dùng bình thường)
                if ($status == 'Active') {
                    ThietBi::where('maTB', $request->thietbi)->update(['tinhTrang' => 'In_Use']);
                }
            });

            $msg = $status == 'Active' ? 'Mượn thiết bị thành công!' : 'Đặt lịch thành công! Vui lòng đến lấy máy đúng giờ.';
            return back()->with('success', $msg);

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }


    // --- HÀM CÒN THIẾU: Xem danh sách lịch sử mượn ---
    public function index()
    {
        // Lấy danh sách phiếu của người đang đăng nhập
        $danhSach = PhieuMuon::where('maNV', Auth::user()->maNV)
            ->with('chiTietMuon.thietbi') // Load kèm thông tin thiết bị
            ->orderBy('ngayMuon', 'desc') // Mới nhất lên đầu
            ->paginate(10);

        return view('borrow.index', compact('danhSach'));
    }
    // 3. Danh sách trả / hủy
    public function returnIndex(Request $request)
    {
        $query = PhieuMuon::where('maNV', Auth::user()->maNV)
                          ->whereIn('trangThai', ['Active', 'Pending']); // Lấy cả 2 loại

        if ($request->filled('keyword')) {
            $key = $request->keyword;
            $query->where(function($q) use ($key) {
                $q->where('maPM', 'like', "%$key%")
                  ->orWhereHas('chiTietMuon.thietbi', function($q2) use ($key){
                      $q2->where('tenTB', 'like', "%$key%");
                  });
            });
        }

        if ($request->filled('type')) {
            if($request->type == 'qua_han') {
                $query->where('ngayTraDuKien', '<', now())->where('trangThai', 'Active');
            } else {
                $query->where('trangThai', $request->type); // Lọc theo active hoặc pending
            }
        }

        $dangMuon = $query->with('chiTietMuon.thietbi')
                          ->orderBy('ngayTraDuKien', 'asc')
                          ->paginate(10)
                          ->withQueryString();

        return view('borrow.return', compact('dangMuon'));
    }

    // 4. Xử lý Trả đồ hoặc Hủy lịch (QUAN TRỌNG)
    public function action(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $phieu = PhieuMuon::where('maPM', $id)
                            ->where('maNV', Auth::user()->maNV)
                            ->firstOrFail();

                // Trường hợp 1: Trả đồ (khi đang Active)
                if ($phieu->trangThai == 'Active') {
                    $phieu->update([
                        'trangThai' => 'Closed',
                        'ngayTraThucTe' => Carbon::now()
                    ]);
                    
                    // Trả trạng thái máy về Available
                    $maThietBis = $phieu->chiTietMuon->pluck('maTB');
                    ThietBi::whereIn('maTB', $maThietBis)->update(['tinhTrang' => 'Available']);
                
                // Trường hợp 2: Hủy lịch (khi đang Pending)
                } elseif ($phieu->trangThai == 'Pending') {
                    $phieu->update([
                        'trangThai' => 'Cancelled' // Chuyển sang hủy
                    ]);
                    // Không cần update bảng ThietBi vì máy chưa chuyển sang In_Use
                }
            });

            return back()->with('success', 'Thao tác thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    
}