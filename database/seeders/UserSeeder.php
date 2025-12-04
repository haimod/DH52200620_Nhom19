<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
{
    DB::table('users')->delete();

    // Admin
    DB::table('users')->insert([
        'maNV' => 'DH52200620', // Khóa chính
        'tenDangNhap' => 'admin',
        'password' => Hash::make('123456'),
        'vaiTro' => 'Thực tập',
        'hoTen' => 'Trần Đức Hải',
        'email' => 'hai@student.stu.edu.vn',
        'soDienThoai' => '0969961752',
        'phongBan' => 'D22_TH12 - Nhóm 19',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Nhân viên test
    DB::table('users')->insert([
        'maNV' => 'NV002',
        'tenDangNhap' => 'nhanvien',
        'password' => Hash::make('123456'),
        'vaiTro' => 'NhanVien',
        'hoTen' => 'Nguyễn Văn A',
        'email' => 'nva@gmail.com',
        'soDienThoai' => '0123456789',
        'phongBan' => 'Phòng Kế Toán',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
}