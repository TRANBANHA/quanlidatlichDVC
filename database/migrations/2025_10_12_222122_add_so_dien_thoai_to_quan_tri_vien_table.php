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
            $table->string('so_dien_thoai')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quan_tri_vien', function (Blueprint $table) {
            $table->dropColumn('so_dien_thoai');
        });
    }
};
