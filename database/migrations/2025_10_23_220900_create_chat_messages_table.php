<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tin_nhan', function (Blueprint $table) {
            $table->id();
            // Tham chiếu tới phòng chat
            $table->foreignId('phong_chat_id')->constrained('phong_chat')->cascadeOnDelete();
            // Người gửi: có thể là user hoặc admin (không FK để linh hoạt)
            $table->unsignedBigInteger('nguoi_gui_id')->nullable()->index();
            $table->enum('loai_nguoi_gui', ['user', 'admin'])->index();
            $table->string('ten_nguoi_gui');
            $table->text('tin_nhan');
            $table->string('loai_tin_nhan')->default('text');
            $table->boolean('da_doc')->default(false);
            $table->timestamps();

            // Index phục vụ truy vấn lịch sử nhanh
            $table->index(['phong_chat_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tin_nhan');
    }
};