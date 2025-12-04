<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PhieuMuon extends Model
{
    use HasFactory;

    protected $table = 'phieu_muon';
    protected $primaryKey = 'maPM';
    public $incrementing = true;

    public $timestamps = false; // nếu bảng không có created_at, updated_at

    protected $fillable = [
        'maNV',
        'ngayMuon',
        'ngayTraDuKien',
        'ghiChu',
        'trangThai'
    ];

    // Quan hệ tới người mượn (User)
    public function user()
    {
        return $this->belongsTo(User::class, 'maNV', 'maNV');
    }

    // Quan hệ tới chi tiết mượn
    public function chiTietMuon()
    {
        return $this->hasMany(ChiTietMuon::class, 'maPM', 'maPM');
    }

    
}
