<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Role;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // bảng chủ hộ

        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id(); // ID người dùng
            $table->string('ten'); // Họ và tên người dùng
            $table->string('email')->unique(); // Địa chỉ email
            $table->string('mat_khau'); // Mật khẩu
            $table->string('so_dien_thoai', 10); // Mật khẩu
            $table->integer('tinh_trang')->default(1); // Trạng thái kích hoạt
            $table->integer('phuong_id')->nullable(); // Phường
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
