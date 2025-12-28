@extends('website.components.layout')

@section('title', 'Upload hồ sơ')

@section('content')
<div class="container-fluid py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5 animate-fade-in-up">
            <h1 class="display-4 fw-bold mb-3 text-gradient">Upload hồ sơ</h1>
            <p class="fs-5 mb-2">
                <i class="fas fa-building text-primary me-2"></i>Phường: <strong>{{ $donVi->ten_don_vi }}</strong> | 
                <i class="fas fa-concierge-bell text-success me-2"></i>Dịch vụ: <strong>{{ $dichVu->ten_dich_vu }}</strong>
            </p>
            <p class="mb-2">
                <i class="fas fa-calendar text-info me-2"></i>Ngày hẹn: <strong>{{ \Carbon\Carbon::parse($ngayHen)->format('d/m/Y') }}</strong> 
                lúc <strong>{{ $gioHen }}</strong>
            </p>
            <p class="text-dark">Bước 4: Điền thông tin và upload giấy tờ cần thiết</p>
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
                        <div class="progress-step-circle completed bg-gradient-success text-white shadow-beautiful">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="mt-3 mb-0 fw-semibold">Chọn dịch vụ</p>
                    </div>
                    <div class="progress-step-line"></div>
                    <div class="progress-step-item">
                        <div class="progress-step-circle completed bg-gradient-success text-white shadow-beautiful">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="mt-3 mb-0 fw-semibold">Chọn ngày</p>
                    </div>
                    <div class="progress-step-line"></div>
                    <div class="progress-step-item">
                        <div class="progress-step-circle active bg-gradient-primary text-white shadow-beautiful">
                            <strong>4</strong>
                        </div>
                        <p class="mt-3 mb-0 fw-semibold">Upload hồ sơ</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center animate-fade-in-up" style="animation-delay: 0.4s;">
            <div class="col-md-10 col-lg-9">
                <div class="card shadow-beautiful">
                    <div class="card-header">
                        <h4 class="mb-0 text-white">
                            <i class="fas fa-file-upload me-2"></i>Thông tin và hồ sơ
                        </h4>
                    </div>
                    <div class="card-body p-5">
                        <form action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                            @csrf
                            <input type="hidden" name="don_vi_id" value="{{ $donVi->id }}">
                            <input type="hidden" name="dich_vu_id" value="{{ $dichVu->id }}">
                            <input type="hidden" name="ngay_hen" value="{{ $ngayHen }}">
                            <input type="hidden" name="gio_hen" value="{{ $gioHen }}">

                            <!-- Thông tin dịch vụ -->
                            <div class="alert alert-info mb-4 rounded-beautiful shadow-sm">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Thông tin dịch vụ
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <p class="mb-2">
                                            <strong class="text-primary">Dịch vụ:</strong><br>
                                            <span class="text-dark">{{ $dichVu->ten_dich_vu }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-2">
                                            <strong class="text-primary">Mô tả:</strong><br>
                                            <span class="text-dark">{{ $dichVu->mo_ta }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-0">
                                            <strong class="text-primary">Phí dịch vụ:</strong><br>
                                            <span class="text-danger fw-bold fs-5">{{ number_format($servicePhuong->phi_dich_vu) }} VNĐ</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form động theo dịch vụ -->
                            @if($dichVu->serviceFields && $dichVu->serviceFields->count() > 0)
                                <div class="mb-4">
                                    <h5 class="fw-bold mb-4 text-primary">
                                        <i class="fas fa-file-alt me-2"></i>Thông tin và giấy tờ cần thiết
                                    </h5>
                                </div>
                                
                                @foreach($dichVu->serviceFields as $index => $field)
                                    <div class="mb-4 animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                                        <label class="form-label fw-semibold mb-2">
                                            <i class="fas fa-circle text-primary me-2" style="font-size: 0.5rem;"></i>
                                            {{ $field->nhan_hien_thi }}
                                            @if($field->bat_buoc)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        
                                        @if($field->loai_truong === 'text' || $field->loai_truong === 'email' || $field->loai_truong === 'number')
                                            <input type="{{ $field->loai_truong }}" 
                                                   name="{{ $field->ten_truong }}" 
                                                   class="form-control @error($field->ten_truong) is-invalid @enderror"
                                                   placeholder="{{ $field->placeholder }}"
                                                   value="{{ old($field->ten_truong) }}"
                                                   @if($field->bat_buoc) required @endif>
                                            @if($field->goi_y)
                                                <small class="form-text text-info">{{ $field->goi_y }}</small>
                                            @endif
                                            @error($field->ten_truong)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        
                                        @elseif($field->loai_truong === 'date')
                                            <input type="date" 
                                                   name="{{ $field->ten_truong }}" 
                                                   class="form-control @error($field->ten_truong) is-invalid @enderror"
                                                   placeholder="{{ $field->placeholder }}"
                                                   value="{{ old($field->ten_truong) }}"
                                                   @if($field->bat_buoc) required @endif>
                                            @if($field->goi_y)
                                                <small class="form-text text-info">{{ $field->goi_y }}</small>
                                            @endif
                                            @error($field->ten_truong)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        
                                        @elseif($field->loai_truong === 'textarea')
                                            <textarea name="{{ $field->ten_truong }}" 
                                                      class="form-control @error($field->ten_truong) is-invalid @enderror"
                                                      rows="3"
                                                      placeholder="{{ $field->placeholder }}"
                                                      @if($field->bat_buoc) required @endif>{{ old($field->ten_truong) }}</textarea>
                                            @if($field->goi_y)
                                                <small class="form-text text-info">{{ $field->goi_y }}</small>
                                            @endif
                                            @error($field->ten_truong)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        
                                        @elseif($field->loai_truong === 'select')
                                            <select name="{{ $field->ten_truong }}" 
                                                    class="form-select @error($field->ten_truong) is-invalid @enderror"
                                                    @if($field->bat_buoc) required @endif>
                                                <option value="">-- Chọn --</option>
                                                @if($field->tuy_chon && is_array($field->tuy_chon))
                                                    @foreach($field->tuy_chon as $option)
                                                        <option value="{{ $option }}" {{ old($field->ten_truong) == $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error($field->ten_truong)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        
                                        @elseif($field->loai_truong === 'file')
                                            <input type="file" 
                                                   name="{{ $field->ten_truong }}" 
                                                   class="form-control @error($field->ten_truong) is-invalid @enderror"
                                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                   @if($field->bat_buoc) required @endif>
                                            @if($field->goi_y)
                                                <small class="form-text text-info">{{ $field->goi_y }}</small>
                                            @endif
                                            <small class="form-text text-info">Định dạng: PDF, DOC, DOCX, JPG, PNG (Tối đa 5MB)</small>
                                            @error($field->ten_truong)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-warning rounded-beautiful shadow-sm">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Dịch vụ này chưa có form hồ sơ. Vui lòng liên hệ phường để được hướng dẫn.
                                </div>
                            @endif

                            <!-- Ghi chú -->
                            <div class="mb-4 animate-fade-in-up" style="animation-delay: 0.6s;">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-sticky-note text-primary me-2"></i>Ghi chú (nếu có)
                                </label>
                                <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Nhập ghi chú nếu có yêu cầu đặc biệt...">{{ old('ghi_chu') }}</textarea>
                            </div>

                            <!-- Lỗi tổng -->
                            @if($errors->any())
                                <div class="alert alert-danger rounded-beautiful shadow-sm mb-4">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Vui lòng kiểm tra lại:
                                    </h6>
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between gap-3 mt-5 pt-4 border-top">
                                <form action="{{ route('booking.select-date') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="don_vi_id" value="{{ $donVi->id }}">
                                    <input type="hidden" name="dich_vu_id" value="{{ $dichVu->id }}">
                                    <input type="hidden" name="ngay_hen" value="{{ $ngayHen }}">
                                    <input type="hidden" name="gio_hen" value="{{ $gioHen }}">
                                    <button type="submit" class="btn btn-secondary btn-lg flex-fill shadow-beautiful">
                                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                                    </button>
                                </form>
                                <button type="submit" class="btn btn-primary btn-lg flex-fill shadow-beautiful" id="submitBtn">
                                    <i class="fas fa-check me-2"></i>Xác nhận đặt lịch
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
});
</script>

<style>
/* Đảm bảo text hiển thị rõ ràng */
.text-dark {
    color: #212529 !important;
    opacity: 1 !important;
}

.form-label {
    color: #495057 !important;
    font-weight: 600 !important;
}

.form-control, .form-select {
    color: #495057 !important;
}

.text-info {
    color: #0dcaf0 !important;
}

/* Override muted text */
.text-muted {
    color: #6c757d !important;
    opacity: 0.8 !important;
}

/* Đảm bảo các element quan trọng không bị mờ */
h1, h2, h3, h4, h5, h6, p, span, label, input, select, textarea {
    opacity: 1 !important;
}

/* Card content */
.card-body {
    color: #212529 !important;
}

.alert {
    opacity: 1 !important;
}

.alert span, .alert p {
    opacity: 1 !important;
}
</style>
@endsection

