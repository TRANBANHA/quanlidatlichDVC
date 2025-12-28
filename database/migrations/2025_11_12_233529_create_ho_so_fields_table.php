<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ho_so_truong_du_lieu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ho_so_id')->constrained('ho_so')->onDelete('cascade');
            $table->string('ten_truong'); // Tên trường
            $table->text('gia_tri')->nullable(); // Giá trị (text hoặc đường dẫn file)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ho_so_truong_du_lieu');
    }
};
