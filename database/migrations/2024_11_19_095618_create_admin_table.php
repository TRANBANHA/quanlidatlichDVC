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
        Schema::create('quan_tri_vien', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten')->nullable();
            $table->string('ten_dang_nhap');
            $table->string('mat_khau');
            $table->integer('quyen')->default(1);
            $table->string('email')->nullable()->check(function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quan_tri_vien');
    }
};
