<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ChiTietMuon extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'chi_tiet_muon';

    protected $fillable = [
        'phieu_muon_id', // Khóa ngoại
        'thiet_bi_id',   // Khóa ngoại
        'soLuongMuon',
    ];

    // --- MỐI QUAN HỆ ---

    public function phieuMuon()
    {
        return $this->belongsTo(PhieuMuon::class);
    }

    public function thietBi()
    {
        // Lưu ý: Model tên là ThietBi, nên belongsTo này vẫn đúng
        return $this->belongsTo(ThietBi::class);
    }
}