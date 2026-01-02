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
        Schema::table('can_bo_nghi', function (Blueprint $table) {
            $table->string('trang_thai')->default('cho_duyet')->after('ly_do');
            $table->unsignedBigInteger('nguoi_duyet_id')->nullable()->after('trang_thai');
            $table->timestamp('ngay_duyet')->nullable()->after('nguoi_duyet_id');
            $table->text('ghi_chu_duyet')->nullable()->after('ngay_duyet');
            
            // Foreign key cho người duyệt
            $table->foreign('nguoi_duyet_id')->references('id')->on('quan_tri_vien')->onDelete('set null');
            
            // Index để tìm nhanh các báo nghỉ chờ duyệt
            $table->index('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('can_bo_nghi', function (Blueprint $table) {
            $table->dropForeign(['nguoi_duyet_id']);
            $table->dropIndex(['trang_thai']);
            $table->dropColumn(['trang_thai', 'nguoi_duyet_id', 'ngay_duyet', 'ghi_chu_duyet']);
        });
    }
};
