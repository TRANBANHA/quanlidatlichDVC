<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dich_vu_phuong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dich_vu_id')->constrained('dich_vu')->onDelete('cascade');
            $table->foreignId('don_vi_id')->constrained('don_vi')->onDelete('cascade');
            $table->integer('thoi_gian_xu_ly')->default(0)->comment('Thời gian xử lý (ngày)');
            $table->integer('so_luong_toi_da')->default(10)->comment('Số lượng tối đa mỗi ngày');
            $table->decimal('phi_dich_vu', 10, 2)->default(0)->comment('Phí dịch vụ (VNĐ)');
            $table->boolean('kich_hoat')->default(true);
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
            
            // Unique: Mỗi dịch vụ chỉ có 1 bản ghi cho 1 phường
            $table->unique(['dich_vu_id', 'don_vi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dich_vu_phuong');
    }
};
