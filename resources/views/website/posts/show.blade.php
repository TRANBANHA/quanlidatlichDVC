@extends('website.components.layout')
@section('title', $post->title)

@section('content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <article class="card shadow-sm border-0 rounded-3">
                    @if($post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" class="card-img-top" alt="{{ $post->title }}">
                    @endif
                    <div class="card-body p-4">
                        <h1 class="card-title mb-3">{{ $post->title }}</h1>
                        <div class="d-flex flex-wrap gap-3 mb-4 text-muted">
                            <span><i class="fas fa-user me-1"></i>{{ $post->author ?? 'Admin' }}</span>
                            <span><i class="fas fa-calendar me-1"></i>{{ $post->created_at->format('d/m/Y') }}</span>
                            <span><i class="fas fa-eye me-1"></i>{{ $post->views }} lượt xem</span>
                        </div>
                        <div class="post-content">
                            {!! $post->content !!}
                        </div>
                    </div>
                </article>

                @if($relatedPosts->count() > 0)
                <div class="mt-5">
                    <h3 class="mb-4">Bài viết liên quan</h3>
                    <div class="row g-4">
                        @foreach($relatedPosts as $relatedPost)
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0">
                                @if($relatedPost->image)
                                <img src="{{ asset('storage/' . $relatedPost->image) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $relatedPost->title }}">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">
                                        @if($relatedPost->slug)
                                            <a href="{{ route('posts.show', $relatedPost->slug) }}" class="text-decoration-none">
                                                {{ $relatedPost->title }}
                                            </a>
                                        @else
                                            <span>{{ $relatedPost->title }}</span>
                                        @endif
                                    </h5>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin bài viết</h5>
                        <hr>
                        <p class="mb-2"><strong>Tác giả:</strong> {{ $post->author ?? 'Admin' }}</p>
                        <p class="mb-2"><strong>Ngày đăng:</strong> {{ $post->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mb-2"><strong>Lượt xem:</strong> {{ $post->views }}</p>
                        @if($post->is_featured)
                        <span class="badge bg-primary">Bài viết nổi bật</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .post-content {
        line-height: 1.8;
    }
    .post-content h1, .post-content h2, .post-content h3 {
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    .post-content p {
        margin-bottom: 1rem;
    }
    .post-content img {
        max-width: 100%;
        height: auto;
    }
</style>
@endsection

