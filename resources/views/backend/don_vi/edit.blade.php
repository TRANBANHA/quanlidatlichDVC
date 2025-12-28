@extends('backend.components.layout')
@section('title')
    Sửa đơn vị/phường
@endsection
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Sửa đơn vị/phường</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="/admin">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('don-vi.index') }}">Danh sách đơn vị</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li>Sửa</li>
                </ul>
            </div>

            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('don-vi.update', $donVi) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="ten_don_vi" class="form-label">Tên đơn vị/phường <span class="text-danger">*</span></label>
                            <input type="text" id="ten_don_vi" name="ten_don_vi" class="form-control"
                                value="{{ old('ten_don_vi', $donVi->ten_don_vi) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô tả</label>
                            <textarea id="mo_ta" name="mo_ta" class="form-control" rows="3">{{ old('mo_ta', $donVi->mo_ta) }}</textarea>
                        </div>

                        @php
                            $currentUser = Auth::guard('admin')->user();
                            $canEditQR = $currentUser->isAdmin() || ($currentUser->isAdminPhuong() && $currentUser->don_vi_id == $donVi->id);
                        @endphp

                        @if($canEditQR)
                        <hr>
                        <h5 class="mb-3"><i class="fas fa-qrcode me-2"></i>Cấu Hình QR Code Thanh Toán</h5>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Cấu hình thông tin ngân hàng để người dùng chuyển khoản vào tài khoản của phường này.
                        </div>
                        <div class="mb-3">
                            <label for="qr_bank_name" class="form-label">Tên ngân hàng</label>
                            <input type="text" id="qr_bank_name" name="qr_bank_name" class="form-control"
                                value="{{ old('qr_bank_name', $donVi->qr_bank_name) }}" placeholder="VD: VietinBank, BIDV, Vietcombank...">
                        </div>
                        <div class="mb-3">
                            <label for="qr_account_number" class="form-label">Số tài khoản</label>
                            <input type="text" id="qr_account_number" name="qr_account_number" class="form-control"
                                value="{{ old('qr_account_number', $donVi->qr_account_number) }}" placeholder="VD: 1234567890">
                        </div>
                        <div class="mb-3">
                            <label for="qr_account_name" class="form-label">Tên chủ tài khoản</label>
                            <input type="text" id="qr_account_name" name="qr_account_name" class="form-control"
                                value="{{ old('qr_account_name', $donVi->qr_account_name) }}" placeholder="VD: PHUONG X">
                        </div>
                        <div class="mb-3">
                            <label for="qr_image" class="form-label">Ảnh QR Code (tùy chọn)</label>
                            <input type="file" id="qr_image" name="qr_image" class="form-control" accept="image/*">
                            <small class="text-muted">Nếu không upload ảnh, hệ thống sẽ tự động tạo QR code từ thông tin tài khoản.</small>
                            @if($donVi->qr_image)
                            <div class="mt-2">
                                <p class="mb-1">Ảnh hiện tại:</p>
                                <img src="{{ asset('storage/' . $donVi->qr_image) }}" alt="QR Code" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                            @endif
                        </div>
                        @endif

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <a href="{{ route('don-vi.index') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

