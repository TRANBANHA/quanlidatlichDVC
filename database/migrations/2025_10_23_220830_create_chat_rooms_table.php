<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('phong_chat', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phong')->unique();
            // Quan hệ người dùng (users), để null khi khách
            $table->foreignId('nguoi_dung_id')->nullable()  ;
            // Quan hệ quản trị viên (quan_tri_vien), để null khi chưa gán
            $table->integer('quan_tri_id')->nullable();
            // Thông tin người dùng hiển thị
            $table->string('ten_nguoi_dung');
            $table->string('email_nguoi_dung')->nullable();
            $table->string('so_dien_thoai')->nullable();
            // Trạng thái phòng
            $table->enum('trang_thai', ['waiting', 'active', 'closed'])->default('waiting')->index();
            // Thời điểm hoạt động cuối cùng
            $table->timestamp('hoat_dong_cuoi')->nullable()->index();
            $table->timestamps();

            // Các chỉ mục hữu ích
            $table->index(['quan_tri_id', 'trang_thai']);
        });
    }

    public function down ()
    {
        Schema::dropIfExists('phong_chat');
    }
};