<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportTicket;
use App\Models\TicketMessage;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // 1. Lấy danh sách Tickets của user (Mới nhất lên đầu)
        $tickets = SupportTicket::where('user_id', $userId)
                                ->orderBy('created_at', 'desc')
                                ->get();

        // 2. Xác định phiếu nào đang được chọn (Active)
        // Nếu trên URL có ?id=1 thì lấy phiếu số 1, không thì lấy phiếu đầu tiên
        $activeTicketId = $request->query('id');
        $activeTicket = null;

        if ($activeTicketId) {
            $activeTicket = $tickets->where('id', $activeTicketId)->first();
        } elseif ($tickets->count() > 0) {
            $activeTicket = $tickets->first();
        }

        // 3. Lấy tin nhắn của phiếu đang chọn (nếu có)
        $messages = [];
        if ($activeTicket) {
            $messages = TicketMessage::where('ticket_id', $activeTicket->id)
                                     ->with('user') // Lấy luôn thông tin người chat (avatar, tên)
                                     ->orderBy('created_at', 'asc')
                                     ->get();
        }

        return view('support.index', compact('tickets', 'activeTicket', 'messages'));
    }

    // Gửi yêu cầu mới (Form Modal)
    public function sendRequest(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'type' => 'required|string',
            'message' => 'required|string',
        ]);

        // Tạo Ticket
        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'type' => $request->type,
            'status' => 'pending'
        ]);

        // Tạo tin nhắn đầu tiên
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);
        
        return redirect()->route('support.index', ['id' => $ticket->id])
                         ->with('success', 'Đã gửi yêu cầu thành công!');
    }
    
    // Gửi tin nhắn trả lời (Form Chat ở giữa)
    public function sendMessage(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:support_tickets,id',
            'message' => 'required|string'
        ]);

        TicketMessage::create([
            'ticket_id' => $request->ticket_id,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        return back(); // Quay lại trang cũ để thấy tin nhắn mới
    }
}