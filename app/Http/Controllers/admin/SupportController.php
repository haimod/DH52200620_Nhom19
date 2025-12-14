<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    // 1. Xem danh sách yêu cầu (Chỉ dành cho Admin)
    public function index(Request $request)
    {
        // Lấy tất cả ticket kèm thông tin người gửi
        $query = SupportTicket::with('user')->withCount('messages');

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sắp xếp: Pending lên đầu -> Processing -> Resolved -> Closed
        // Và ưu tiên những cái mới cập nhật (có tin nhắn mới)
        $tickets = $query->orderByRaw("FIELD(status, 'pending', 'processing', 'resolved', 'closed')")
                         ->orderBy('updated_at', 'desc')
                         ->paginate(10);

        // Trả về View riêng của Admin: admin.support.index
        return view('admin.support.index', compact('tickets'));
    }

    // 2. Xem chi tiết & Chat (Giao diện Admin)
    public function show($id)
    {
        $ticket = SupportTicket::with(['messages.user', 'user'])->findOrFail($id);
        
        // Trả về View chi tiết riêng của Admin: admin.support.show
        return view('admin.support.show', compact('ticket'));
    }

    // 3. Admin trả lời tin nhắn
    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);

        $ticket = SupportTicket::findOrFail($id);

        // Tạo tin nhắn từ phía Admin
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(), // ID của Admin đang đăng nhập
            'message' => $request->message
        ]);

        // Cập nhật trạng thái ticket
        if ($ticket->status == 'pending' || $ticket->status == 'closed') {
            $ticket->status = 'processing';
        }
        
        $ticket->touch(); // Đẩy ticket lên đầu danh sách
        $ticket->save();

        return back()->with('success', 'Đã gửi phản hồi.');
    }

    // 4. Admin đóng ticket
    public function close($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['status' => 'closed']);
        
        return back()->with('success', 'Đã đóng yêu cầu.');
    }

    // 5. Admin xóa ticket
    public function destroy($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->delete();

        return redirect()->route('admin.support.index')->with('success', 'Đã xóa yêu cầu.');
    }
}