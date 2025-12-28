@extends('website.components.layout')

@section('title', 'Chỉnh sửa hồ sơ')

@section('content')
<div class="container-fluid py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5 animate-fade-in-up">
            <h1 class="display-4 fw-bold mb-3 text-gradient">Chỉnh sửa hồ sơ</h1>
            <p class="text-muted fs-5 mb-2">
                <i class="fas fa-building text-primary me-2"></i>Phường: <strong>{{ $hoSo->donVi->ten_don_vi }}</strong> | 
                <i class="fas fa-concierge-bell text-success me-2"></i>Dịch vụ: <strong>{{ $hoSo->dichVu->ten_dich_vu }}</strong>
            </p>
            <p class="text-muted mb-2">
                <i class="fas fa-barcode text-info me-2"></i>Mã hồ sơ: <strong>{{ $hoSo->ma_ho_so }}</strong>
            </p>
            <p class="text-muted mb-2">
                <i class="fas fa-calendar text-info me-2"></i>Ngày hẹn: <strong>{{ \Carbon\Carbon::parse($hoSo->ngay_hen)->format('d/m/Y') }}</strong> 
                lúc <strong>{{ $hoSo->gio_hen }}</strong>
            </p>
        </div>

        <div class="row justify-content-center animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="col-md-10 col-lg-9">
                <div class="card shadow-beautiful">
                    <div class="card-header">
                        <h4 class="mb-0 text-white">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa thông tin hồ sơ
                        </h4>
                    </div>
                    <div class="card-body p-5">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('ho-so.update', $hoSo->id) }}" method="POST" enctype="multipart/form-data" id="editForm">
                            @csrf
                            @method('PUT')

                            <!-- Form động theo dịch vụ -->
                            @if($hoSo->dichVu->serviceFields && $hoSo->dichVu->serviceFields->count() > 0)
                                <div class="mb-4">
                                    <h5 class="fw-bold mb-4 text-primary">
                                        <i class="fas fa-file-alt me-2"></i>Thông tin và giấy tờ cần thiết
                                    </h5>
                                </div>
                                
                                @php
                                    $hoSoFieldsMap = $hoSo->hoSoFields->pluck('gia_tri', 'ten_truong')->toArray();
                                @endphp
                                
                                @foreach($hoSo->dichVu->serviceFields as $index => $field)
                                    <div class="mb-4 animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                                        <label class="form-label fw-semibold mb-2">
                                            <i class="fas fa-circle text-primary me-2" style="font-size: 0.5rem;"></i>
                                            {{ $field->nhan_hien_thi }}
                                            @if($field->bat_buoc)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        
                                        @php
                                            $oldValue = old($field->ten_truong, $hoSoFieldsMap[$field->ten_truong] ?? '');
                                        @endphp
                                        
                                        @if($field->loai_truong === 'text' || $field->loai_truong === 'email' || $field->loai_truong === 'number')
                                            <input type="{{ $field->loai_truong }}" 
                                                   name="{{ $field->ten_truong }}" 
                                                   class="form-control @error($field->ten_truong) is-invalid @enderror"
                                                   placeholder="{{ $field->placeholder }}"
                                                   value="{{ $oldValue }}"
                                                   @if($field->bat_buoc) required @endif>
                                            @if($field->goi_y)
                                                <small class="form-text text-muted">{{ $field->goi_y }}</small>
                                            @endif
                                            @error($field->ten_truong)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        
                                        @elseif($field->loai_truong === 'date')
                                            <input type="date" 
                                                   name="{{ $field->ten_truong }}" 
                                                   class="form-control @error($field->ten_truong) is-invalid @enderror"
                                                   placeholder="{{ $field->placeholder }}"
                                                   value="{{ $oldValue }}"
                                                   @if($field->bat_buoc) required @endif>
                                            @if($field->goi_y)
                                                <small class="form-text text-muted">{{ $field->goi_y }}</small>
                                            @endif
                                            @error($field->ten_truong)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        
                                        @elseif($field->loai_truong === 'textarea')
                                            <textarea name="{{ $field->ten_truong }}" 
                                                      class="form-control @error($field->ten_truong) is-invalid @enderror"
                                                      rows="3"
                                                      placeholder="{{ $field->placeholder }}"
                                                      @if($field->bat_buoc) required @endif>{{ $oldValue }}</textarea>
                                            @if($field->goi_y)
                                                <small class="form-text text-muted">{{ $field->goi_y }}</small>
                                            @endif
                                            @error($field->ten_truong)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        
                                        @elseif($field->loai_truong === 'select')
                                            @php
                                                $options = is_array($field->tuy_chon) ? $field->tuy_chon : (is_string($field->tuy_chon) ? json_decode($field->tuy_chon, true) : []);
                                            @endphp
                                            <select name="{{ $field->ten_truong }}" 
                                                    class="form-select @error($field->ten_truong) is-invalid @enderror"
                                                    @if($field->bat_buoc) required @endif>
                                                <option value="">-- Chọn --</option>
                                                @if(is_array($options) && !empty($options))
                                                    @foreach($options as $option)
                                                        <option value="{{ $option }}" {{ $oldValue == $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error($field->ten_truong)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        
                                        @elseif($field->loai_truong === 'file')
                                            @php
                                                // Lấy file từ hoSoFields
                                                $hoSoField = $hoSo->hoSoFields->where('ten_truong', $field->ten_truong)->first();
                                                $existingFile = null;
                                                if ($hoSoField && $hoSoField->gia_tri) {
                                                    // Kiểm tra file có tồn tại không
                                                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($hoSoField->gia_tri)) {
                                                        $existingFile = $hoSoField->gia_tri;
                                                    }
                                                }
                                            @endphp
                                            @if($existingFile)
                                                <div class="mb-3 p-3 bg-light rounded border">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <i class="fas fa-file-pdf text-danger me-2"></i>
                                                            <span class="fw-semibold">File hiện tại:</span>
                                                            <span class="text-muted">{{ basename($existingFile) }}</span>
                                                        </div>
                                                        <a href="{{ asset('storage/' . $existingFile) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-download me-1"></i>Tải xuống
                                                        </a>
                                                    </div>
                                                    <small class="text-muted d-block mt-2">
                                                        <i class="fas fa-info-circle me-1"></i>Để thay đổi file, hãy chọn file mới bên dưới. Để trống nếu không muốn thay đổi.
                                                    </small>
                                                </div>
                                            @endif
                                            <input type="file" 
                                                   name="{{ $field->ten_truong }}" 
                                                   class="form-control @error($field->ten_truong) is-invalid @enderror"
                                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                   @if(!$existingFile && $field->bat_buoc) required @endif>
                                            @if($field->goi_y)
                                                <small class="form-text text-muted">{{ $field->goi_y }}</small>
                                            @endif
                                            <small class="form-text text-muted">Định dạng: PDF, DOC, DOCX, JPG, PNG (Tối đa 20MB).</small>
                                            @error($field->ten_truong)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-warning rounded-beautiful shadow-sm">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Dịch vụ này chưa có form hồ sơ.
                                </div>
                            @endif

                            <!-- Ghi chú -->
                            <div class="mb-4 animate-fade-in-up" style="animation-delay: 0.6s;">
                                <label for="ghi_chu" class="form-label fw-semibold mb-2">
                                    <i class="fas fa-sticky-note text-primary me-2"></i>Ghi chú
                                </label>
                                <textarea name="ghi_chu" id="ghi_chu" 
                                          class="form-control @error('ghi_chu') is-invalid @enderror" 
                                          rows="4" 
                                          placeholder="Nhập ghi chú (nếu có)...">{{ old('ghi_chu', $hoSo->ghi_chu) }}</textarea>
                                @error('ghi_chu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-3 justify-content-end mt-4">
                                <a href="{{ route('info.index', ['action' => 'tab2']) }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i>Hủy
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Lưu thay đổi
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

