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
        Schema::create('chi_tiet_muon', function (Blueprint $table) {
            $table->id(); // Khóa chính tự tăng (id)
            
            // Khai báo cột khóa ngoại (kiểu dữ liệu phải trùng với bảng cha)
            $table->string('maPM'); 
            $table->string('maTB'); 
            
            // Số lượng (Lưu ý logic bên dưới)
            $table->integer('soLuongMuon')->default(1); 
            
            $table->timestamps();

            // --- TẠO LIÊN KẾT KHÓA NGOẠI ---
            
            // 1. Liên kết với bảng Phiếu Mượn
            // onDelete('cascade'): Nếu xóa phiếu mượn, các chi tiết mượn của phiếu đó tự mất theo -> Tránh rác dữ liệu
            $table->foreign('maPM')
                  ->references('maPM')->on('phieu_muon')
                  ->onDelete('cascade');

            // 2. Liên kết với bảng Thiết Bị
            $table->foreign('maTB')
                  ->references('maTB')->on('thietbi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_muon');
    }
};