@extends('backend.components.layout')

@section('title')
    Cập Nhật Dịch Vụ
@endsection

@section('content')
    <div class="container">
        <div class="page-inner">
            <!-- Tiêu đề Trang -->
            <div class="page-header d-flex justify-content-between align-items-center">
                <h3 class="fw-bold text-primary"><i class="fas fa-concierge-bell me-2"></i> Cập Nhật Dịch Vụ</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="icon-home"></i> Trang Chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Quản Lý Dịch Vụ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cập Nhật</li>
                    </ol>
                </nav>
            </div>

            <!-- Form Section -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <!-- Tiêu đề Card -->
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-pencil-alt me-2"></i> Chỉnh Sửa Thông Tin Dịch Vụ</h5>
                        </div>

                        <!-- Nội dung Form -->
                        <div class="card-body p-4">
                            <!-- Thông Báo -->
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Form -->
                            <form action="{{ route('services.update', $service->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row gy-3">
                                    <!-- Tên Dịch Vụ -->
                                    <div class="col-md-12">
                                        <label for="ten_dich_vu" class="form-label">Tên Dịch Vụ</label>
                                        <input type="text" id="ten_dich_vu" name="ten_dich_vu"
                                            class="form-control"
                                            value="{{ old('ten_dich_vu', $service->ten_dich_vu) }}"
                                            placeholder="Nhập tên dịch vụ" required>
                                    </div>

                                    <!-- Mô Tả -->
                                    <div class="col-md-12">
                                        <label for="mo_ta" class="form-label">Mô Tả</label>
                                        <textarea id="mo_ta" name="mo_ta" class="form-control" rows="4" placeholder="Nhập mô tả chi tiết về dịch vụ">{{ old('mo_ta', $service->mo_ta) }}</textarea>
                                    </div>
                                </div>

                                <!-- Nút Lưu -->
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Lưu Cập Nhật
                                    </button>
                                    <a href="{{ route('services.index') }}" class="btn btn-secondary">
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
