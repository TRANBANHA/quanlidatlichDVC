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
        Schema::table('quan_tri_vien', function (Blueprint $table) {
            $table->unsignedBigInteger('don_vi_id')->nullable()->after('so_dien_thoai');
            $table->foreign('don_vi_id')->references('id')->on('don_vi')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quan_tri_vien', function (Blueprint $table) {
            $table->dropForeign(['don_vi_id']);
            $table->dropColumn('don_vi_id');
        });
    }
};
