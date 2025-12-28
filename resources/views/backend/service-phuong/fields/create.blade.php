@extends('backend.components.layout')

@section('title')
    Thêm Trường Form
@endsection

@section('content')
    <div class="container">
        <div class="page-inner">
            <!-- Tiêu đề Trang -->
            <div class="page-header d-flex justify-content-between align-items-center">
                <h3 class="fw-bold text-primary"><i class="fas fa-file-alt me-2"></i> Thêm Trường Form</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="icon-home"></i> Trang Chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('service-phuong.index') }}">Quản Lý Dịch Vụ Phường</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('service-phuong.edit', $service->id) }}">{{ $service->ten_dich_vu }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Thêm Trường</li>
                    </ol>
                </nav>
            </div>

            <!-- Form Section -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-10">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Thêm Trường Form Mới</h5>
                            <small>Dịch vụ: <strong>{{ $service->ten_dich_vu }}</strong></small>
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

                            <form action="{{ route('service-phuong.fields.store', $service->id) }}" method="POST">
                                @csrf

                                <div class="row gy-3">
                                    <!-- Tên Trường -->
                                    <div class="col-md-6">
                                        <label for="ten_truong" class="form-label">Tên Trường (Field Name) <span class="text-danger">*</span></label>
                                        <input type="text" id="ten_truong" name="ten_truong" 
                                               class="form-control @error('ten_truong') is-invalid @enderror"
                                               value="{{ old('ten_truong') }}" 
                                               placeholder="vd: ho_ten, ngay_sinh, cmnd" 
                                               pattern="[a-z0-9_]+" 
                                               required>
                                        <small class="form-text text-muted">
                                            Chỉ dùng chữ thường, số và dấu gạch dưới. Ví dụ: ho_ten, ngay_sinh, cmnd
                                        </small>
                                        @error('ten_truong')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Nhãn Hiển Thị -->
                                    <div class="col-md-6">
                                        <label for="nhan_hien_thi" class="form-label">Nhãn Hiển Thị <span class="text-danger">*</span></label>
                                        <input type="text" id="nhan_hien_thi" name="nhan_hien_thi" 
                                               class="form-control @error('nhan_hien_thi') is-invalid @enderror"
                                               value="{{ old('nhan_hien_thi') }}" 
                                               placeholder="vd: Họ và tên, Ngày sinh" 
                                               required>
                                        <small class="form-text text-muted">Tên hiển thị cho người dùng</small>
                                        @error('nhan_hien_thi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Loại Trường -->
                                    <div class="col-md-6">
                                        <label for="loai_truong" class="form-label">Loại Trường <span class="text-danger">*</span></label>
                                        <select id="loai_truong" name="loai_truong" 
                                                class="form-select @error('loai_truong') is-invalid @enderror" 
                                                required>
                                            <option value="">-- Chọn loại trường --</option>
                                            <option value="text" {{ old('loai_truong') == 'text' ? 'selected' : '' }}>Text (Văn bản)</option>
                                            <option value="email" {{ old('loai_truong') == 'email' ? 'selected' : '' }}>Email</option>
                                            <option value="number" {{ old('loai_truong') == 'number' ? 'selected' : '' }}>Number (Số)</option>
                                            <option value="date" {{ old('loai_truong') == 'date' ? 'selected' : '' }}>Date (Ngày tháng)</option>
                                            <option value="textarea" {{ old('loai_truong') == 'textarea' ? 'selected' : '' }}>Textarea (Văn bản dài)</option>
                                            <option value="select" {{ old('loai_truong') == 'select' ? 'selected' : '' }}>Select (Dropdown)</option>
                                            <option value="file" {{ old('loai_truong') == 'file' ? 'selected' : '' }}>File (Tệp đính kèm)</option>
                                        </select>
                                        @error('loai_truong')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Thứ Tự -->
                                    <div class="col-md-6">
                                        <label for="thu_tu" class="form-label">Thứ Tự Hiển Thị</label>
                                        <input type="number" id="thu_tu" name="thu_tu" 
                                               class="form-control"
                                               value="{{ old('thu_tu') }}" 
                                               placeholder="Tự động nếu để trống"
                                               min="0">
                                        <small class="form-text text-muted">Số càng nhỏ hiển thị càng trước</small>
                                    </div>

                                    <!-- Bắt Buộc -->
                                    <div class="col-md-6">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="bat_buoc" name="bat_buoc" 
                                                   {{ old('bat_buoc') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="bat_buoc">
                                                <strong>Bắt buộc</strong> - Người dùng phải điền trường này
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Placeholder -->
                                    <div class="col-md-12">
                                        <label for="placeholder" class="form-label">Placeholder (Gợi ý)</label>
                                        <input type="text" id="placeholder" name="placeholder" 
                                               class="form-control"
                                               value="{{ old('placeholder') }}" 
                                               placeholder="vd: Nhập họ và tên của bạn">
                                        <small class="form-text text-muted">Văn bản gợi ý hiển thị trong ô nhập</small>
                                    </div>

                                    <!-- Gợi ý -->
                                    <div class="col-md-12">
                                        <label for="goi_y" class="form-label">Gợi ý / Hướng Dẫn</label>
                                        <textarea id="goi_y" name="goi_y" 
                                                  class="form-control" 
                                                  rows="2"
                                                  placeholder="vd: Vui lòng nhập đầy đủ họ và tên theo CMND/CCCD">{{ old('goi_y') }}</textarea>
                                        <small class="form-text text-muted">Hướng dẫn thêm cho người dùng</small>
                                    </div>

                                    <!-- Tùy Chọn (Cho Select) -->
                                    <div class="col-md-12" id="tuy_chon_container" style="display: none;">
                                        <label for="tuy_chon" class="form-label">Tùy Chọn (Cho Select) <span class="text-danger">*</span></label>
                                        <textarea id="tuy_chon" name="tuy_chon" 
                                                  class="form-control" 
                                                  rows="4"
                                                  placeholder="Mỗi dòng là một tùy chọn&#10;vd:&#10;Nam&#10;Nữ&#10;Khác">{{ old('tuy_chon') }}</textarea>
                                        <small class="form-text text-muted">Mỗi dòng là một tùy chọn trong dropdown</small>
                                    </div>
                                </div>

                                <!-- Nút Lưu -->
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Lưu Trường
                                    </button>
                                    <a href="{{ route('service-phuong.edit', $service->id) }}" class="btn btn-secondary">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loaiTruong = document.getElementById('loai_truong');
            const tuyChonContainer = document.getElementById('tuy_chon_container');
            const tuyChon = document.getElementById('tuy_chon');

            loaiTruong.addEventListener('change', function() {
                if (this.value === 'select') {
                    tuyChonContainer.style.display = 'block';
                    tuyChon.setAttribute('required', 'required');
                } else {
                    tuyChonContainer.style.display = 'none';
                    tuyChon.removeAttribute('required');
                    tuyChon.value = '';
                }
            });

            // Trigger on page load if old value exists
            if (loaiTruong.value === 'select') {
                tuyChonContainer.style.display = 'block';
            }
        });
    </script>
@endsection

