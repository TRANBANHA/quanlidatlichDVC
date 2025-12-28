@extends('website.components.layout')

@section('title', 'Tra cứu hồ sơ')

@section('content')
<div class="container-fluid py-5 bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-search fa-3x text-primary mb-3"></i>
                            <h2 class="mb-3">Tra cứu hồ sơ</h2>
                            <p class="text-muted">Nhập mã hồ sơ hoặc số CCCD để tra cứu trạng thái hồ sơ của bạn</p>
                        </div>

                        <form action="{{ route('tracking.search') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <div class="btn-group w-100 mb-3" role="group">
                                    <input type="radio" class="btn-check" name="type" id="type_ma_ho_so" value="ma_ho_so" checked>
                                    <label class="btn btn-outline-primary" for="type_ma_ho_so">
                                        <i class="fas fa-barcode me-2"></i>Mã hồ sơ
                                    </label>

                                    <input type="radio" class="btn-check" name="type" id="type_cccd" value="cccd">
                                    <label class="btn btn-outline-primary" for="type_cccd">
                                        <i class="fas fa-id-card me-2"></i>Số CCCD
                                    </label>
                                </div>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                           name="keyword" 
                                           class="form-control" 
                                           placeholder="Nhập mã hồ sơ hoặc số CCCD..." 
                                           required
                                           autofocus>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>Tra cứu
                                    </button>
                                </div>
                                <small class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Mã hồ sơ có dạng: HS20241113XXXX hoặc nhập số CCCD 12 chữ số
                                </small>
                            </div>
                        </form>

                        @if(session('error'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="mt-4 text-center">
                            <p class="text-muted mb-0">
                                <i class="fas fa-shield-alt me-2"></i>
                                Thông tin của bạn được bảo mật tuyệt đối
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

