<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ho_so', function (Blueprint $table) {
            $table->string('ma_ho_so', 30)->nullable()->after('id');
            $table->timestamp('cancelled_at')->nullable()->after('trang_thai');
            $table->string('ly_do_huy')->nullable()->after('cancelled_at');
        });

        // Gán mã hồ sơ cho các bản ghi hiện có
        DB::table('ho_so')
            ->select('id')
            ->orderBy('id')
            ->chunkById(200, function ($records) {
                foreach ($records as $record) {
                    DB::table('ho_so')
                        ->where('id', $record->id)
                        ->update([
                            'ma_ho_so' => sprintf('HS%06d', $record->id),
                        ]);
                }
            });

        Schema::table('ho_so', function (Blueprint $table) {
            $table->unique('ma_ho_so');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ho_so', function (Blueprint $table) {
            $table->dropUnique(['ma_ho_so']);
            $table->dropColumn(['ma_ho_so', 'cancelled_at', 'ly_do_huy']);
        });
    }
};
