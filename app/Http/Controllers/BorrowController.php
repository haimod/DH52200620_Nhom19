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
    // 1. API Lấy lịch bận (Giữ nguyên để hiển thị cho User biết giờ nào bận)
    public function getSchedule($maTB)
    {
        $device = ThietBi::where('maTB', $maTB)->first();

        if (!$device) {
            return response()->json([]);
        }

        $schedules = PhieuMuon::whereHas('chiTietMuon', function ($q) use ($device) {
                $q->where('thiet_bi_id', $device->id);
            })
            // Lấy cả đơn đang chờ duyệt để User biết mà tránh
            ->whereIn('trangThai', ['Active', 'Pending']) 
            ->where('ngayTraDuKien', '>=', now())
            ->with('user')
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

    // 2. XỬ LÝ MƯỢN / ĐẶT LỊCH (SỬA ĐỔI LOGIC DUYỆT)
    public function store(Request $request)
    {
        $request->validate([
            'thiet_bi_id' => 'required|exists:thietbi,id', // Lưu ý: Tên bảng trong DB là 'thietbi' (theo các bước trước)
            'ngayMuon' => 'required|date',
            'ngayTraDuKien' => 'required|date',
            'lyDo' => 'nullable|string|max:255',
        ]);

        $start = Carbon::parse($request->ngayMuon);
        $end = Carbon::parse($request->ngayTraDuKien);
        $thietBiId = $request->thiet_bi_id;

        // A. Validate thời gian
        $now = Carbon::now()->subMinutes(5);
        if ($start->lt($now)) {
            return back()->with('error', '⛔ Thời gian mượn không được nằm trong quá khứ!');
        }

        if ($end->lessThanOrEqualTo($start)) {
            return back()->with('error', '⛔ Ngày trả phải lớn hơn ngày mượn!');
        }

        // B. Kiểm tra trùng lịch (Vẫn giữ để User không đặt chồng lên nhau)
        $conflict = PhieuMuon::whereHas('chiTietMuon', function ($q) use ($thietBiId) {
                $q->where('thiet_bi_id', $thietBiId);
            })
            ->whereIn('trangThai', ['Active', 'Pending']) // Check cả đơn đã duyệt và đang chờ
            ->where(function ($query) use ($start, $end) {
                $query->where('ngayMuon', '<', $end)
                      ->where('ngayTraDuKien', '>', $start);
            })
            ->exists();

        if ($conflict) {
            return back()->with('error', '❌ Khoảng thời gian này đã có người khác đặt hoặc đang chờ duyệt! Vui lòng chọn giờ khác.');
        }

        // --- SỬA ĐỔI QUAN TRỌNG TẠI ĐÂY ---
        // Bất kể đặt bây giờ hay tương lai, trạng thái luôn là PENDING để Admin duyệt
        $status = 'Pending'; 

        try {
            DB::transaction(function () use ($request, $start, $end, $status, $thietBiId) {
                // 1. Tạo phiếu
                $phieuMuon = PhieuMuon::create([
                    'maPM' => 'PM-' . strtoupper(uniqid()),
                    'user_id' => Auth::id(),
                    'ngayMuon' => $start,
                    'ngayTraDuKien' => $end,
                    'ghiChu' => $request->lyDo,
                    'trangThai' => $status, // Luôn là Pending
                ]);

                // 2. Tạo chi tiết
                ChiTietMuon::create([
                    'phieu_muon_id' => $phieuMuon->id,
                    'thiet_bi_id' => $thietBiId,
                    'soLuongMuon' => 1,
                ]);

                // 3. KHÔNG CẬP NHẬT TRẠNG THÁI THIẾT BỊ NỮA
                // Việc set 'In_Use' sẽ do Admin làm khi bấm nút "Duyệt"
            });

            return back()->with('success', '✅ Yêu cầu mượn đã được gửi! Vui lòng chờ Admin phê duyệt.');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    // 3. Xem danh sách lịch sử mượn (Giữ nguyên)
    public function index(Request $request)
    {
        $query = PhieuMuon::where('user_id', Auth::id())
            ->with('chiTietMuon.thietBi');

        if ($request->filled('keyword')) {
            $key = $request->keyword;
            $query->where(function ($q) use ($key) {
                $q->where('ghiChu', 'like', "%$key%")
                ->orWhereHas('chiTietMuon.thietBi', function ($q2) use ($key) {
                    $q2->where('tenTB', 'like', "%$key%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('trangThai', $request->status);
        }

        $danhSach = $query
            ->orderBy('created_at', 'desc') // Sắp xếp theo ngày tạo mới nhất
            ->paginate(10)
            ->withQueryString();

        return view('borrow.index', compact('danhSach'));
    }

    // 4. Danh sách đang mượn để trả (Sửa query lấy Active)
    public function returnIndex(Request $request)
    {
        // Chỉ lấy những phiếu đang Active (Đã duyệt) để user bấm trả
        $query = PhieuMuon::where('user_id', Auth::id())
                          ->where('trangThai', 'Active'); 

        $dangMuon = $query->with('chiTietMuon.thietBi')
                          ->orderBy('ngayTraDuKien', 'asc')
                          ->paginate(10);

        return view('borrow.return', compact('dangMuon'));
    }

    // 5. XỬ LÝ: HỦY ĐƠN hoặc GỬI YÊU CẦU TRẢ (SỬA ĐỔI LOGIC)
    public function action(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $phieu = PhieuMuon::where('id', $id)
                            ->where('user_id', Auth::id())
                            ->firstOrFail();

                // TRƯỜNG HỢP 1: Đang mượn (Active) -> User bấm trả -> Chuyển thành Waiting_Return
                if ($phieu->trangThai == 'Active') {
                    
                    $phieu->update([
                        'trangThai' => 'Waiting_Return' // Chờ Admin nhận máy
                    ]);
                    
                    // KHÔNG update thiết bị về Available. Việc này Admin làm sau khi kiểm tra máy.

                // TRƯỜNG HỢP 2: Đang chờ duyệt (Pending) -> User muốn hủy -> Cancelled
                } elseif ($phieu->trangThai == 'Pending') {
                    
                    $phieu->update([
                        'trangThai' => 'Cancelled'
                    ]);
                    // Thiết bị vẫn an toàn vì chưa ai mượn
                }
            });

            return back()->with('success', 'Thao tác thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}