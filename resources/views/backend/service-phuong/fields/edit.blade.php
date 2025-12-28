@extends('backend.components.layout')

@section('title')
    Sửa Trường Form
@endsection

@section('content')
    <div class="container">
        <div class="page-inner">
            <!-- Tiêu đề Trang -->
            <div class="page-header d-flex justify-content-between align-items-center">
                <h3 class="fw-bold text-primary"><i class="fas fa-edit me-2"></i> Sửa Trường Form</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="icon-home"></i> Trang Chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('service-phuong.index') }}">Quản Lý Dịch Vụ Phường</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('service-phuong.edit', $service->id) }}">{{ $service->ten_dich_vu }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sửa Trường</li>
                    </ol>
                </nav>
            </div>

            <!-- Form Section -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-10">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Sửa Trường Form</h5>
                            <small>Dịch vụ: <strong>{{ $service->ten_dich_vu }}</strong> | Trường: <code>{{ $field->ten_truong }}</code></small>
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

                            <form action="{{ route('service-phuong.fields.update', [$service->id, $field->id]) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row gy-3">
                                    <!-- Tên Trường (Read-only) -->
                                    <div class="col-md-6">
                                        <label for="ten_truong" class="form-label">Tên Trường (Field Name)</label>
                                        <input type="text" id="ten_truong" 
                                               class="form-control" 
                                               value="{{ $field->ten_truong }}" 
                                               disabled>
                                        <small class="form-text text-muted">Tên trường không thể thay đổi sau khi tạo</small>
                                    </div>

                                    <!-- Nhãn Hiển Thị -->
                                    <div class="col-md-6">
                                        <label for="nhan_hien_thi" class="form-label">Nhãn Hiển Thị <span class="text-danger">*</span></label>
                                        <input type="text" id="nhan_hien_thi" name="nhan_hien_thi" 
                                               class="form-control @error('nhan_hien_thi') is-invalid @enderror"
                                               value="{{ old('nhan_hien_thi', $field->nhan_hien_thi) }}" 
                                               placeholder="vd: Họ và tên, Ngày sinh" 
                                               required>
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
                                            <option value="text" {{ old('loai_truong', $field->loai_truong) == 'text' ? 'selected' : '' }}>Text (Văn bản)</option>
                                            <option value="email" {{ old('loai_truong', $field->loai_truong) == 'email' ? 'selected' : '' }}>Email</option>
                                            <option value="number" {{ old('loai_truong', $field->loai_truong) == 'number' ? 'selected' : '' }}>Number (Số)</option>
                                            <option value="date" {{ old('loai_truong', $field->loai_truong) == 'date' ? 'selected' : '' }}>Date (Ngày tháng)</option>
                                            <option value="textarea" {{ old('loai_truong', $field->loai_truong) == 'textarea' ? 'selected' : '' }}>Textarea (Văn bản dài)</option>
                                            <option value="select" {{ old('loai_truong', $field->loai_truong) == 'select' ? 'selected' : '' }}>Select (Dropdown)</option>
                                            <option value="file" {{ old('loai_truong', $field->loai_truong) == 'file' ? 'selected' : '' }}>File (Tệp đính kèm)</option>
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
                                               value="{{ old('thu_tu', $field->thu_tu) }}" 
                                               min="0">
                                        <small class="form-text text-muted">Số càng nhỏ hiển thị càng trước</small>
                                    </div>

                                    <!-- Bắt Buộc -->
                                    <div class="col-md-6">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="bat_buoc" name="bat_buoc" 
                                                   {{ old('bat_buoc', $field->bat_buoc) ? 'checked' : '' }}>
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
                                               value="{{ old('placeholder', $field->placeholder) }}" 
                                               placeholder="vd: Nhập họ và tên của bạn">
                                    </div>

                                    <!-- Gợi ý -->
                                    <div class="col-md-12">
                                        <label for="goi_y" class="form-label">Gợi ý / Hướng Dẫn</label>
                                        <textarea id="goi_y" name="goi_y" 
                                                  class="form-control" 
                                                  rows="2">{{ old('goi_y', $field->goi_y) }}</textarea>
                                    </div>

                                    <!-- Tùy Chọn (Cho Select) -->
                                    <div class="col-md-12" id="tuy_chon_container" style="display: none;">
                                        <label for="tuy_chon" class="form-label">Tùy Chọn (Cho Select) <span class="text-danger">*</span></label>
                                        <textarea id="tuy_chon" name="tuy_chon" 
                                                  class="form-control" 
                                                  rows="4"
                                                  placeholder="Mỗi dòng là một tùy chọn">{{ old('tuy_chon', $field->tuy_chon ? implode("\n", json_decode($field->tuy_chon, true)) : '') }}</textarea>
                                        <small class="form-text text-muted">Mỗi dòng là một tùy chọn trong dropdown</small>
                                    </div>
                                </div>

                                <!-- Nút Lưu -->
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Lưu Thay Đổi
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

            function toggleTuyChon() {
                if (loaiTruong.value === 'select') {
                    tuyChonContainer.style.display = 'block';
                    tuyChon.setAttribute('required', 'required');
                } else {
                    tuyChonContainer.style.display = 'none';
                    tuyChon.removeAttribute('required');
                }
            }

            loaiTruong.addEventListener('change', toggleTuyChon);
            
            // Trigger on page load
            toggleTuyChon();
        });
    </script>
@endsection

