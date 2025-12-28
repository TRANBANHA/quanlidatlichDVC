<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lich_dich_vu', function (Blueprint $table) {
            $table->id(); // Mã định danh
            $table->integer('dich_vu_id')->nullable();
            $table->tinyInteger('thu_trong_tuan')->comment('1=Thứ 2, 7=Chủ nhật');
            $table->boolean('trang_thai')->default(true)->comment('1=Hoạt động, 0=Không hoạt động');
            $table->time('gio_bat_dau')->nullable()->comment('Giờ bắt đầu làm việc');
            $table->time('gio_ket_thuc')->nullable()->comment('Giờ kết thúc làm việc');
            $table->integer('so_luong_toi_da')->default(10)->comment('Số lượng tối đa cho phép đăng ký');
            $table->string('ghi_chu')->nullable()->comment('Ghi chú thêm');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_dich_vu');
    }
};
