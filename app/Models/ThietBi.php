<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Import UUID

class ThietBi extends Model
{
    use HasFactory, HasUuids; // 2. Kích hoạt UUID

    protected $table = 'ThietBi'; // Lưu ý chữ hoa thường phải khớp DB
    
    // Mặc định timestamps là true (nên dùng để theo dõi ngày tạo)
    public $timestamps = true; 

    protected $fillable = [
        'maTB', // Mã hiển thị (QR Code)
        'tenTB', 
        'maLoai', 
        'maPhong',
        'tinhTrang', 
        'hanBaoHanh', 
        'soSerial', 
        'ngayMua',
        'viTri',
    ];

    // --- QUAN HỆ ---

    // 1. Một thiết bị có nhiều dòng chi tiết mượn (Lịch sử mượn)
    public function chiTietMuon()
    {
        // Laravel tự hiểu khóa ngoại là 'thiet_bi_id' dựa trên tên Model
        return $this->hasMany(ChiTietMuon::class);
    }

    // 2. Lấy người đang mượn máy này (Thông qua chi tiết mượn mới nhất)
    public function muonMoiNhat()
    {
        return $this->hasOne(ChiTietMuon::class)->latest('id');
    }

    // 3. Lịch đặt trước (Logic phức tạp hơn chút vì phải chọc qua bảng PhieuMuon)
    public function lichDatTruoc()
    {
        return $this->hasMany(ChiTietMuon::class)
            ->whereHas('phieuMuon', function($q) {
                $q->where('trangThai', 'Pending') // Hoặc 'ChoDuyet' tùy enum của bạn
                  ->where('ngayMuon', '>', now());
            });
    }
}