<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ
        DB::table('users')->delete();

        // --- Admin ---
        DB::table('users')->insert([
            'id' => Str::uuid(),
            'maNV' => 'ADMIN01',
            'tenDangNhap' => 'admin',
            'password' => Hash::make('admin123'), // mật khẩu admin
            'vaiTro' => 'admin', 
            'hoTen' => 'Quản Trị Hệ Thống',
            'email' => 'admin@example.com',
            'soDienThoai' => '0900000000',
            'phongBan' => 'Ban Quản Trị',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // --- Nhân viên demo ---
        DB::table('users')->insert([
            'id' => Str::uuid(),
            'maNV' => 'NV003',
            'tenDangNhap' => 'userdemo',
            'password' => Hash::make('123456'),
            'vaiTro' => 'NhanVien',
            'hoTen' => 'Trần Đức Hải',
            'email' => 'ducpm@example.com',
            'soDienThoai' => '0987123456',
            'phongBan' => 'Phòng Hành Chính',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
