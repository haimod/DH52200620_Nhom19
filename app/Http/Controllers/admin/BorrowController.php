<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhieuMuon;
use App\Models\ThietBi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BorrowController extends Controller
{
    // 1. HIỂN THỊ DANH SÁCH (Phân chia theo Tab)
    public function index(Request $request)
    {
        // Tab 1: Chờ duyệt (Pending) - Giữ nguyên get() vì ít dữ liệu
        $pending = PhieuMuon::with(['user', 'chiTietMuon.thietBi'])
            ->where('trangThai', 'Pending')
            ->orderBy('created_at', 'asc')
            ->get();

        // Tab 2: Đang mượn & Chờ trả - Giữ nguyên get()
        $ongoing = PhieuMuon::with(['user', 'chiTietMuon.thietBi'])
            ->whereIn('trangThai', ['Active', 'Waiting_Return'])
            ->orderByRaw("FIELD(trangThai, 'Waiting_Return', 'Active')")
            ->get();

        // Tab 3: Lịch sử - SỬA ĐỔI: Dùng Paginate + Logic Tìm kiếm
        $historyQuery = PhieuMuon::with(['user', 'chiTietMuon.thietBi'])
            ->whereIn('trangThai', ['Closed', 'Cancelled', 'Rejected']);

        // a. Xử lý tìm kiếm (Keyword)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $historyQuery->where(function($q) use ($keyword) {
                // Tìm theo tên người mượn
                $q->whereHas('user', function($subQ) use ($keyword) {
                    $subQ->where('hoTen', 'like', "%{$keyword}%");
                })
                // Hoặc tìm theo tên thiết bị
                ->orWhereHas('chiTietMuon.thietBi', function($subQ) use ($keyword) {
                    $subQ->where('tenTB', 'like', "%{$keyword}%");
                });
            });
        }

        // b. Xử lý lọc trạng thái (Status)
        if ($request->filled('status')) {
            $historyQuery->where('trangThai', $request->status);
        }

        // c. Phân trang (Thay vì get() -> paginate(5))
        $history = $historyQuery->orderBy('updated_at', 'desc')
            ->paginate(5); 

        return view('admin.borrow.index', compact('pending', 'ongoing', 'history'));
    }

    // 2. DUYỆT ĐƠN (Pending -> Active)
    public function approve($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $phieu = PhieuMuon::with('chiTietMuon')->findOrFail($id);
                
                // Kiểm tra lại lần cuối xem thiết bị có rảnh không
                foreach($phieu->chiTietMuon as $ct) {
                    $device = ThietBi::find($ct->thiet_bi_id);
                    if($device->tinhTrang != 'Available') {
                        throw new \Exception("Thiết bị {$device->tenTB} hiện không khả dụng!");
                    }
                    
                    // Cập nhật thiết bị: Đổi sang In_Use + Cập nhật vị trí người giữ
                    $device->update([
                        'tinhTrang' => 'In_Use',
                       // Cập nhật vị trí thành tên Phòng ban của nhân viên (nếu chưa có phòng ban thì lấy tên người)
                        'viTri' => $phieu->user->phongBan ?? ('User: ' . $phieu->user->hoTen),
                    ]);
                }

                // Cập nhật phiếu
                $phieu->update(['trangThai' => 'Active']);
            });

            return back()->with('success', 'Đã duyệt phiếu mượn thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // 3. TỪ CHỐI ĐƠN (Pending -> Rejected)
    public function reject($id)
    {
        PhieuMuon::where('id', $id)->update(['trangThai' => 'Rejected']);
        return back()->with('success', 'Đã từ chối phiếu mượn.');
    }

    // 4. NHẬN TRẢ THIẾT BỊ (Active/Waiting_Return -> Closed)
    public function returnDevice(Request $request, $id)
    {
        $request->validate([
            'condition' => 'required', // Normal, Broken, Lost
            'note' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $id) {
            $phieu = PhieuMuon::with('chiTietMuon')->findOrFail($id);
            
            // Cập nhật phiếu
            $phieu->update([
                'trangThai' => 'Closed',
                // 'ngayTraThucTe' => now(), 
                'ghiChu' => $request->note . " (Tình trạng trả: " . $request->condition . ")"
            ]);

            // Xử lý thiết bị dựa trên tình trạng trả
            foreach($phieu->chiTietMuon as $ct) {
                $device = ThietBi::find($ct->thiet_bi_id);
                
                if ($request->condition == 'Broken') {
                    // Hỏng -> Chuyển sang Bảo trì
                    $device->update([
                        'tinhTrang' => 'Broken',
                        'viTri' => 'Kho Bảo Trì',
                        'ngayBaoTriTiepTheo' => now()
                    ]);
                } elseif ($request->condition == 'Lost') {
                    // Mất -> Update trạng thái đặc biệt
                    $device->update([
                        'tinhTrang' => 'Liquidated', 
                        'viTri' => 'Mất thất lạc'
                    ]);
                } else {
                    // Bình thường -> Về kho
                    $device->update([
                        'tinhTrang' => 'Available',
                        'viTri' => 'Kho Trung Tâm'
                    ]);
                }
            }
        });

        return back()->with('success', 'Đã xác nhận trả thiết bị.');
    }
}