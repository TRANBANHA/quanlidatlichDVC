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
        Schema::create('service_schedule_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('lich_dich_vu')->onDelete('cascade');
            $table->foreignId('can_bo_id')->constrained('quan_tri_vien')->onDelete('cascade');
            $table->timestamps();
            
            // Unique: Mỗi cán bộ chỉ được phân công 1 lần cho 1 lịch
            $table->unique(['schedule_id', 'can_bo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_schedule_staff');
    }
};
