<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dich_vu_truong_du_lieu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dich_vu_id')->constrained('dich_vu')->onDelete('cascade');
            $table->string('ten_truong'); // Tên trường (VD: giay_khai_sinh, cmnd)
            $table->string('nhan_hien_thi'); // Nhãn hiển thị
            $table->enum('loai_truong', ['text', 'textarea', 'file', 'date', 'select', 'number'])->default('text');
            $table->boolean('bat_buoc')->default(false);
            $table->text('tuy_chon')->nullable(); // JSON cho select options
            $table->text('placeholder')->nullable();
            $table->text('goi_y')->nullable();
            $table->integer('thu_tu')->default(0); // Thứ tự hiển thị
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dich_vu_truong_du_lieu');
    }
};
