<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thanh_toan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->foreignId('ho_so_id')->nullable()->constrained('ho_so')->onDelete('set null');
            $table->string('loai_hinh')->nullable(); // Loại hình thanh toán
            $table->string('ma_ban_ghi')->nullable(); // Mã bản ghi liên quan
            $table->decimal('so_tien', 10, 2)->default(0);
            $table->enum('trang_thai_thanh_toan', ['cho_thanh_toan', 'da_thanh_toan', 'that_bai', 'hoan_tien'])->default('cho_thanh_toan');
            $table->string('hinh_anh')->nullable();
            $table->text('giai_trinh')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanh_toan');
    }
};
