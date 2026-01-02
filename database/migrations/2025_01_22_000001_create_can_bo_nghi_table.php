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
        Schema::create('can_bo_nghi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('can_bo_id'); // ID cán bộ báo nghỉ
            $table->date('ngay_nghi'); // Ngày nghỉ
            $table->text('ly_do')->nullable(); // Lý do nghỉ
            $table->boolean('da_chuyen_ho_so')->default(false); // Đã chuyển hồ sơ chưa
            $table->timestamps();

            // Foreign key
            $table->foreign('can_bo_id')->references('id')->on('quan_tri_vien')->onDelete('cascade');
            
            // Index để tìm nhanh cán bộ nghỉ trong ngày
            $table->index(['can_bo_id', 'ngay_nghi']);
            $table->index('ngay_nghi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('can_bo_nghi');
    }
};
