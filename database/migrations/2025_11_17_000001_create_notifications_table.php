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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->date('publish_date');
            $table->date('expiry_date')->nullable();
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('type')->default(0)->comment('0: Thông báo chung, 1: Thông báo cá nhân');
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            
            // Indexes
            $table->index('publish_date');
            $table->index('expiry_date');
            $table->index('user_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
