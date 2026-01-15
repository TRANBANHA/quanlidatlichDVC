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
        Schema::table('ho_so', function (Blueprint $table) {
            // Thêm cột da_thanh_toan_tien_mat với giá trị mặc định là 0
            $table->tinyInteger('da_thanh_toan_tien_mat')->default(0)->comment('0: Chưa thanh toán, 1: Đã thanh toán');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ho_so', function (Blueprint $table) {
            $table->dropColumn('da_thanh_toan_tien_mat');
        });
    }
};
