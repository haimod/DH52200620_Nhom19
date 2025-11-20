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
        Schema::create('ThietBi', function(Blueprint $table){
            $table->string('maTB',20)->primary();
            $table->string('maLoai',20);
            $table->string('tenTB',100)->nullable();
            $table->string('soSerial',50)->nullable();
            $table->enum('tinhTrang',['Available','In_Use','Maintenance','Broken','Liquidated'])->default('Available');
            $table->date('ngayMua')->nullable();
            $table->date('hanBaoHanh')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ThietBi');
    }
};
