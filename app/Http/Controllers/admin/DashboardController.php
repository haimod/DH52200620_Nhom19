<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// 1. Import đầy đủ các Model cần dùng
use App\Models\User;
use App\Models\SupportTicket;
use App\Models\ThietBi;   // Model quản lý Thiết bị (Tài sản)
use App\Models\PhieuMuon; // Model quản lý Mượn trả

class DashboardController extends Controller
{
    public function index()
    {
        // 2. Truy vấn dữ liệu thật từ Database
        $stats = [
            // Đếm tổng User (loại trừ tài khoản admin ra)
            'total_users' => User::where('vaiTro', '!=', 'admin')->count(),
            
            // Đếm tổng thiết bị hiện có
            'total_devices' => ThietBi::count(), 
            
            // --- SỬA Ở ĐÂY ---
            // Đếm số phiếu cần xử lý (bao gồm 'Pending' - chờ duyệt và 'Waiting_Return' - chờ trả)
            // Đổi tên key thành 'pending_borrows' để khớp với View
            'pending_borrows' => PhieuMuon::whereIn('trangThai', ['Pending', 'Waiting_Return'])->count(),
            
            // Đếm số ticket hỗ trợ đang chờ (status là 'pending')
            'open_tickets' => SupportTicket::where('status', 'pending')->count(),
        ];

        // 3. Lấy 5 yêu cầu hỗ trợ mới nhất để hiện ra bảng bên dưới
        $recentTickets = SupportTicket::with('user') // Kèm thông tin người gửi
                                      ->where('status', 'pending')
                                      ->orderBy('created_at', 'desc')
                                      ->take(5)
                                      ->get();

        return view('admin.dashboard.index', compact('stats', 'recentTickets'));
    }
}