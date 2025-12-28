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
        // Tạo bảng thong_bao_admin nếu chưa tồn tại
        if (!Schema::hasTable('thong_bao_admin')) {
            Schema::create('thong_bao_admin', function (Blueprint $table) {
                $table->id();
                $table->string('tieu_de');
                $table->text('noi_dung');
                $table->date('ngay_dang');
                $table->date('ngay_het_han')->nullable();
                $table->string('hinh_anh')->nullable();
                $table->string('video')->nullable();
                $table->unsignedBigInteger('nguoi_dung_id')->nullable();
                $table->integer('type')->default(0)->comment('0: Thông báo chung, 1: Thông báo cá nhân');
                $table->timestamps();

                // Foreign key
                $table->foreign('nguoi_dung_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
                
                // Indexes
                $table->index('ngay_dang');
                $table->index('ngay_het_han');
                $table->index('nguoi_dung_id');
                $table->index('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_bao_admin');
    }
};

