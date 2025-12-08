<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lưu ý: Tên bảng là 'ThietBi' (phân biệt hoa thường trên Linux)
        Schema::create('thietbi', function(Blueprint $table){
            // 1. UUID làm khóa chính
            $table->uuid('id')->primary();

            // 2. Mã hiển thị (để in QR code dán lên máy)
            $table->string('maTB', 20)->unique();

            $table->string('maLoai', 20); // Có thể tách bảng LoaiThietBi sau này
            $table->string('tenTB', 100)->nullable();
            $table->string('soSerial', 50)->nullable();
            $table->enum('tinhTrang',['Available','In_Use','Maintenance','Broken','Liquidated'])->default('Available');
            $table->date('ngayMua')->nullable();
            $table->date('hanBaoHanh')->nullable();
            
            $table->timestamps(); // Nên thêm cái này để theo dõi ngày tạo

             $table->engine = 'InnoDB';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thietbi');
    }
};