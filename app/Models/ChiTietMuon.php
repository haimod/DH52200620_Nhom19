<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietMuon extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_muon';
    public $timestamps = false; // Nếu bảng không có created_at, updated_at

    protected $fillable = [
        'maPM',
        'maTB',
        'soLuongMuon',
    ];

    // Quan hệ đến phiếu mượn
    public function phieuMuon()
    {
        return $this->belongsTo(PhieuMuon::class, 'maPM', 'maPM');
    }

    // Quan hệ đến thiết bị
    public function thietBi()
    {
        return $this->belongsTo(ThietBi::class, 'maTB', 'maTB');
    }
}
