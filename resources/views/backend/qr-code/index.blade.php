@extends('backend.components.layout')

@section('title', 'Cấu Hình QR Code Thanh Toán')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-qrcode me-2"></i>Cấu Hình QR Code Thanh Toán
                    </h6>
                    <span class="badge bg-info">{{ $donVi->ten_don_vi }}</span>
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

                    <form method="POST" action="{{ route('admin.qr-code.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Hướng dẫn:</strong> Cấu hình thông tin ngân hàng để người dùng chuyển khoản vào tài khoản của phường <strong>{{ $donVi->ten_don_vi }}</strong>. 
                            Bạn có thể upload ảnh QR code hoặc để hệ thống tự động tạo QR code từ thông tin tài khoản.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-university me-2"></i>Thông Tin Ngân Hàng</h5>
                                
                                <div class="mb-3">
                                    <label for="qr_bank_name" class="form-label">Tên ngân hàng <span class="text-danger">*</span></label>
                                    <input type="text" id="qr_bank_name" name="qr_bank_name" class="form-control"
                                        value="{{ old('qr_bank_name', $donVi->qr_bank_name) }}" 
                                        placeholder="VD: VietinBank, BIDV, Vietcombank, Techcombank..." required>
                                    <small class="text-muted">Tên ngân hàng nơi mở tài khoản</small>
                                </div>

                                <div class="mb-3">
                                    <label for="qr_account_number" class="form-label">Số tài khoản <span class="text-danger">*</span></label>
                                    <input type="text" id="qr_account_number" name="qr_account_number" class="form-control"
                                        value="{{ old('qr_account_number', $donVi->qr_account_number) }}" 
                                        placeholder="VD: 1234567890" required>
                                    <small class="text-muted">Số tài khoản ngân hàng</small>
                                </div>

                                <div class="mb-3">
                                    <label for="qr_account_name" class="form-label">Tên chủ tài khoản <span class="text-danger">*</span></label>
                                    <input type="text" id="qr_account_name" name="qr_account_name" class="form-control"
                                        value="{{ old('qr_account_name', $donVi->qr_account_name) }}" 
                                        placeholder="VD: PHUONG X" required>
                                    <small class="text-muted">Tên chủ tài khoản (viết hoa, không dấu)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-image me-2"></i>Ảnh QR Code</h5>
                                
                                <div class="mb-3">
                                    <label for="qr_image" class="form-label">Upload ảnh QR Code (Tùy chọn)</label>
                                    <input type="file" id="qr_image" name="qr_image" class="form-control" accept="image/*">
                                    <small class="text-muted">
                                        Nếu upload ảnh QR code, hệ thống sẽ sử dụng ảnh này. 
                                        Nếu không, hệ thống sẽ tự động tạo QR code từ thông tin tài khoản. 
                                        Kích thước tối đa: 2MB
                                    </small>
                                </div>

                                @if($donVi->qr_image)
                                <div class="mb-3">
                                    <label class="form-label">Ảnh QR Code hiện tại</label>
                                    <div class="border rounded p-3 text-center bg-light">
                                        <img src="{{ asset('storage/' . $donVi->qr_image) }}" 
                                             alt="QR Code" 
                                             class="img-fluid" 
                                             style="max-height: 300px; max-width: 300px;">
                                        <p class="text-muted small mt-2 mb-0">QR Code hiện tại của phường</p>
                                    </div>
                                </div>
                                @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Chưa có ảnh QR code. Hệ thống sẽ tự động tạo QR code từ thông tin tài khoản.
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="alert alert-warning mt-4">
                            <h6 class="mb-2"><i class="fas fa-lightbulb me-2"></i>Lưu ý:</h6>
                            <ul class="mb-0 small">
                                <li>Nếu bạn upload ảnh QR code, hệ thống sẽ ưu tiên sử dụng ảnh này.</li>
                                <li>Nếu không upload ảnh, hệ thống sẽ tự động tạo QR code theo chuẩn VietQR (EMV QR Code) từ thông tin tài khoản.</li>
                                <li>QR code tự động tạo sẽ tương thích với tất cả ứng dụng ngân hàng tại Việt Nam.</li>
                                <li>Sau khi cấu hình, người dùng thanh toán sẽ chuyển khoản vào tài khoản này.</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Lưu Cấu Hình
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

