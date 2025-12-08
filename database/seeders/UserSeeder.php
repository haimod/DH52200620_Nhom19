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
    DB::table('users')->delete();

  // Nhân viên mới
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