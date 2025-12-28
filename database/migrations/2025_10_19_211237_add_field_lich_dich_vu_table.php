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
        Schema::table('lich_dich_vu', function (Blueprint $table) {
            $table->text('file_dinh_kem')->nullable()->comment('File đính kèm hướng dẫn hoặc tài liệu liên quan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_dich_vu', function (Blueprint $table) {
            $table->dropColumn('file_dinh_kem');
        });
    }
};
