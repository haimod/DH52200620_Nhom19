<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThietBi extends Model
{
    use HasFactory;

    protected $table = 'thietbi';
    protected $primaryKey = 'maTB';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'maTB', 'tenTB', 'maLoai', 'maPhong',
        'tinhTrang', 'hanBaoHanh', 'soSerial', 'ngayMua'
    ];

    // Quan hệ: nhiều chi tiết mượn
    public function chiTietMuon()
    {
        return $this->hasMany(ChiTietMuon::class, 'maTB', 'maTB');
    }

    // Quan hệ: lấy phiếu mượn mới nhất
    public function muonMoiNhat()
    {
        return $this->hasOne(ChiTietMuon::class, 'maTB', 'maTB')
            ->latest('id');
    }
}
