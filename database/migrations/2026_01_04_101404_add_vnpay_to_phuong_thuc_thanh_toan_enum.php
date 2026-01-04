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
        // Thêm 'vnpay' vào ENUM phuong_thuc_thanh_toan
        // Bước 1: Mở rộng ENUM tạm thời để chứa vnpay
        DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN phuong_thuc_thanh_toan ENUM('qr_code', 'tien_mat', 'chuyen_khoan', 'vnpay') DEFAULT 'tien_mat'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Loại bỏ 'vnpay' khỏi ENUM và cập nhật các bản ghi có vnpay thành tien_mat
        // Bước 1: Cập nhật các payment có vnpay thành tien_mat
        DB::table('thanh_toan')
            ->where('phuong_thuc_thanh_toan', 'vnpay')
            ->update(['phuong_thuc_thanh_toan' => 'tien_mat']);
        
        // Bước 2: Thu hẹp ENUM về chỉ 3 giá trị (loại bỏ vnpay)
        DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN phuong_thuc_thanh_toan ENUM('qr_code', 'tien_mat', 'chuyen_khoan') DEFAULT 'tien_mat'");
    }
};
