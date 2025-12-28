<?php

// Model NewsArticle
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'author', 'image', 'category_id', 'is_published', 'published_at', 'slug'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }
}
