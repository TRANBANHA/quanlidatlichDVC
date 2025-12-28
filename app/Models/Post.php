<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $table = 'bai_viet';

    protected $fillable = [
        'tieu_de',
        'duong_dan',
        'trich_dan',
        'noi_dung',
        'hinh_anh',
        'tac_gia',
        'trang_thai',
        'luot_xem',
        'noi_bat',
    ];

    protected $casts = [
        'noi_bat' => 'boolean',
        'luot_xem' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->duong_dan)) {
                $post->duong_dan = Str::slug($post->tieu_de ?? 'post-' . time());
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('tieu_de') && empty($post->duong_dan)) {
                $post->duong_dan = Str::slug($post->tieu_de ?? 'post-' . time());
            }
        });

        // Đảm bảo duong_dan luôn có giá trị khi retrieve
        static::retrieved(function ($post) {
            if (empty($post->duong_dan) && !empty($post->tieu_de)) {
                $post->duong_dan = Str::slug($post->tieu_de);
                $post->saveQuietly(); // Lưu không trigger events
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'duong_dan';
    }

    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('trang_thai', 'published');
    }

    /**
     * Scope a query to only include featured posts.
     */
    public function scopeFeatured($query)
    {
        return $query->where('noi_bat', true);
    }

    /**
     * Get excerpt
     */
    public function getShortExcerptAttribute()
    {
        return Str::limit($this->trich_dan ?? Str::limit(strip_tags($this->noi_dung), 150), 150);
    }

    // Accessors để tương thích với code cũ
    public function getSlugAttribute()
    {
        return $this->duong_dan ?? null;
    }

    public function getTitleAttribute()
    {
        return $this->tieu_de ?? null;
    }

    public function getContentAttribute()
    {
        return $this->noi_dung ?? null;
    }

    public function getExcerptAttribute()
    {
        return $this->trich_dan ?? null;
    }

    public function getImageAttribute()
    {
        return $this->hinh_anh ?? null;
    }

    public function getAuthorAttribute()
    {
        return $this->tac_gia ?? null;
    }

    public function getStatusAttribute()
    {
        return $this->trang_thai ?? null;
    }

    public function getViewsAttribute()
    {
        return $this->luot_xem ?? 0;
    }

    public function getIsFeaturedAttribute()
    {
        return (bool)($this->noi_bat ?? false);
    }

    // Mutators để tương thích với code cũ
    public function setSlugAttribute($value)
    {
        $this->attributes['duong_dan'] = $value;
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['tieu_de'] = $value;
    }

    public function setContentAttribute($value)
    {
        $this->attributes['noi_dung'] = $value;
    }

    public function setExcerptAttribute($value)
    {
        $this->attributes['trich_dan'] = $value;
    }

    public function setImageAttribute($value)
    {
        $this->attributes['hinh_anh'] = $value;
    }

    public function setAuthorAttribute($value)
    {
        $this->attributes['tac_gia'] = $value;
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['trang_thai'] = $value;
    }

    public function setViewsAttribute($value)
    {
        $this->attributes['luot_xem'] = $value;
    }

    public function setIsFeaturedAttribute($value)
    {
        $this->attributes['noi_bat'] = (bool)$value;
    }
}

