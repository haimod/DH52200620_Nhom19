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

    // Thêm vào ThietBi.php
 // Thêm vào trong class ThietBi
    public function lichDatTruoc()
    {
        // Lấy những phiếu Pending (Đặt trước) của máy này
        return $this->hasMany(ChiTietMuon::class, 'maTB', 'maTB')
            ->whereHas('phieuMuon', function($q) {
                $q->where('trangThai', 'Pending') 
                  ->where('ngayMuon', '>', now()); // Chỉ tính lịch tương lai
            });
    }
}
