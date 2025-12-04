<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        // Khóa chính là maNV
        $table->string('maNV')->primary(); 
        
        // Các cột khác (Đã xóa maTK)
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
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
