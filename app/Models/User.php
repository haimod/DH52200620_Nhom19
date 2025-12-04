<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// 1. Đã xóa dòng use Laravel\Sanctum... gây lỗi

class User extends Authenticatable
{
    // 2. Đã xóa HasApiTokens khỏi dòng này
    use HasFactory, Notifiable;

   protected $primaryKey = 'maNV';
    public $incrementing = false; 
    protected $keyType = 'string';


  protected $fillable = [
    'maNV',
    'tenDangNhap',
    'password',
    'vaiTro',
    'hoTen',
    'email',
    'soDienThoai',
    'phongBan',
];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}