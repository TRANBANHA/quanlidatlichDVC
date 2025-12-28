<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->foreignId('quan_tri_vien_id')->nullable()->after('nguoi_dung_id')->constrained('quan_tri_vien')->onDelete('set null')->comment('Cán bộ được đánh giá');
        });
    }

    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropForeign(['quan_tri_vien_id']);
            $table->dropColumn('quan_tri_vien_id');
        });
    }
};
