<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'author_name',
        'author_email',
        'content',
        'publish',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'publish' => 'integer',
    ];

    /**
     * Define the relationship with the Post model.
     * Assuming a Post model exists.
     */
    public function post()
    {
        return $this->belongsTo(NewsArticle::class);
    }
}
