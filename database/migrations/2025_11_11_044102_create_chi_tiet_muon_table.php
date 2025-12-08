<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
public function up(): void
{
    Schema::create('chi_tiet_muon', function (Blueprint $table) {
        $table->uuid('id')->primary();

        // --- KHẮC PHỤC NỐI DÂY ---

        // 1. Tạo 2 cột chứa ID
        $table->uuid('phieu_muon_id');
        $table->uuid('thiet_bi_id');

        // 2. Nối sang bảng PhieuMuon
        $table->foreign('phieu_muon_id')
              ->references('id')->on('phieu_muon')
              ->onDelete('cascade');

        // 3. Nối sang bảng ThietBi
        // LƯU Ý: Ở dòng on('...'), bạn phải điền CHÍNH XÁC tên bảng trong Database 
        // (Xem trong phpMyAdmin, nó là 'ThietBi' hay 'thietbi'?)
        // Thường Laravel sẽ chuyển về chữ thường. Mình để 'ThietBi' theo code cũ của bạn.
        $table->foreign('thiet_bi_id')
              ->references('id')->on('thietbi') 
              ->onDelete('cascade');

        $table->integer('soLuongMuon')->default(1);
        $table->timestamps();
        
        $table->engine = 'InnoDB';
    });
}
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_muon');
    }
};