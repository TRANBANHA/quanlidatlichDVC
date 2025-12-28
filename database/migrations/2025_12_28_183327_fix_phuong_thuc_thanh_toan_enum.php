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
        // Đảm bảo cột phuong_thuc_thanh_toan có ENUM đúng
        // Nếu cột chưa tồn tại, tạo mới
        if (!Schema::hasColumn('thanh_toan', 'phuong_thuc_thanh_toan')) {
            Schema::table('thanh_toan', function (Blueprint $table) {
                $table->enum('phuong_thuc_thanh_toan', ['qr_code', 'tien_mat', 'chuyen_khoan'])
                      ->default('tien_mat')
                      ->after('ma_giao_dich');
            });
        } else {
            // Cập nhật ENUM nếu cột đã tồn tại
            // Bước 1: Mở rộng ENUM tạm thời để chứa tất cả giá trị có thể
            DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN phuong_thuc_thanh_toan ENUM('qr_code', 'tien_mat', 'chuyen_khoan', 'vnpay', 'zalopay') DEFAULT 'tien_mat'");
            
            // Bước 2: Cập nhật các giá trị không hợp lệ
            DB::table('thanh_toan')
                ->whereNotIn('phuong_thuc_thanh_toan', ['qr_code', 'tien_mat', 'chuyen_khoan'])
                ->update(['phuong_thuc_thanh_toan' => 'tien_mat']);
            
            // Bước 3: Thu hẹp ENUM về chỉ 3 giá trị hợp lệ
            DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN phuong_thuc_thanh_toan ENUM('qr_code', 'tien_mat', 'chuyen_khoan') DEFAULT 'tien_mat'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần rollback vì đây là migration sửa lỗi
    }
};
