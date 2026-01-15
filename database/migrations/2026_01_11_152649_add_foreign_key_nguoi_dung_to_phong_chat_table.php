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
        Schema::table('phong_chat', function (Blueprint $table) {
            // Thêm foreign key constraint cho nguoi_dung_id tham chiếu đến bảng nguoi_dung
            $table->foreign('nguoi_dung_id')
                  ->references('id')
                  ->on('nguoi_dung')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phong_chat', function (Blueprint $table) {
            // Xóa foreign key constraint
            $table->dropForeign(['nguoi_dung_id']);
        });
    }
};
