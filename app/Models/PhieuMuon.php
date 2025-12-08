<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Import UUID

class PhieuMuon extends Model
{
    use HasFactory, HasUuids; // 2. Kích hoạt UUID

    protected $table = 'phieu_muon';

    protected $fillable = [
        'maPM',      // Mã hiển thị (VD: PM-001)
        'user_id',   // KHÓA NGOẠI: Liên kết với bảng users
        'ngayMuon',
        'ngayTraDuKien',
        'ghiChu',
        'trangThai'
    ];

    // --- QUAN HỆ ---

    // 1. Phiếu này thuộc về 1 User
    public function user()
    {
        // Mặc định Laravel tìm cột 'user_id' trong bảng phieu_muon -> Chính xác
        return $this->belongsTo(User::class);
    }

    // 2. Phiếu này có nhiều chi tiết (mượn nhiều máy 1 lúc)
    public function chiTietMuon()
    {
        // Mặc định Laravel tìm cột 'phieu_muon_id' trong bảng chi_tiet_muon
        return $this->hasMany(ChiTietMuon::class);
    }
}