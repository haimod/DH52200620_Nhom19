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
        Schema::table('thietbi', function (Blueprint $table) {
            // Thêm cột viTri, cho phép null, mặc định là 'Kho Trung Tâm'
            // Đặt cột này nằm sau cột tinhTrang cho dễ nhìn trong Database
            $table->string('viTri')->nullable()->default('Kho Trung Tâm')->after('tinhTrang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thietbi', function (Blueprint $table) {
            // Xóa cột này nếu rollback
            $table->dropColumn('viTri');
        });
    }
};