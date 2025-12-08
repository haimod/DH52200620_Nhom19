<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
{
    Schema::create('phieu_muon', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('maPM')->unique();

        // --- CÁCH KHẮC PHỤC: VIẾT TƯỜNG MINH ---
        
        // 1. Tạo cột user_id trước
        $table->uuid('user_id'); 

        // 2. Nối dây thủ công (Chỉ định rõ tên bảng là 'users')
        $table->foreign('user_id')
              ->references('id')->on('users')
              ->onDelete('cascade');

        $table->dateTime('ngayMuon');
        $table->dateTime('ngayTraDuKien');
        $table->string('trangThai')->default('ChoDuyet');
        $table->text('ghiChu')->nullable();
        $table->timestamps();
        
        // Đảm bảo dùng Engine InnoDB (Hỗ trợ khóa ngoại)
        $table->engine = 'InnoDB'; 
    });
}
    public function down(): void
    {
        Schema::dropIfExists('phieu_muon');
    }
};