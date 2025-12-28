@extends('website.components.layout')
@section('title', 'Danh sách bài viết')

@section('content')
<div class="container-fluid py-5 bg-light">
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-primary"><i class="fas fa-newspaper me-2"></i>Danh sách bài viết</h1>
                <p class="text-muted">Các bài viết mới nhất về dịch vụ hành chính</p>
            </div>
        </div>

        <div class="row g-4">
            @forelse($posts as $post)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden post-card">
                    @if($post->image)
                    <div class="position-relative overflow-hidden" style="height: 250px;">
                        <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="{{ $post->title }}">
                        @if($post->is_featured)
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-primary">Nổi bật</span>
                        </div>
                        @endif
                    </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">
                            @if($post->slug)
                                <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none text-dark">
                                    {{ $post->title }}
                                </a>
                            @else
                                <span class="text-dark">{{ $post->title }}</span>
                            @endif
                        </h5>
                        <p class="card-text text-muted">{{ $post->short_excerpt }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>{{ $post->author ?? 'Admin' }}
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-eye me-1"></i>{{ $post->views }} lượt xem
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        @if($post->slug)
                            <a href="{{ route('posts.show', $post->slug) }}" class="btn btn-primary btn-sm w-100">
                                Đọc thêm <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        @else
                            <button class="btn btn-secondary btn-sm w-100" disabled>
                                Chưa có đường dẫn
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chưa có bài viết nào</p>
                </div>
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>
</div>

<style>
    .post-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection

