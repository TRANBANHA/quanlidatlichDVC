@extends('backend.components.layout')

@section('title')
    Tạo Dịch Vụ Mới
@endsection

@section('content')
    <div class="container">
        <div class="page-inner">
            <!-- Tiêu đề Trang -->
            <div class="page-header d-flex justify-content-between align-items-center">
                <h3 class="fw-bold text-primary"><i class="fas fa-plus-circle me-2"></i> Tạo Dịch Vụ Mới</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="icon-home"></i> Trang Chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('service-phuong.index') }}">Quản Lý Dịch Vụ Phường</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tạo Mới</li>
                    </ol>
                </nav>
            </div>

            <!-- Form Section -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Thông Tin Dịch Vụ Mới</h5>
                        </div>
                        <div class="card-body p-4">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('service-phuong.store') }}" method="POST">
                                @csrf

                                <div class="row gy-3">
                                    <!-- Tên dịch vụ -->
                                    <div class="col-md-12">
                                        <label for="ten_dich_vu" class="form-label">Tên Dịch Vụ <span class="text-danger">*</span></label>
                                        <input type="text" id="ten_dich_vu" name="ten_dich_vu" 
                                               class="form-control @error('ten_dich_vu') is-invalid @enderror"
                                               value="{{ old('ten_dich_vu') }}" 
                                               placeholder="vd: Đăng ký khai sinh, Cấp lại CMND" 
                                               required>
                                        @error('ten_dich_vu')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Mô tả -->
                                    <div class="col-md-12">
                                        <label for="mo_ta" class="form-label">Mô Tả</label>
                                        <textarea id="mo_ta" name="mo_ta" 
                                                  class="form-control @error('mo_ta') is-invalid @enderror" 
                                                  rows="4" 
                                                  placeholder="Mô tả chi tiết về dịch vụ">{{ old('mo_ta') }}</textarea>
                                        @error('mo_ta')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Lưu ý:</strong> Sau khi tạo dịch vụ, bạn sẽ được chuyển đến trang cấu hình để thêm các trường form đăng ký.
                                </div>

                                <!-- Nút Lưu -->
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Tạo Dịch Vụ
                                    </button>
                                    <a href="{{ route('service-phuong.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Quay Lại
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

