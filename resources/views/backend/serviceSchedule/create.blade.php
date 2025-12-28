@extends('backend.components.layout')

@section('title')
    Thêm Dịch Vụ
@endsection

@section('content')
    <div class="container">
        <div class="page-inner">
            <!-- Page Header -->
            <div class="page-header d-flex justify-content-between align-items-center">
                <h3 class="fw-bold mb-0 text-primary"><i class="fas fa-concierge-bell me-2"></i> Thêm Dịch Vụ</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="icon-home"></i> Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Quản lý Dịch Vụ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Thêm Dịch Vụ</li>
                    </ol>
                </nav>
            </div>

            <!-- Form Section -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-8">
                    <div class="card shadow border-0">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Thêm Thông Tin Dịch Vụ</h5>
                        </div>
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
                            <form action="{{ route('services.store') }}" method="POST">
                                @csrf
                                <div class="row gy-3">
                                    <!-- Tên dịch vụ -->
                                    <div class="col-md-12">
                                        <label for="ten_dich_vu" class="form-label">Tên Dịch Vụ</label>
                                        <input type="text" id="ten_dich_vu" name="ten_dich_vu" class="form-control" placeholder="Nhập tên dịch vụ" required>
                                    </div>

                                    <!-- Mô tả -->
                                    <div class="col-md-12">
                                        <label for="mo_ta" class="form-label">Mô Tả</label>
                                        <textarea id="mo_ta" name="mo_ta" class="form-control" rows="4" placeholder="Nhập mô tả chi tiết về dịch vụ"></textarea>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check-circle me-1"></i> Thêm Dịch Vụ
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
