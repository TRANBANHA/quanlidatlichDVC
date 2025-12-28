@extends('backend.components.layout')
@section('title', 'Cấu Hình Website')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Cấu Hình Website</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Thông tin cơ bản -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3"><i class="fas fa-info-circle me-2"></i>Thông Tin Cơ Bản</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tên website <span class="text-danger">*</span></label>
                                    <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings['site_name'] ?? '') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email liên hệ</label>
                                    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Địa chỉ</label>
                                    <input type="text" name="contact_address" class="form-control" value="{{ old('contact_address', $settings['contact_address'] ?? '') }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Mô tả website</label>
                                    <textarea name="site_description" class="form-control" rows="3">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Từ khóa SEO (phân cách bằng dấu phẩy)</label>
                                    <input type="text" name="site_keywords" class="form-control" value="{{ old('site_keywords', $settings['site_keywords'] ?? '') }}" placeholder="ví dụ: đặt lịch, dịch vụ công, hành chính">
                                </div>
                            </div>
                        </div>

                        <!-- Logo và Favicon -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3"><i class="fas fa-image me-2"></i>Logo & Favicon</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Logo</label>
                                    @if($settings['site_logo'] ?? null)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo" style="max-height: 100px;">
                                        </div>
                                    @endif
                                    <input type="file" name="site_logo" class="form-control" accept="image/*">
                                    <small class="text-muted">Kích thước tối đa: 2MB. Định dạng: JPEG, PNG, JPG, GIF</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Favicon</label>
                                    @if($settings['site_favicon'] ?? null)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $settings['site_favicon']) }}" alt="Favicon" style="max-height: 50px;">
                                        </div>
                                    @endif
                                    <input type="file" name="site_favicon" class="form-control" accept="image/*">
                                    <small class="text-muted">Kích thước tối đa: 512KB. Định dạng: JPEG, PNG, JPG, ICO</small>
                                </div>
                            </div>
                        </div>

                        <!-- Mạng xã hội -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3"><i class="fas fa-share-alt me-2"></i>Mạng Xã Hội</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Facebook URL</label>
                                    <input type="url" name="facebook_url" class="form-control" value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}" placeholder="https://facebook.com/...">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">YouTube URL</label>
                                    <input type="url" name="youtube_url" class="form-control" value="{{ old('youtube_url', $settings['youtube_url'] ?? '') }}" placeholder="https://youtube.com/...">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Zalo URL</label>
                                    <input type="url" name="zalo_url" class="form-control" value="{{ old('zalo_url', $settings['zalo_url'] ?? '') }}" placeholder="https://zalo.me/...">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Lưu Cấu Hình
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

