@extends('website.components.layout')
@section('title', 'Trang chủ - Hệ thống đặt lịch dịch vụ hành chính')

@section('content')
    <!-- Hero Banner -->
    <div class="hero-banner bg-gradient-primary text-white py-5" style="min-height: 500px; display: flex; align-items: center;">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6 animate-fade-in-up">
                    <h1 class="display-3 fw-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">Đặt lịch dịch vụ hành chính trực tuyến</h1>
                    <p class="lead mb-4 fs-5" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Nhanh chóng, tiện lợi, không cần xếp hàng. Đặt lịch hẹn chỉ với vài cú click!</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('booking.select-phuong') }}" class="btn btn-light btn-lg px-5 shadow-beautiful">
                            <i class="fas fa-calendar-check me-2"></i>Đặt lịch ngay
                        </a>
                        <a href="{{ route('tracking.index') }}" class="btn btn-outline-light btn-lg px-5">
                            <i class="fas fa-search me-2"></i>Tra cứu hồ sơ
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center d-none d-lg-block animate-slide-in-right">
                    <div class="position-relative">
                        <i class="fas fa-calendar-alt" style="font-size: 250px; opacity: 0.3; animation: float 3s ease-in-out infinite;"></i>
                    </div>
                </div>
            </div>
        </div>
        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
        </style>
    </div>

    <!-- Statistics -->
    <div class="container py-5">
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-sm-6 animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="stat-card bg-gradient-primary text-white shadow-beautiful">
                    <i class="fas fa-concierge-bell"></i>
                    <h3 class="fw-bold mb-0">{{ $stats['total_services'] }}</h3>
                    <p class="mb-0 fs-5">Dịch vụ</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="stat-card bg-gradient-success text-white shadow-beautiful">
                    <i class="fas fa-calendar-check"></i>
                    <h3 class="fw-bold mb-0">{{ $stats['total_bookings'] }}</h3>
                    <p class="mb-0 fs-5">Lượt đặt lịch</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="stat-card bg-gradient-info text-white shadow-beautiful">
                    <i class="fas fa-users"></i>
                    <h3 class="fw-bold mb-0">{{ $stats['total_users'] }}</h3>
                    <p class="mb-0 fs-5">Người dùng</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="stat-card bg-gradient-warning text-white shadow-beautiful">
                    <i class="fas fa-check-circle"></i>
                    <h3 class="fw-bold mb-0">{{ $stats['completed_bookings'] }}</h3>
                    <p class="mb-0 fs-5">Hoàn tất</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Services -->
    <div class="container pb-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3 text-gradient" style="font-size: 2.5rem;">Dịch vụ phổ biến</h2>
            <p class="text-muted fs-5">Chọn dịch vụ bạn cần và đặt lịch ngay hôm nay</p>
        </div>
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="service-card hover-lift">
                    <div class="service-icon">
                        <i class="fas fa-baby fa-3x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Đăng ký khai sinh</h5>
                    <p class="text-muted mb-4">Đăng ký khai sinh cho trẻ em mới sinh</p>
                    <a href="{{ route('booking.select-phuong') }}" class="btn btn-primary btn-sm">Đặt lịch</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="service-card hover-lift">
                    <div class="service-icon">
                        <i class="fas fa-home fa-3x text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Đăng ký thường trú</h5>
                    <p class="text-muted mb-4">Đăng ký hộ khẩu thường trú</p>
                    <a href="{{ route('booking.select-phuong') }}" class="btn btn-success btn-sm">Đặt lịch</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="service-card hover-lift">
                    <div class="service-icon">
                        <i class="fas fa-heart fa-3x" style="color: #f5576c;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Giấy độc thân</h5>
                    <p class="text-muted mb-4">Cấp giấy xác nhận tình trạng hôn nhân</p>
                    <a href="{{ route('booking.select-phuong') }}" class="btn btn-sm" style="background: var(--secondary-gradient); color: white; border: none;">Đặt lịch</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="service-card hover-lift">
                    <div class="service-icon">
                        <i class="fas fa-file-alt fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Dịch vụ khác</h5>
                    <p class="text-muted mb-4">Các dịch vụ hành chính khác</p>
                    <a href="{{ route('booking.select-phuong') }}" class="btn btn-warning btn-sm">Xem thêm</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Posts -->
    @if($featuredPosts->count() > 0)
    <div class="bg-light py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">
                    <i class="fas fa-star text-warning me-2"></i>Tin tức nổi bật
                </h2>
                <a href="{{ route('posts.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            
            <div class="row g-4">
                @foreach($featuredPosts->take(3) as $post)
                <div class="col-lg-4 col-md-6">
                    <article class="post-card card h-100 border-0 shadow-sm hover-lift">
                        @if($post->image)
                        <div class="post-image position-relative overflow-hidden">
                            <img src="{{ asset('storage/' . $post->image) }}" class="card-img-top" style="height: 220px; object-fit: cover;" alt="{{ $post->title }}">
                            <span class="badge bg-warning position-absolute top-0 end-0 m-3">
                                <i class="fas fa-star me-1"></i>Nổi bật
                            </span>
                        </div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold mb-3">
                                @if($post->slug)
                                    <a href="{{ route('posts.show', $post->slug) }}" class="text-dark text-decoration-none stretched-link">
                                        {{ Str::limit($post->title, 60) }}
                                    </a>
                                @else
                                    <span class="text-dark">{{ Str::limit($post->title, 60) }}</span>
                                @endif
                            </h5>
                            <p class="card-text text-muted small flex-grow-1">{{ Str::limit($post->short_excerpt, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>{{ $post->created_at->format('d/m/Y') }}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-eye me-1"></i>{{ $post->views ?? 0 }}
                                </small>
                            </div>
                        </div>
                    </article>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Latest Posts -->
    @if($latestPosts->count() > 0)
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">
                <i class="fas fa-newspaper text-primary me-2"></i>Tin tức mới nhất
            </h2>
        </div>
        
        <div class="row g-4">
            @foreach($latestPosts->take(6) as $post)
            <div class="col-lg-4 col-md-6">
                <article class="post-card-simple card h-100 border shadow-sm hover-lift">
                    <div class="row g-0">
                        @if($post->image)
                        <div class="col-4">
                            <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid h-100" style="object-fit: cover;" alt="{{ $post->title }}">
                        </div>
                        <div class="col-8">
                        @else
                        <div class="col-12">
                        @endif
                            <div class="card-body p-3">
                                <h6 class="card-title fw-bold mb-2">
                                    @if($post->slug)
                                        <a href="{{ route('posts.show', $post->slug) }}" class="text-dark text-decoration-none stretched-link">
                                            {{ Str::limit($post->title, 70) }}
                                        </a>
                                    @else
                                        <span class="text-dark">{{ Str::limit($post->title, 70) }}</span>
                                    @endif
                                </h6>
                                <p class="card-text text-muted small mb-2">{{ Str::limit($post->short_excerpt, 80) }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $post->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('posts.index') }}" class="btn btn-primary btn-lg px-5">
                Xem tất cả tin tức <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
    @endif

    <!-- Call to Action -->
    <div class="cta-section bg-gradient-primary text-white py-5">
        <div class="container py-5 text-center">
            <h2 class="display-4 fw-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">Bắt đầu đặt lịch ngay hôm nay!</h2>
            <p class="lead mb-5 fs-4" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Tiết kiệm thời gian, tránh xếp hàng chờ đợi. Đặt lịch trực tuyến chỉ trong vài phút.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('booking.select-phuong') }}" class="btn btn-light btn-lg px-5 shadow-beautiful">
                    <i class="fas fa-calendar-plus me-2"></i>Đặt lịch ngay
                </a>
                <a href="{{ route('tracking.index') }}" class="btn btn-outline-light btn-lg px-5">
                    <i class="fas fa-search me-2"></i>Tra cứu hồ sơ
                </a>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
.stretched-link::after {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1;
    content: "";
}
</style>
@endpush
