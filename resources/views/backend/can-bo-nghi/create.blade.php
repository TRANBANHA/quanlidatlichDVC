@extends('backend.components.layout')
@section('title', 'Báo nghỉ')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="fw-bold mb-2 text-primary">Báo nghỉ</h2>
                    <p class="text-muted mb-0">Đăng ký ngày nghỉ của bạn</p>
                </div>
            </div>
            <ul class="breadcrumbs mt-3">
                <li class="nav-home">
                    <a href="/admin"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li><a href="{{ route('admin.can-bo-nghi.index') }}">Cán bộ báo nghỉ</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li>Báo nghỉ</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.can-bo-nghi.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="ngay_nghi" class="form-label">
                                    Ngày nghỉ <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('ngay_nghi') is-invalid @enderror" 
                                       id="ngay_nghi" 
                                       name="ngay_nghi" 
                                       value="{{ old('ngay_nghi') }}"
                                       min="{{ date('Y-m-d') }}"
                                       required>
                                @error('ngay_nghi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Chọn ngày bạn sẽ nghỉ. Hệ thống sẽ tự động chuyển hồ sơ của bạn sang cán bộ khác.
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="ly_do" class="form-label">Lý do nghỉ</label>
                                <textarea class="form-control @error('ly_do') is-invalid @enderror" 
                                          id="ly_do" 
                                          name="ly_do" 
                                          rows="3"
                                          maxlength="500">{{ old('ly_do') }}</textarea>
                                @error('ly_do')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Mô tả lý do nghỉ (tối đa 500 ký tự)
                                </small>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Lưu ý:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Khi bạn báo nghỉ, tất cả hồ sơ được phân công cho bạn trong ngày đó sẽ tự động chuyển sang cán bộ khác.</li>
                                    <li>Người dùng sẽ nhận được thông báo về việc thay đổi cán bộ xử lý hồ sơ.</li>
                                    <li>Bạn chỉ có thể báo nghỉ từ hôm nay trở đi.</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.can-bo-nghi.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Báo nghỉ
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
