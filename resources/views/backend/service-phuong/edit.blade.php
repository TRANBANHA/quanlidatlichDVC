@extends('backend.components.layout')

@section('title')
    Cấu Hình Dịch Vụ
@endsection

@section('content')
    <div class="container">
        <div class="page-inner">
            <!-- Tiêu đề Trang -->
            <div class="page-header d-flex justify-content-between align-items-center">
                <h3 class="fw-bold text-primary"><i class="fas fa-cog me-2"></i> Cấu Hình Dịch Vụ</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="icon-home"></i> Trang Chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('service-phuong.index') }}">Quản Lý Dịch Vụ Phường</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cấu Hình</li>
                    </ol>
                </nav>
            </div>

            <!-- Form Section -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-10">
                    <!-- Thông tin dịch vụ -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Thông Tin Dịch Vụ</h5>
                        </div>
                        <div class="card-body p-4">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('service-phuong.update-service', $service->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row gy-3">
                                    <!-- Tên Dịch Vụ -->
                                    <div class="col-md-12">
                                        <label for="ten_dich_vu" class="form-label">Tên Dịch Vụ <span class="text-danger">*</span></label>
                                        <input type="text" id="ten_dich_vu" name="ten_dich_vu"
                                            class="form-control"
                                            value="{{ old('ten_dich_vu', $service->ten_dich_vu) }}"
                                            placeholder="Nhập tên dịch vụ" required>
                                    </div>

                                    <!-- Mô Tả -->
                                    <div class="col-md-12">
                                        <label for="mo_ta" class="form-label">Mô Tả</label>
                                        <textarea id="mo_ta" name="mo_ta" class="form-control" rows="4" placeholder="Nhập mô tả chi tiết về dịch vụ">{{ old('mo_ta', $service->mo_ta) }}</textarea>
                                    </div>
                                </div>

                                <!-- Nút Lưu -->
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Lưu Thông Tin
                                    </button>
                                    <a href="{{ route('service-phuong.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Quay Lại
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Quản lý Form Fields -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> Cấu Hình Form Đăng Ký</h5>
                            <a href="{{ route('service-phuong.fields.create', $service->id) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-1"></i> Thêm Trường
                            </a>
                        </div>
                        <div class="card-body p-4">
                            @if($service->serviceFields && $service->serviceFields->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>STT</th>
                                                <th>Tên Trường</th>
                                                <th>Nhãn Hiển Thị</th>
                                                <th>Loại</th>
                                                <th>Bắt Buộc</th>
                                                <th>Thứ Tự</th>
                                                <th>Thao Tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($service->serviceFields as $index => $field)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><code>{{ $field->ten_truong }}</code></td>
                                                    <td>{{ $field->nhan_hien_thi }}</td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            @if($field->loai_truong === 'text') Text
                                                            @elseif($field->loai_truong === 'textarea') Textarea
                                                            @elseif($field->loai_truong === 'file') File
                                                            @elseif($field->loai_truong === 'date') Date
                                                            @elseif($field->loai_truong === 'select') Select
                                                            @elseif($field->loai_truong === 'number') Number
                                                            @elseif($field->loai_truong === 'email') Email
                                                            @else {{ $field->loai_truong }}
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($field->bat_buoc)
                                                            <span class="badge bg-danger">Bắt buộc</span>
                                                        @else
                                                            <span class="badge bg-secondary">Tùy chọn</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $field->thu_tu }}</td>
                                                    <td>
                                                        <a href="{{ route('service-phuong.fields.edit', [$service->id, $field->id]) }}" 
                                                           class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('service-phuong.fields.destroy', [$service->id, $field->id]) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Bạn có chắc muốn xóa trường này?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Chưa có trường form nào. 
                                    <a href="{{ route('service-phuong.fields.create', $service->id) }}" class="alert-link">Thêm trường đầu tiên</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

