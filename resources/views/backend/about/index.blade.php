@extends('backend.components.layout')

@section('title', 'Quản Lý Bài Giới Thiệu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Quản Lý Bài Giới Thiệu
                    </h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.about.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Thông tin cơ bản -->
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-edit me-2"></i>Thông Tin Cơ Bản</h5>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" id="title" name="title" class="form-control" 
                                    value="{{ old('title', $about->tieu_de ?? '') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea id="content" name="content" class="form-control" rows="10" required>{{ old('content', $about->noi_dung ?? '') }}</textarea>
                                <small class="text-muted">Bạn có thể sử dụng HTML để định dạng nội dung</small>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Hình ảnh</label>
                                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Kích thước tối đa: 2MB. Định dạng: JPG, PNG, GIF</small>
                                @if($about->hinh_anh ?? null)
                                <div class="mt-2">
                                    <p class="mb-1">Hình ảnh hiện tại:</p>
                                    <img src="{{ asset('storage/' . $about->hinh_anh) }}" alt="About" class="img-thumbnail" style="max-width: 300px;">
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Sứ mệnh, Tầm nhìn, Giá trị -->
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-bullseye me-2"></i>Sứ Mệnh - Tầm Nhìn - Giá Trị</h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="mission" class="form-label">Sứ mệnh</label>
                                    <textarea id="mission" name="mission" class="form-control" rows="5">{{ old('mission', $about->su_menh ?? '') }}</textarea>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="vision" class="form-label">Tầm nhìn</label>
                                    <textarea id="vision" name="vision" class="form-control" rows="5">{{ old('vision', $about->tam_nhin ?? '') }}</textarea>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="values" class="form-label">Giá trị</label>
                                    <textarea id="values" name="values" class="form-control" rows="5">{{ old('values', $about->gia_tri ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin liên hệ -->
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-address-card me-2"></i>Thông Tin Liên Hệ</h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="text" id="phone" name="phone" class="form-control" 
                                        value="{{ old('phone', $about->so_dien_thoai ?? '') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" 
                                        value="{{ old('email', $about->email ?? '') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="address" class="form-label">Địa chỉ</label>
                                    <input type="text" id="address" name="address" class="form-control" 
                                        value="{{ old('address', $about->dia_chi ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Lưu Thay Đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Có thể thêm editor WYSIWYG cho textarea nếu cần
    // Ví dụ: CKEditor, TinyMCE, Summernote...
</script>
@endpush

