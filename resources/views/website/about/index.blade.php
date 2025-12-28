@extends('website.components.layout')
@section('title', 'Giới thiệu')

@section('content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Giới thiệu</h1>
                <p class="lead text-muted">{{ $about->title ?? 'Về chúng tôi' }}</p>
            </div>
        </div>

        @if($about->image)
        <div class="row mb-5">
            <div class="col-12">
                <img src="{{ asset('storage/' . $about->image) }}" class="img-fluid rounded-3 shadow" alt="About">
            </div>
        </div>
        @endif

        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h3 class="mb-4">Tổng quan</h3>
                        <div class="content">
                            {!! $about->content ?? '<p>Nội dung đang được cập nhật...</p>' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($about->mission)
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-bullseye fa-3x text-primary mb-3"></i>
                        <h4 class="mb-3">Sứ mệnh</h4>
                        <div>{!! $about->mission !!}</div>
                    </div>
                </div>
            </div>
            @if($about->vision)
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-eye fa-3x text-primary mb-3"></i>
                        <h4 class="mb-3">Tầm nhìn</h4>
                        <div>{!! $about->vision !!}</div>
                    </div>
                </div>
            </div>
            @endif
            @if($about->values)
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-heart fa-3x text-primary mb-3"></i>
                        <h4 class="mb-3">Giá trị cốt lõi</h4>
                        <div>{!! $about->values !!}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        @if($about->phone || $about->email || $about->address)
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h3 class="mb-4">Thông tin liên hệ</h3>
                        <div class="row g-4">
                            @if($about->phone)
                            <div class="col-md-4 text-center">
                                <i class="fas fa-phone fa-2x text-primary mb-2"></i>
                                <p class="mb-0"><strong>Điện thoại</strong></p>
                                <p>{{ $about->phone }}</p>
                            </div>
                            @endif
                            @if($about->email)
                            <div class="col-md-4 text-center">
                                <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                                <p class="mb-0"><strong>Email</strong></p>
                                <p>{{ $about->email }}</p>
                            </div>
                            @endif
                            @if($about->address)
                            <div class="col-md-4 text-center">
                                <i class="fas fa-map-marker-alt fa-2x text-primary mb-2"></i>
                                <p class="mb-0"><strong>Địa chỉ</strong></p>
                                <p>{{ $about->address }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

