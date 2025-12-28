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
        // Tạo bảng cai_dat nếu chưa tồn tại
        if (!Schema::hasTable('cai_dat')) {
            Schema::create('cai_dat', function (Blueprint $table) {
                $table->id();
                $table->string('khoa')->unique();
                $table->text('gia_tri')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cai_dat');
    }
};

