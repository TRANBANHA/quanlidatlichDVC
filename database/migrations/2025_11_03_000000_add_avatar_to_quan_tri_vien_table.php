<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvatarToQuanTriVienTable extends Migration
{
    public function up()
    {
        Schema::table('quan_tri_vien', function (Blueprint $table) {
            if (!Schema::hasColumn('quan_tri_vien', 'hinh_anh')) {
                $table->string('hinh_anh')->nullable()->after('email');
            }
            if (!Schema::hasColumn('quan_tri_vien', 'so_dien_thoai')) {
                $table->string('so_dien_thoai')->nullable()->after('hinh_anh');
            }
        });
    }

    public function down()
    {
        Schema::table('quan_tri_vien', function (Blueprint $table) {
            $table->dropColumn('hinh_anh');
            $table->dropColumn('so_dien_thoai');
        });
    }
}