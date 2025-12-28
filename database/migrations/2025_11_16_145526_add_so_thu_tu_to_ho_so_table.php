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
        Schema::table('ho_so', function (Blueprint $table) {
            $table->integer('so_thu_tu')->nullable()->after('ngay_hen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ho_so', function (Blueprint $table) {
            $table->dropColumn('so_thu_tu');
        });
    }
};
