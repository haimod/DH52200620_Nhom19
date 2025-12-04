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
        Schema::create('phieu_muon', function (Blueprint $table) {
            // Khóa chính là chuỗi (nếu bạn muốn tự đặt mã như PM001)
            $table->string('maPM')->primary(); 
            
            // Khóa ngoại liên kết với nhân viên
            $table->string('maNV'); 
            
            // Các thông tin mượn
            $table->dateTime('ngayMuon');
            $table->dateTime('ngayTraDuKien');
            $table->string('trangThai')->default('ChoDuyet'); // ChoDuyet, DaDuyet, DaTra, TuChoi
            
            // Cột bắt buộc created_at, updated_at
            $table->timestamps();
            $table->text('ghiChu');
            // Thiết lập khóa ngoại (Lưu ý: Bảng 'nhan_vien' phải tồn tại trước thì dòng này mới chạy được)
            // Nếu chưa có bảng nhan_vien thì tạm thời comment dòng dưới lại
            // $table->foreign('maNV')->references('maNV')->on('nhan_vien')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_muon');
    }
};