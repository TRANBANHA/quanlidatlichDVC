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
        Schema::table('nguoi_dung', function (Blueprint $table) {
            // Xóa các cột cũ
            $table->dropColumn(['phuong_id', 'duong_id', 'so_nha_id']);
            
            // Thêm cột mới
            $table->integer('don_vi_id')->nullable()->after('cccd');
            $table->text('dia_chi')->nullable()->after('don_vi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            // Xóa cột mới
            $table->dropColumn(['don_vi_id', 'dia_chi']);
            
            // Khôi phục cột cũ
            $table->integer('phuong_id')->nullable()->after('cccd');
            $table->integer('duong_id')->nullable()->after('phuong_id');
            $table->integer('so_nha_id')->nullable()->after('duong_id');
        });
    }
};
