<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {

            
            // 1. Dùng UUID làm khóa chính (System ID)
             $table->engine = 'InnoDB'; // <--- THÊM DÒNG NÀY ĐỂ CHẮC CHẮN NỐI ĐƯỢC
            $table->uuid('id')->primary(); 
            
            // 2. Mã NV để hiển thị (Human ID) - Vẫn giữ để bạn in thẻ, nhưng ko dùng nối bảng
            $table->string('maNV')->unique(); 
            
            $table->string('tenDangNhap')->unique();
            $table->string('password');
            $table->string('vaiTro')->default('NhanVien');
            $table->string('hoTen');
            $table->string('email')->unique();
            $table->string('soDienThoai')->nullable();
            $table->string('phongBan')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};