<?php

namespace App\Http\Controllers\website;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    public function index()
    {
        $posts = Post::published()
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('website.posts.index', compact('posts'));
    }

    /**
     * Display the specified post.
     */
    public function show($slug)
    {
        $post = Post::published()->where('duong_dan', $slug)->firstOrFail();
        
        // Tăng lượt xem
        $post->increment('luot_xem');

        // Lấy bài viết liên quan
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('website.posts.show', compact('post', 'relatedPosts'));
    }
}

