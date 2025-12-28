<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename notifications table to thong_bao_admin
        if (Schema::hasTable('notifications')) {
            Schema::rename('notifications', 'thong_bao_admin');
            
            // Rename columns in thong_bao_admin using DB::statement
            if (Schema::hasColumn('thong_bao_admin', 'title')) {
                DB::statement('ALTER TABLE thong_bao_admin CHANGE title tieu_de VARCHAR(255)');
            }
            if (Schema::hasColumn('thong_bao_admin', 'content')) {
                DB::statement('ALTER TABLE thong_bao_admin CHANGE content noi_dung TEXT');
            }
            if (Schema::hasColumn('thong_bao_admin', 'publish_date')) {
                DB::statement('ALTER TABLE thong_bao_admin CHANGE publish_date ngay_dang DATE');
            }
            if (Schema::hasColumn('thong_bao_admin', 'expiry_date')) {
                DB::statement('ALTER TABLE thong_bao_admin CHANGE expiry_date ngay_het_han DATE NULL');
            }
            if (Schema::hasColumn('thong_bao_admin', 'image')) {
                DB::statement('ALTER TABLE thong_bao_admin CHANGE image hinh_anh VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('thong_bao_admin', 'user_id')) {
                DB::statement('ALTER TABLE thong_bao_admin CHANGE user_id nguoi_dung_id BIGINT UNSIGNED NULL');
            }
        }

        // Rename posts table to bai_viet
        if (Schema::hasTable('posts')) {
            Schema::rename('posts', 'bai_viet');
            
            // Rename columns in bai_viet using DB::statement
            if (Schema::hasColumn('bai_viet', 'title')) {
                DB::statement('ALTER TABLE bai_viet CHANGE title tieu_de VARCHAR(255)');
            }
            if (Schema::hasColumn('bai_viet', 'slug')) {
                DB::statement('ALTER TABLE bai_viet CHANGE slug duong_dan VARCHAR(255)');
            }
            if (Schema::hasColumn('bai_viet', 'excerpt')) {
                DB::statement('ALTER TABLE bai_viet CHANGE excerpt trich_dan TEXT NULL');
            }
            if (Schema::hasColumn('bai_viet', 'content')) {
                DB::statement('ALTER TABLE bai_viet CHANGE content noi_dung LONGTEXT');
            }
            if (Schema::hasColumn('bai_viet', 'image')) {
                DB::statement('ALTER TABLE bai_viet CHANGE image hinh_anh VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('bai_viet', 'author')) {
                DB::statement('ALTER TABLE bai_viet CHANGE author tac_gia VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('bai_viet', 'status')) {
                DB::statement("ALTER TABLE bai_viet CHANGE status trang_thai ENUM('draft', 'published') DEFAULT 'published'");
            }
            if (Schema::hasColumn('bai_viet', 'views')) {
                DB::statement('ALTER TABLE bai_viet CHANGE views luot_xem INT DEFAULT 0');
            }
            if (Schema::hasColumn('bai_viet', 'is_featured')) {
                DB::statement('ALTER TABLE bai_viet CHANGE is_featured noi_bat TINYINT(1) DEFAULT 0');
            }
        }

        // Rename about table to gioi_thieu
        if (Schema::hasTable('about')) {
            Schema::rename('about', 'gioi_thieu');
            
            // Rename columns in gioi_thieu using DB::statement
            if (Schema::hasColumn('gioi_thieu', 'title')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE title tieu_de VARCHAR(255)');
            }
            if (Schema::hasColumn('gioi_thieu', 'content')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE content noi_dung TEXT');
            }
            if (Schema::hasColumn('gioi_thieu', 'image')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE image hinh_anh VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('gioi_thieu', 'mission')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE mission su_menh TEXT NULL');
            }
            if (Schema::hasColumn('gioi_thieu', 'vision')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE vision tam_nhin TEXT NULL');
            }
            if (Schema::hasColumn('gioi_thieu', 'values')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE `values` gia_tri TEXT NULL');
            }
            if (Schema::hasColumn('gioi_thieu', 'phone')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE phone so_dien_thoai VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('gioi_thieu', 'address')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE address dia_chi VARCHAR(255) NULL');
            }
        }

        // Rename contacts table to lien_he
        if (Schema::hasTable('contacts')) {
            Schema::rename('contacts', 'lien_he');
            
            // Rename columns in lien_he using DB::statement
            if (Schema::hasColumn('lien_he', 'name')) {
                DB::statement('ALTER TABLE lien_he CHANGE name ten VARCHAR(255)');
            }
            if (Schema::hasColumn('lien_he', 'phone')) {
                DB::statement('ALTER TABLE lien_he CHANGE phone so_dien_thoai VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('lien_he', 'subject')) {
                DB::statement('ALTER TABLE lien_he CHANGE subject chu_de VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('lien_he', 'message')) {
                DB::statement('ALTER TABLE lien_he CHANGE message tin_nhan TEXT');
            }
            if (Schema::hasColumn('lien_he', 'status')) {
                DB::statement("ALTER TABLE lien_he CHANGE status trang_thai ENUM('new', 'read', 'replied') DEFAULT 'new'");
            }
            if (Schema::hasColumn('lien_he', 'reply')) {
                DB::statement('ALTER TABLE lien_he CHANGE reply phan_hoi TEXT NULL');
            }
            if (Schema::hasColumn('lien_he', 'replied_at')) {
                DB::statement('ALTER TABLE lien_he CHANGE replied_at ngay_phan_hoi TIMESTAMP NULL');
            }
        }

        // Rename settings table to cai_dat
        if (Schema::hasTable('settings')) {
            Schema::rename('settings', 'cai_dat');
            
            // Rename columns in cai_dat using DB::statement
            if (Schema::hasColumn('cai_dat', 'key')) {
                DB::statement('ALTER TABLE cai_dat CHANGE `key` khoa VARCHAR(255)');
            }
            if (Schema::hasColumn('cai_dat', 'value')) {
                DB::statement('ALTER TABLE cai_dat CHANGE `value` gia_tri TEXT NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse rename cai_dat to settings
        if (Schema::hasTable('cai_dat')) {
            if (Schema::hasColumn('cai_dat', 'khoa')) {
                DB::statement('ALTER TABLE cai_dat CHANGE khoa `key` VARCHAR(255)');
            }
            if (Schema::hasColumn('cai_dat', 'gia_tri')) {
                DB::statement('ALTER TABLE cai_dat CHANGE gia_tri `value` TEXT NULL');
            }
            Schema::rename('cai_dat', 'settings');
        }

        // Reverse rename lien_he to contacts
        if (Schema::hasTable('lien_he')) {
            if (Schema::hasColumn('lien_he', 'ten')) {
                DB::statement('ALTER TABLE lien_he CHANGE ten name VARCHAR(255)');
            }
            if (Schema::hasColumn('lien_he', 'so_dien_thoai')) {
                DB::statement('ALTER TABLE lien_he CHANGE so_dien_thoai phone VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('lien_he', 'chu_de')) {
                DB::statement('ALTER TABLE lien_he CHANGE chu_de subject VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('lien_he', 'tin_nhan')) {
                DB::statement('ALTER TABLE lien_he CHANGE tin_nhan message TEXT');
            }
            if (Schema::hasColumn('lien_he', 'trang_thai')) {
                DB::statement("ALTER TABLE lien_he CHANGE trang_thai status ENUM('new', 'read', 'replied') DEFAULT 'new'");
            }
            if (Schema::hasColumn('lien_he', 'phan_hoi')) {
                DB::statement('ALTER TABLE lien_he CHANGE phan_hoi reply TEXT NULL');
            }
            if (Schema::hasColumn('lien_he', 'ngay_phan_hoi')) {
                DB::statement('ALTER TABLE lien_he CHANGE ngay_phan_hoi replied_at TIMESTAMP NULL');
            }
            Schema::rename('lien_he', 'contacts');
        }

        // Reverse rename gioi_thieu to about
        if (Schema::hasTable('gioi_thieu')) {
            if (Schema::hasColumn('gioi_thieu', 'tieu_de')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE tieu_de title VARCHAR(255)');
            }
            if (Schema::hasColumn('gioi_thieu', 'noi_dung')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE noi_dung content TEXT');
            }
            if (Schema::hasColumn('gioi_thieu', 'hinh_anh')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE hinh_anh image VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('gioi_thieu', 'su_menh')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE su_menh mission TEXT NULL');
            }
            if (Schema::hasColumn('gioi_thieu', 'tam_nhin')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE tam_nhin vision TEXT NULL');
            }
            if (Schema::hasColumn('gioi_thieu', 'gia_tri')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE gia_tri `values` TEXT NULL');
            }
            if (Schema::hasColumn('gioi_thieu', 'so_dien_thoai')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE so_dien_thoai phone VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('gioi_thieu', 'dia_chi')) {
                DB::statement('ALTER TABLE gioi_thieu CHANGE dia_chi address VARCHAR(255) NULL');
            }
            Schema::rename('gioi_thieu', 'about');
        }

        // Reverse rename bai_viet to posts
        if (Schema::hasTable('bai_viet')) {
            if (Schema::hasColumn('bai_viet', 'tieu_de')) {
                DB::statement('ALTER TABLE bai_viet CHANGE tieu_de title VARCHAR(255)');
            }
            if (Schema::hasColumn('bai_viet', 'duong_dan')) {
                DB::statement('ALTER TABLE bai_viet CHANGE duong_dan slug VARCHAR(255)');
            }
            if (Schema::hasColumn('bai_viet', 'trich_dan')) {
                DB::statement('ALTER TABLE bai_viet CHANGE trich_dan excerpt TEXT NULL');
            }
            if (Schema::hasColumn('bai_viet', 'noi_dung')) {
                DB::statement('ALTER TABLE bai_viet CHANGE noi_dung content LONGTEXT');
            }
            if (Schema::hasColumn('bai_viet', 'hinh_anh')) {
                DB::statement('ALTER TABLE bai_viet CHANGE hinh_anh image VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('bai_viet', 'tac_gia')) {
                DB::statement('ALTER TABLE bai_viet CHANGE tac_gia author VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('bai_viet', 'trang_thai')) {
                DB::statement("ALTER TABLE bai_viet CHANGE trang_thai status ENUM('draft', 'published') DEFAULT 'published'");
            }
            if (Schema::hasColumn('bai_viet', 'luot_xem')) {
                DB::statement('ALTER TABLE bai_viet CHANGE luot_xem views INT DEFAULT 0');
            }
            if (Schema::hasColumn('bai_viet', 'noi_bat')) {
                DB::statement('ALTER TABLE bai_viet CHANGE noi_bat is_featured TINYINT(1) DEFAULT 0');
            }
            Schema::rename('bai_viet', 'posts');
        }

        // Reverse rename thong_bao_admin to notifications
        if (Schema::hasTable('thong_bao_admin')) {
            if (Schema::hasColumn('thong_bao_admin', 'tieu_de')) {
                DB::statement('ALTER TABLE thong_bao_admin CHANGE tieu_de title VARCHAR(255)');
            }
            if (Schema::hasColumn('thong_bao_admin', 'noi_dung')) {
                DB::statement('ALTER TABLE thong_bao_admin CHANGE noi_dung content TEXT');
            }
            if (Schema::hasColumn('thong_bao_admin', 'ngay_dang')) {
                DB::statement('ALTER TABLE thong_bao_admin CHANGE ngay_dang publish_date DATE');
            }
            if (Schema::hasColumn('thong_bao_admin', 'ngay_het_han')) {
                DB::statement('ALTER TABLE thong_bao_admin CHANGE ngay_het_han expiry_date DATE NULL');
            }
            if (Schema::hasColumn('thong_bao_admin', 'hinh_anh')) {
                DB::statement('ALTER TABLE thong_bao_admin CHANGE hinh_anh image VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('thong_bao_admin', 'nguoi_dung_id')) {
                DB::statement('ALTER TABLE thong_bao_admin CHANGE nguoi_dung_id user_id BIGINT UNSIGNED NULL');
            }
            Schema::rename('thong_bao_admin', 'notifications');
        }
    }
};

