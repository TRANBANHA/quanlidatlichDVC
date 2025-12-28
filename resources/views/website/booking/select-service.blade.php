@extends('website.components.layout')

@section('title', 'Chọn dịch vụ')

@section('content')
<div class="container-fluid py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5 animate-fade-in-up">
            <h1 class="display-4 fw-bold mb-3 text-gradient">Chọn dịch vụ</h1>
            <p class="text-muted fs-5 mb-2">
                <i class="fas fa-building text-primary me-2"></i>Phường: <strong>{{ $donVi->ten_don_vi }}</strong>
            </p>
            <p class="text-muted">Bước 2: Chọn loại dịch vụ bạn muốn đăng ký</p>
        </div>

        <!-- Progress Steps -->
        <div class="row justify-content-center mb-5 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="col-md-10">
                <div class="progress-step">
                    <div class="progress-step-item">
                        <div class="progress-step-circle completed bg-gradient-success text-white shadow-beautiful">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="mt-3 mb-0 fw-semibold">Chọn phường</p>
                    </div>
                    <div class="progress-step-line"></div>
                    <div class="progress-step-item">
                        <div class="progress-step-circle active bg-gradient-primary text-white shadow-beautiful">
                            <strong>2</strong>
                        </div>
                        <p class="mt-3 mb-0 fw-semibold">Chọn dịch vụ</p>
                    </div>
                    <div class="progress-step-line"></div>
                    <div class="progress-step-item">
                        <div class="progress-step-circle bg-secondary text-white">
                            <strong>3</strong>
                        </div>
                        <p class="mt-3 mb-0 text-muted">Chọn ngày</p>
                    </div>
                    <div class="progress-step-line"></div>
                    <div class="progress-step-item">
                        <div class="progress-step-circle bg-secondary text-white">
                            <strong>4</strong>
                        </div>
                        <p class="mt-3 mb-0 text-muted">Upload hồ sơ</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10">
                @if($services->isEmpty())
                    <div class="alert alert-warning text-center rounded-beautiful shadow-beautiful py-5 animate-fade-in-up">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3" style="opacity: 0.7;"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Chưa có dịch vụ nào</h4>
                        <p class="mb-4 fs-5">Phường này chưa có dịch vụ nào được kích hoạt. Vui lòng chọn phường khác.</p>
                        <a href="{{ route('booking.select-phuong') }}" class="btn btn-primary btn-lg shadow-beautiful">
                            <i class="fas fa-arrow-left me-2"></i>Chọn lại phường
                        </a>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($services as $index => $service)
                            @php
                                // Lấy cấu hình dịch vụ cho phường đã chọn
                                $servicePhuong = $service->servicePhuongs->where('don_vi_id', $donVi->id)->first();
                            @endphp
                            @if($servicePhuong)
                            <div class="col-md-6 animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                                <div class="card h-100 shadow-beautiful hover-lift">
                                    <div class="card-header">
                                        <h4 class="mb-0 text-white">
                                            <i class="fas fa-concierge-bell me-2"></i>{{ $service->ten_dich_vu }}
                                        </h4>
                                    </div>
                                    <div class="card-body p-4">
                                        <p class="card-text text-muted mb-4">{{ $service->mo_ta }}</p>
                                        
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-beautiful">
                                                <span class="text-muted">
                                                    <i class="fas fa-clock text-primary me-2"></i>Thời gian xử lý:
                                                </span>
                                                <strong class="text-primary">
                                                    <i class="fas fa-calendar-alt me-1"></i>{{ $servicePhuong->thoi_gian_xu_ly }} ngày làm việc
                                                </strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-beautiful">
                                                <span class="text-muted">
                                                    <i class="fas fa-users text-primary me-2"></i>Số lượng/ngày:
                                                </span>
                                                <strong class="text-info">
                                                    <i class="fas fa-user-check me-1"></i>{{ $servicePhuong->so_luong_toi_da }} người
                                                </strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-beautiful">
                                                <span class="text-muted">
                                                    <i class="fas fa-money-bill text-primary me-2"></i>Phí dịch vụ:
                                                </span>
                                                <strong class="text-danger fs-5">
                                                    @if($servicePhuong->phi_dich_vu > 0)
                                                        {{ number_format($servicePhuong->phi_dich_vu) }} VNĐ
                                                    @else
                                                        <span class="badge bg-success">Miễn phí</span>
                                                    @endif
                                                </strong>
                                            </div>
                                        </div>

                                        <form action="{{ route('booking.select-date') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="don_vi_id" value="{{ $donVi->id }}">
                                            <input type="hidden" name="dich_vu_id" value="{{ $service->id }}">
                                            <button type="submit" class="btn btn-primary w-100 shadow-beautiful">
                                                <i class="fas fa-check me-2"></i>Chọn dịch vụ này
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="text-center mt-5 animate-fade-in-up" style="animation-delay: 0.4s;">
                        <a href="{{ route('booking.select-phuong') }}" class="btn btn-secondary btn-lg shadow-beautiful">
                            <i class="fas fa-arrow-left me-2"></i>Chọn lại phường
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
