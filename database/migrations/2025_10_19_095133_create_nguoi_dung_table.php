<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->string('code', 7)->unique()->nullable(); // Mã xác nhận
            $table->integer('duong_id')->nullable();  // không cần after()
            $table->integer('so_nha_id')->nullable(); // không cần after()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn('duong_id');
            $table->dropColumn('so_nha_id');
        });
    }
};
