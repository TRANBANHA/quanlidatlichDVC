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
        Schema::create('phan_cong_dich_vu', function (Blueprint $table) {
            $table->id();    
            $table->json('ma_can_bo')->nullable();
            $table->integer('ma_dich_vu');
            $table->date('ngay_phan_cong')->nullable();
            $table->string('ghi_chu', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phan_cong_dich_vu');
    }
};
