<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Thêm cột avatar, cho phép null (để user cũ không bị lỗi), đặt sau cột email
            $table->string('avatar')->nullable()->after('email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Nếu rollback thì xóa cột này đi
            $table->dropColumn('avatar');
        });
    }
};
