<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm 'email' vào enum loai_truong
        DB::statement("ALTER TABLE `dich_vu_truong_du_lieu` MODIFY COLUMN `loai_truong` ENUM('text', 'textarea', 'file', 'date', 'select', 'number', 'email') DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa 'email' khỏi enum (chỉ nếu không có dữ liệu nào sử dụng 'email')
        DB::statement("ALTER TABLE `dich_vu_truong_du_lieu` MODIFY COLUMN `loai_truong` ENUM('text', 'textarea', 'file', 'date', 'select', 'number') DEFAULT 'text'");
    }
};
