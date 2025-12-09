<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Thêm dòng này để dùng lệnh SQL trực tiếp

return new class extends Migration
{
    public function up()
    {
        // 1. Tạo bảng support_tickets (Chưa nối dây vội)
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Bắt buộc dùng InnoDB
            $table->id(); // ID số (BigInt)
            $table->uuid('user_id'); // UUID (để khớp với users)
            $table->string('subject');
            $table->string('type')->default('other');
            $table->enum('status', ['pending', 'processing', 'resolved', 'closed'])->default('pending');
            $table->timestamps();
        });

        // 2. Tạo bảng ticket_messages (Chưa nối dây vội)
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Bắt buộc dùng InnoDB
            $table->id();
            $table->unsignedBigInteger('ticket_id'); // ID số (để khớp với support_tickets)
            $table->uuid('user_id'); // UUID (để khớp với users)
            $table->text('message');
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        // 3. THỰC HIỆN NỐI DÂY (Dùng Schema::table tách biệt)
        // Cách này an toàn hơn vì đảm bảo 2 bảng đã được tạo xong xuôi
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->foreign('ticket_id')->references('id')->on('support_tickets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        // Xóa bảng con trước, cha sau
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('support_tickets');
    }
};