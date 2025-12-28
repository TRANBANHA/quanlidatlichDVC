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
        Schema::table('don_vi', function (Blueprint $table) {
            $table->string('qr_bank_name')->nullable()->after('mo_ta');
            $table->string('qr_account_number')->nullable()->after('qr_bank_name');
            $table->string('qr_account_name')->nullable()->after('qr_account_number');
            $table->string('qr_image')->nullable()->after('qr_account_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_vi', function (Blueprint $table) {
            $table->dropColumn(['qr_bank_name', 'qr_account_number', 'qr_account_name', 'qr_image']);
        });
    }
};
