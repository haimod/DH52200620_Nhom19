<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// 1. Import đầy đủ các Model cần dùng
use App\Models\User;
use App\Models\ThietBi;
use App\Models\PhieuMuon;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemAlert; 
class DashboardController extends Controller
{
    public function index()
    {
        // 2. THỐNG KÊ TỔNG QUAN (Dùng cho 4 thẻ trên cùng và biểu đồ)
        $stats = [
            // Đếm tổng User (loại trừ tài khoản admin ra)
            'total_users'     => User::where('vaiTro', '!=', 'Admin')->count(),
            
            // Đếm tổng thiết bị hiện có
            'total_devices'   => ThietBi::count(),
            
            // Đếm số phiếu cần xử lý (bao gồm 'Pending' - chờ duyệt và 'Waiting_Return' - chờ trả)
            'pending_borrows' => PhieuMuon::whereIn('trangThai', ['Pending', 'Waiting_Return'])->count(),
            
            // Đếm số ticket hỗ trợ đang chờ (status là 'pending')
            'open_tickets'    => SupportTicket::where('status', 'pending')->count(),

            // Thống kê chi tiết trạng thái thiết bị (Dùng cho thanh phần trăm bên phải)
            'available'       => ThietBi::where('tinhTrang', 'Available')->count(),
            'in_use'          => ThietBi::where('tinhTrang', 'In_Use')->count(),
            'broken'          => ThietBi::whereIn('tinhTrang', ['Broken', 'Maintenance', 'Liquidated'])->count(),
        ];

        // 3. HOẠT ĐỘNG MƯỢN TRẢ (Dùng cho mục "Mượn trong ngày/tháng")
        $activity = [
            'today' => PhieuMuon::whereDate('created_at', Carbon::today())->count(),
            'month' => PhieuMuon::whereMonth('created_at', Carbon::now()->month)
                                ->whereYear('created_at', Carbon::now()->year)
                                ->count(),
        ];

        // 4. Lấy danh sách hỗ trợ (SỬA ĐỔI: Phân trang 3 dòng)
        $recentTickets = SupportTicket::with('user') // Kèm thông tin người gửi
            ->whereIn('status', ['pending', 'processing'])
            ->orderByRaw("FIELD(status, 'pending', 'processing')")
            ->orderBy('updated_at', 'desc')
            ->paginate(3); // <--- Thay đổi ở đây: paginate(3) thay vì limit(5)->get()

        // 5. CẢNH BÁO MƯỢN TRẢ (Dùng cho mục "Sắp trễ hạn/Quá hạn")
        
        // Sắp trễ hạn: Đang mượn (Active) và hạn trả trong vòng 3 ngày tới
        $nearDeadline = PhieuMuon::where('trangThai', 'Active')
            ->where('ngayTraDuKien', '>=', Carbon::now())
            ->where('ngayTraDuKien', '<=', Carbon::now()->addDays(3))
            ->get();

        // Đã quá hạn: Đang mượn (Active) nhưng đã qua ngày trả
        $overdue = PhieuMuon::where('trangThai', 'Active')
            ->where('ngayTraDuKien', '<', Carbon::now())
            ->get();

        // Trả toàn bộ dữ liệu ra View
        return view('admin.dashboard.index', compact('stats', 'activity', 'recentTickets', 'nearDeadline', 'overdue'));
    }


    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string|max:500',
        ]);

        // Lấy danh sách nhân viên (loại trừ Admin)
        $users = User::where('vaiTro', '!=', 'Admin')->get();

        if ($users->count() > 0) {
            // Gửi thông báo hàng loạt
            Notification::send(
                $users,
                new SystemAlert(
                    $request->input('title'),
                    $request->input('content')
                )
            );

            return back()->with('success', 'Đã gửi thông báo đến ' . $users->count() . ' nhân viên.');
        }

        return back()->with('error', 'Không tìm thấy nhân viên nào để gửi.');
    }
}