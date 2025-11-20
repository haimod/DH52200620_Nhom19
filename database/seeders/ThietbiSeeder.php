<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThietbiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ThietBi')->insert([
            [
                'maTB' => 'TB001',
                'maLoai' => 'LT001',
                'tenTB' => 'Laptop Dell',
                'soSerial' => 'SN12345',
                'tinhTrang' => 'Available',
                'ngayMua' => '2024-01-01',
                'hanBaoHanh' => '2026-01-01',
            ],
            [
                'maTB' => 'TB002',
                'maLoai' => 'LT002',
                'tenTB' => 'Máy in HP',
                'soSerial' => 'SN54321',
                'tinhTrang' => 'In_Use',
                'ngayMua' => '2023-05-10',
                'hanBaoHanh' => '2025-05-10',
            ],
        ]);
    }
}
