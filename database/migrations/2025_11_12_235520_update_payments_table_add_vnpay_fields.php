<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thanh_toan', function (Blueprint $table) {
            $table->string('ma_giao_dich')->nullable()->after('so_tien');
            $table->enum('phuong_thuc_thanh_toan', ['qr_code', 'tien_mat', 'chuyen_khoan'])->default('tien_mat')->after('ma_giao_dich');
            $table->text('du_lieu_vnpay')->nullable()->after('phuong_thuc_thanh_toan')->comment('Dữ liệu JSON từ VNPay');
            $table->timestamp('ngay_thanh_toan')->nullable()->after('du_lieu_vnpay');
        });
    }

    public function down(): void
    {
        Schema::table('thanh_toan', function (Blueprint $table) {
            $table->dropColumn(['ma_giao_dich', 'phuong_thuc_thanh_toan', 'du_lieu_vnpay', 'ngay_thanh_toan']);
        });
    }
};
