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
        Schema::table('phong_chat', function (Blueprint $table) {
            $table->foreignId('don_vi_id')->nullable()->after('quan_tri_id')->constrained('don_vi')->nullOnDelete();
            $table->boolean('use_rasa')->default(false)->after('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phong_chat', function (Blueprint $table) {
            $table->dropForeign(['don_vi_id']);
            $table->dropColumn(['don_vi_id', 'use_rasa']);
        });
    }
};
