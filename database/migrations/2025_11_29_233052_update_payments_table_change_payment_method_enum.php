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
        // Bước 1: Thêm qr_code vào enum (tạm thời giữ nguyên vnpay và zalopay)
        DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN phuong_thuc_thanh_toan ENUM('vnpay', 'zalopay', 'qr_code', 'tien_mat', 'chuyen_khoan') DEFAULT 'tien_mat'");

        // Bước 2: Cập nhật các payment có phuong_thuc_thanh_toan = 'vnpay' thành 'qr_code'
        DB::table('thanh_toan')
            ->where('phuong_thuc_thanh_toan', 'vnpay')
            ->update(['phuong_thuc_thanh_toan' => 'qr_code']);

        // Bước 3: Thay đổi enum để loại bỏ vnpay và zalopay, chỉ giữ qr_code, tien_mat, chuyen_khoan
        DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN phuong_thuc_thanh_toan ENUM('qr_code', 'tien_mat', 'chuyen_khoan') DEFAULT 'tien_mat'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục enum cũ
        DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN phuong_thuc_thanh_toan ENUM('vnpay', 'zalopay', 'tien_mat', 'chuyen_khoan') DEFAULT 'tien_mat'");
        
        // Cập nhật lại các payment có qr_code về vnpay (nếu cần)
        DB::table('thanh_toan')
            ->where('phuong_thuc_thanh_toan', 'qr_code')
            ->update(['phuong_thuc_thanh_toan' => 'vnpay']);
    }
};
