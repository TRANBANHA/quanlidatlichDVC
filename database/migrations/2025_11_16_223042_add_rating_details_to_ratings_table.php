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
        Schema::table('ratings', function (Blueprint $table) {
            // Đánh giá chi tiết về các khía cạnh dịch vụ
            $table->integer('diem_thai_do')->nullable()->after('diem')->comment('Điểm đánh giá thái độ phục vụ (1-5)');
            $table->integer('diem_thoi_gian')->nullable()->after('diem_thai_do')->comment('Điểm đánh giá thời gian xử lý (1-5)');
            $table->integer('diem_chat_luong')->nullable()->after('diem_thoi_gian')->comment('Điểm đánh giá chất lượng dịch vụ (1-5)');
            $table->integer('diem_co_so_vat_chat')->nullable()->after('diem_chat_luong')->comment('Điểm đánh giá cơ sở vật chất (1-5)');
            
            // Các câu hỏi khác
            $table->boolean('co_nen_gioi_thieu')->nullable()->after('diem_co_so_vat_chat')->comment('Có nên giới thiệu dịch vụ này cho người khác không');
            $table->text('y_kien_khac')->nullable()->after('co_nen_gioi_thieu')->comment('Ý kiến khác của người dùng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropColumn([
                'diem_thai_do',
                'diem_thoi_gian',
                'diem_chat_luong',
                'diem_co_so_vat_chat',
                'co_nen_gioi_thieu',
                'y_kien_khac'
            ]);
        });
    }
};
