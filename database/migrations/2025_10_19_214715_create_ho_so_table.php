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
        Schema::create('ho_so', function (Blueprint $table) {
            $table->id();
            $table->integer('dich_vu_id');
            $table->integer('nguoi_dung_id');
            $table->integer('don_vi_id');
            $table->string('gio_hen')->nullable();
            $table->string('ngay_hen');
            $table->text('file_path')->nullable();
            $table->string('ghi_chu')->nullable();
            $table->string('trang_thai')->default('Đang chờ xử lý');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ho_so');
    }
};
