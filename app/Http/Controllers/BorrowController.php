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
    // 1. API Lấy lịch bận (Cho Modal hiển thị lịch)
    // Input: $maTB (String hiển thị - VD: "LT-001") từ Javascript gửi lên
    public function getSchedule($maTB)
    {
        // Tìm thiết bị theo mã hiển thị để lấy UUID thực
        $device = ThietBi::where('maTB', $maTB)->first();

        if (!$device) {
            return response()->json([]);
        }

        // Truy vấn qua Relationship: Tìm các phiếu mượn có chứa thiết bị này
        $schedules = PhieuMuon::whereHas('chiTietMuon', function ($q) use ($device) {
                $q->where('thiet_bi_id', $device->id); // So sánh UUID
            })
            ->whereIn('trangThai', ['Active', 'Pending']) // Chỉ lấy đơn đang chạy hoặc chờ
            ->where('ngayTraDuKien', '>=', now()) // Lấy lịch tương lai
            ->with('user') // Eager load user để lấy tên
            ->orderBy('ngayMuon', 'asc')
            ->get()
            ->map(function ($phieu) {
                return [
                    'ngayMuon' => $phieu->ngayMuon,
                    'ngayTraDuKien' => $phieu->ngayTraDuKien,
                    'trangThai' => $phieu->trangThai,
                    'hoTen' => $phieu->user ? $phieu->user->hoTen : 'Người dùng hệ thống'
                ];
            });

        return response()->json($schedules);
    }

    // 2. Xử lý Mượn / Đặt lịch (Lưu vào DB)
    public function store(Request $request)
    {
        $request->validate([
            // Quan trọng: Validate thiet_bi_id là UUID tồn tại trong bảng ThietBi
            'thiet_bi_id' => 'required|exists:ThietBi,id', 
            'ngayMuon' => 'required|date',
            'ngayTraDuKien' => 'required|date',
            'lyDo' => 'nullable|string|max:255',
        ]);

        $start = Carbon::parse($request->ngayMuon);
        $end = Carbon::parse($request->ngayTraDuKien);
        $thietBiId = $request->thiet_bi_id; // Đây là UUID

   // 🔥 A. TỰ KIỂM TRA NGÀY MƯỢN KHÔNG ĐƯỢC QUÁ KHỨ

                $now = Carbon::now()->subMinutes(5);
        if ($start->lt($now)) {
            return back()->with('error', '⛔ Thời gian mượn không được nằm trong quá khứ!');
        }


        if ($end->lessThanOrEqualTo($start)) {
    return back()->with('error', '⛔ Ngày trả phải lớn hơn ngày mượn!');
}
                // C. Giới hạn thời gian mượn tối đa 4 giờ
        $diffHours = $start->diffInHours($end);

        if ($diffHours > 4) {
            return back()->with('error', '⛔ Bạn chỉ được mượn/đặt lịch thiết bị tối đa 4 giờ!');
        }
        // A. Kiểm tra trùng lịch
        // Logic: Tìm xem có phiếu nào chứa thiết bị này mà thời gian bị chồng chéo không
        $conflict = PhieuMuon::whereHas('chiTietMuon', function ($q) use ($thietBiId) {
                $q->where('thiet_bi_id', $thietBiId);
            })
            ->whereIn('trangThai', ['Active', 'Pending'])
            ->where(function ($query) use ($start, $end) {
                // Công thức trùng lịch: (StartA < EndB) và (EndA > StartB)
                $query->where('ngayMuon', '<', $end)
                      ->where('ngayTraDuKien', '>', $start);
            })
            ->exists();

        if ($conflict) {
            return back()->with('error', '❌ Khoảng thời gian này đã có người khác đặt rồi! Vui lòng chọn giờ khác.');
        }

        // B. Tính toán trạng thái
        // Nếu mượn ngay bây giờ (sai số 5 phút) -> Active. Nếu tương lai -> Pending
        $isBorrowNow = $start <= now()->addMinutes(5);
        $status = $isBorrowNow ? 'Active' : 'Pending';

        try {
            DB::transaction(function () use ($request, $start, $end, $status, $thietBiId) {
                // 1. Tạo phiếu (Bảng Cha)
                $phieuMuon = PhieuMuon::create([
                    'maPM' => 'PM-' . strtoupper(uniqid()), // Sinh mã hiển thị
                    'user_id' => Auth::id(), // QUAN TRỌNG: Lưu UUID của User
                    'ngayMuon' => $start,
                    'ngayTraDuKien' => $end,
                    'ghiChu' => $request->lyDo,
                    'trangThai' => $status,
                ]);

                // 2. Lưu chi tiết (Bảng Con)
                ChiTietMuon::create([
                    'phieu_muon_id' => $phieuMuon->id, // UUID phiếu
                    'thiet_bi_id' => $thietBiId,       // UUID thiết bị
                    'soLuongMuon' => 1,
                ]);

                // 3. Cập nhật trạng thái thiết bị
                // Chỉ cập nhật thành In_Use nếu là mượn ngay (Active)
                if ($status == 'Active') {
                    ThietBi::where('id', $thietBiId)->update(['tinhTrang' => 'In_Use']);
                }
            });

            $msg = $status == 'Active' ? '✅ Mượn thiết bị thành công!' : '📅 Đặt lịch thành công! Vui lòng đến lấy máy đúng giờ.';
            return back()->with('success', $msg);

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    // 3. Xem danh sách lịch sử mượn
    public function index(Request $request)
    {
        // Lấy phiếu của User đang đăng nhập (so khớp user_id = Auth::id())
        $query = PhieuMuon::where('user_id', Auth::id())
            ->with('chiTietMuon.thietBi'); // Eager load để lấy thông tin thiết bị

        // Lọc theo keyword (tên hoặc Ghi chú)
            if ($request->filled('keyword')) {
            $key = $request->keyword;

            $query->where(function ($q) use ($key) {
                $q->where('ghiChu', 'like', "%$key%")
                ->orWhereHas('chiTietMuon.thietBi', function ($q2) use ($key) {
                    $q2->where('tenTB', 'like', "%$key%");
                });
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('trangThai', $request->status);
        }

        $danhSach = $query
            ->orderBy('ngayMuon', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('borrow.index', compact('danhSach'));
    }

    // 4. Danh sách thiết bị đang mượn (để trả)
    public function returnIndex(Request $request)
    {
        $query = PhieuMuon::where('user_id', Auth::id())
                          ->whereIn('trangThai', ['Active', 'Pending']);

        if ($request->filled('keyword')) {
            $key = $request->keyword;
            $query->where(function($q) use ($key) {
                $q->where('maPM', 'like', "%$key%")
                  ->orWhereHas('chiTietMuon.thietBi', function($q2) use ($key){
                      $q2->where('tenTB', 'like', "%$key%");
                  });
            });
        }

        if ($request->filled('type')) {
            if($request->type == 'qua_han') {
                $query->where('ngayTraDuKien', '<', now())->where('trangThai', 'Active');
            } else {
                $query->where('trangThai', $request->type);
            }
        }

        $dangMuon = $query->with('chiTietMuon.thietBi')
                          ->orderBy('ngayTraDuKien', 'asc')
                          ->paginate(10)
                          ->withQueryString();

        return view('borrow.return', compact('dangMuon'));
    }

    // 5. Xử lý Trả đồ hoặc Hủy lịch
    public function action(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                // $id ở đây là UUID của PhieuMuon (do Route model binding hoặc truyền ID)
                $phieu = PhieuMuon::where('id', $id) // Tìm theo UUID
                            ->where('user_id', Auth::id()) // Check đúng chủ sở hữu
                            ->firstOrFail();

                // Trường hợp 1: Trả đồ (khi đang Active)
                if ($phieu->trangThai == 'Active') {
                    $phieu->update([
                        'trangThai' => 'Closed',
                        // Cần thêm cột ngayTraThucTe vào migration nếu chưa có, hoặc bỏ dòng này
                        // 'ngayTraThucTe' => Carbon::now() 
                    ]);
                    
                    // Trả trạng thái máy về Available
                    // Lấy danh sách UUID thiết bị trong phiếu
                    $thietBiIds = $phieu->chiTietMuon->pluck('thiet_bi_id');
                    ThietBi::whereIn('id', $thietBiIds)->update(['tinhTrang' => 'Available']);
                
                // Trường hợp 2: Hủy lịch (khi đang Pending)
                } elseif ($phieu->trangThai == 'Pending') {
                    $phieu->update([
                        'trangThai' => 'Cancelled'
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