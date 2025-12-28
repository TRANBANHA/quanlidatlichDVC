@extends('backend.components.layout')
@section('title')
    Chỉnh sửa tài khoản quản trị
@endsection
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="#">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="/admin">Trang chủ</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('quantri.index') }}">Quản lý tài khoản</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">Chỉnh sửa</li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Chỉnh sửa tài khoản</div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('quantri.update', $admin->id) }}" method="POST" class="p-3 border rounded">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="ho_ten" class="form-label">Họ tên <span class="text-danger">*</span></label>
                                            <input type="text" id="ho_ten" name="ho_ten" class="form-control"
                                                value="{{ old('ho_ten', $admin->ho_ten) }}" placeholder="Nhập họ tên" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="ten_dang_nhap" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                            <input type="text" id="ten_dang_nhap" name="ten_dang_nhap" class="form-control"
                                                value="{{ old('ten_dang_nhap', $admin->ten_dang_nhap) }}" placeholder="Nhập tên đăng nhập" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="mat_khau" class="form-label">Mật khẩu mới</label>
                                            <input type="password" id="mat_khau" name="mat_khau" class="form-control"
                                                placeholder="Để trống nếu không đổi mật khẩu">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                value="{{ old('email', $admin->email) }}" placeholder="Nhập email">
                                        </div>
                                        <div class="mb-3">
                                            <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                                            <input type="text" id="so_dien_thoai" name="so_dien_thoai" class="form-control"
                                                value="{{ old('so_dien_thoai', $admin->so_dien_thoai) }}" placeholder="Nhập số điện thoại">
                                        </div>
                                        <div class="mb-3">
                                            <label for="quyen" class="form-label">Quyền <span class="text-danger">*</span></label>
                                            @php
                                                $currentUser = Auth::guard('admin')->user();
                                            @endphp
                                            @if($currentUser->isAdminPhuong())
                                                <input type="hidden" name="quyen" value="{{ $admin->quyen }}">
                                                <input type="text" class="form-control" value="{{ \App\Models\Admin::getRoleName($admin->quyen) }}" disabled>
                                                <small class="text-muted">Bạn không thể thay đổi quyền</small>
                                            @else
                                                <select id="quyen" name="quyen" class="form-select" required>
                                                    <option value="1" {{ old('quyen', $admin->quyen) == 1 ? 'selected' : '' }}>Admin tổng</option>
                                                    <option value="2" {{ old('quyen', $admin->quyen) == 2 ? 'selected' : '' }}>Admin phường</option>
                                                    <option value="0" {{ old('quyen', $admin->quyen) == 0 ? 'selected' : '' }}>Cán bộ phường</option>
                                                </select>
                                                <small class="text-muted">Admin phường và Cán bộ phường cần chọn đơn vị/phường</small>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label for="don_vi_id" class="form-label">Đơn vị/Phường</label>
                                            @if($currentUser->isAdminPhuong())
                                                <input type="hidden" name="don_vi_id" value="{{ $admin->don_vi_id }}">
                                                <input type="text" class="form-control" value="{{ $admin->donVi->ten_don_vi ?? '' }}" disabled>
                                                <small class="text-muted">Bạn không thể thay đổi đơn vị</small>
                                            @else
                                                <select id="don_vi_id" name="don_vi_id" class="form-select">
                                                    <option value="">-- Chọn đơn vị/phường --</option>
                                                    @foreach ($donVis as $donVi)
                                                        <option value="{{ $donVi->id }}" {{ old('don_vi_id', $admin->don_vi_id) == $donVi->id ? 'selected' : '' }}>
                                                            {{ $donVi->ten_don_vi }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Chỉ áp dụng cho Admin phường và Cán bộ phường</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                    <a href="{{ route('quantri.index') }}" class="btn btn-secondary">Hủy</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
