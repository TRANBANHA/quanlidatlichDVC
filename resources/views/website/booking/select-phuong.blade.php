@extends('website.components.layout')

@section('title', 'Chọn phường - Đặt lịch dịch vụ')

@section('content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5 animate-fade-in-up">
            <h1 class="display-4 fw-bold mb-3 text-gradient">Đặt lịch dịch vụ hành chính</h1>
            <p class="text-muted fs-5">Bước 1: Chọn phường bạn muốn đăng ký dịch vụ</p>
        </div>

        <!-- Progress Steps -->
        <div class="row justify-content-center mb-5 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="col-md-10">
                <div class="progress-step">
                    <div class="progress-step-item">
                        <div class="progress-step-circle active bg-gradient-primary text-white shadow-beautiful">
                            <strong>1</strong>
                        </div>
                        <p class="mt-3 mb-0 fw-semibold">Chọn phường</p>
                    </div>
                    <div class="progress-step-line"></div>
                    <div class="progress-step-item">
                        <div class="progress-step-circle bg-secondary text-white">
                            <strong>2</strong>
                        </div>
                        <p class="mt-3 mb-0 text-muted">Chọn dịch vụ</p>
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

        <div class="row justify-content-center animate-fade-in-up" style="animation-delay: 0.4s;">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-beautiful">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-building me-2"></i>Chọn phường/đơn vị</h4>
                    </div>
                    <div class="card-body p-5">
                        <form action="{{ route('booking.select-service') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label h5 mb-3">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>Phường/đơn vị:
                                </label>
                                <select name="don_vi_id" id="don_vi_select" class="form-select form-select-lg" required>
                                    <option value="">-- Chọn phường --</option>
                                    @foreach($donVis as $donVi)
                                        <option value="{{ $donVi->id }}" {{ isset($defaultDonViId) && $defaultDonViId == $donVi->id ? 'selected' : '' }}>
                                            {{ $donVi->ten_don_vi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('don_vi_id')
                                    <div class="text-danger mt-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Select2 CSS -->
                            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                            <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                            <script>
                                $(document).ready(function() {
                                    // Khởi tạo Select2 với tính năng search
                                    $('#don_vi_select').select2({
                                        theme: 'bootstrap-5',
                                        placeholder: '-- Chọn phường --',
                                        allowClear: true,
                                        language: {
                                            noResults: function() {
                                                return "Không tìm thấy phường nào";
                                            },
                                            searching: function() {
                                                return "Đang tìm kiếm...";
                                            }
                                        }
                                    });

                                    // Nếu có phường mặc định, trigger change để Select2 hiển thị đúng
                                    @if(isset($defaultDonViId) && $defaultDonViId)
                                        $('#don_vi_select').val('{{ $defaultDonViId }}').trigger('change');
                                    @endif

                                    // Lưu don_vi_id vào localStorage khi người dùng chọn
                                    $('#don_vi_select').on('change', function() {
                                        if (this.value) {
                                            localStorage.setItem('selected_don_vi_id', this.value);
                                        }
                                    });
                                });
                            </script>

                            <div class="d-flex justify-content-between gap-3 mt-5">
                                <a href="{{ route('index') }}" class="btn btn-secondary btn-lg flex-fill">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                    Tiếp tục<i class="fas fa-arrow-right ms-2"></i>
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
