<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->filled('search')) {
            $query->where('tieu_de', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status);
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('backend.Post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        return view('backend.Post.create');
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:bai_viet,duong_dan',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'author' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'is_featured' => 'nullable|boolean',
        ]);

        $data = [
            'tieu_de' => $validated['title'],
            'duong_dan' => $validated['slug'] ?? Str::slug($validated['title']),
            'trich_dan' => $validated['excerpt'] ?? null,
            'noi_dung' => $validated['content'],
            'hinh_anh' => null,
            'tac_gia' => $validated['author'] ?? null,
            'trang_thai' => $validated['status'],
            'noi_bat' => $validated['is_featured'] ?? false,
        ];

        if ($request->hasFile('image')) {
            $data['hinh_anh'] = $request->file('image')->store('posts', 'public');
        }

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', 'Bài viết đã được tạo thành công!');
    }

    /**
     * Display the specified post.
     * Redirect to index since admin doesn't need detail view.
     */
    public function show($id)
    {
        return redirect()->route('admin.posts.index');
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('backend.Post.edit', compact('post'));
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:bai_viet,duong_dan,' . $id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'author' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'is_featured' => 'nullable|boolean',
        ]);

        $data = [
            'tieu_de' => $validated['title'],
            'duong_dan' => $validated['slug'] ?? Str::slug($validated['title']),
            'trich_dan' => $validated['excerpt'] ?? null,
            'noi_dung' => $validated['content'],
            'tac_gia' => $validated['author'] ?? null,
            'trang_thai' => $validated['status'],
            'noi_bat' => $validated['is_featured'] ?? false,
        ];

        if ($request->hasFile('image')) {
            if ($post->hinh_anh) {
                \Storage::disk('public')->delete($post->hinh_anh);
            }
            $data['hinh_anh'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'Bài viết đã được cập nhật thành công!');
    }

    /**
     * Remove the specified post.
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        
        if ($post->hinh_anh) {
            \Storage::disk('public')->delete($post->hinh_anh);
        }
        
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Bài viết đã được xóa thành công!');
    }
}

