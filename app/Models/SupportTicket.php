<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'subject', 'type', 'status'];

    // Quan hệ: 1 Ticket có nhiều tin nhắn
    public function messages()
    {
        // SỬA LỖI: Thêm dấu $ trước this
        return $this->hasMany(TicketMessage::class, 'ticket_id');
    }

    // Quan hệ: Ticket thuộc về 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}