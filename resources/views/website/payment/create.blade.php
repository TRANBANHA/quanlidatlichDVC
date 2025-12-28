@extends('website.components.layout')

@section('title', 'Thanh Toán Hồ Sơ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Thanh Toán Hồ Sơ</h5>
                </div>
                <div class="card-body">
                    @if($existingPayment && $existingPayment->trang_thai_thanh_toan == 'da_thanh_toan')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>Hồ sơ này đã được thanh toán thành công.
                        </div>
                        <a href="{{ route('payment.show', $existingPayment->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i>Xem chi tiết thanh toán
                        </a>
                    @else
                        <!-- Thông tin hồ sơ -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Thông tin hồ sơ</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Mã hồ sơ:</th>
                                    <td><strong>{{ $hoSo->ma_ho_so }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Dịch vụ:</th>
                                    <td>{{ $hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Đơn vị:</th>
                                    <td>{{ $hoSo->donVi->ten_don_vi ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày hẹn:</th>
                                    <td>{{ $hoSo->ngay_hen ? $hoSo->ngay_hen->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Giờ hẹn:</th>
                                    <td>{{ $hoSo->gio_hen ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Thông tin thanh toán -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Thông tin thanh toán</h6>
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><strong>Phí dịch vụ:</strong></span>
                                    <span class="h4 text-primary mb-0">{{ number_format($phiDichVu) }} VNĐ</span>
                                </div>
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Phương thức thanh toán</h6>
                            @php
                                $payment = $existingPayment;
                            @endphp
                            
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h5 class="mb-3"><i class="fas fa-qrcode me-2 text-success"></i>Thanh toán bằng QR Code</h5>
                                    
                                    @if($useQRImage && $qrImage)
                                        <!-- Hiển thị ảnh QR code đã upload -->
                                        <div class="mb-3">
                                            <img src="{{ asset('storage/' . $qrImage) }}" alt="QR Code" class="img-fluid" style="max-width: 300px; max-height: 300px;">
                                        </div>
                                        
                                        <!-- Mã thanh toán -->
                                        <div class="alert alert-warning mb-3 position-relative">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong>Mã thanh toán:</strong>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="copyPaymentCode('{{ $payment->ma_giao_dich }}')" title="Copy mã thanh toán">
                                                    <i class="fas fa-copy me-1"></i>Copy
                                                </button>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <h4 class="mb-0 mt-2 font-monospace flex-grow-1" id="payment-code-1">{{ $payment->ma_giao_dich }}</h4>
                                            </div>
                                            <small class="text-muted d-block mt-2">Vui lòng nhập đúng mã này khi chuyển khoản</small>
                                        </div>
                                        
                                        <!-- Thông tin tài khoản (nếu có) -->
                                        @if($qrBankName || $qrAccountNumber || $qrAccountName)
                                        <div class="card bg-light mb-3">
                                            <div class="card-body text-start">
                                                <h6 class="mb-3"><i class="fas fa-university me-2"></i>Thông tin tài khoản nhận tiền:</h6>
                                                <table class="table table-sm mb-0">
                                                    @if($qrBankName)
                                                    <tr>
                                                        <th width="40%">Ngân hàng:</th>
                                                        <td><strong>{{ $qrBankName }}</strong></td>
                                                    </tr>
                                                    @endif
                                                    @if($qrAccountNumber)
                                                    <tr>
                                                        <th>Số tài khoản:</th>
                                                        <td><strong class="font-monospace">{{ $qrAccountNumber }}</strong></td>
                                                    </tr>
                                                    @endif
                                                    @if($qrAccountName)
                                                    <tr>
                                                        <th>Chủ tài khoản:</th>
                                                        <td><strong>{{ $qrAccountName }}</strong></td>
                                                    </tr>
                                                    @endif
                                                    <tr>
                                                        <th>Số tiền:</th>
                                                        <td><strong class="text-primary">{{ number_format($phiDichVu) }} VNĐ</strong></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                        
                                    @elseif($qrAccountNumber && $qrAccountName && $qrContent)
                                        <!-- Tạo QR code tự động -->
                                        <div class="mb-3">
                                            <div id="qrcode" class="d-inline-block p-3 bg-white border rounded"></div>
                                        </div>
                                        
                                        <!-- Mã thanh toán -->
                                        <div class="alert alert-warning mb-3 position-relative">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong>Mã thanh toán:</strong>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="copyPaymentCode('{{ $payment->ma_giao_dich }}')" title="Copy mã thanh toán">
                                                    <i class="fas fa-copy me-1"></i>Copy
                                                </button>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <h4 class="mb-0 mt-2 font-monospace flex-grow-1" id="payment-code-1">{{ $payment->ma_giao_dich }}</h4>
                                            </div>
                                            <small class="text-muted d-block mt-2">Vui lòng nhập đúng mã này khi chuyển khoản</small>
                                        </div>
                                        
                                        <!-- Thông tin tài khoản -->
                                        <div class="card bg-light mb-3">
                                            <div class="card-body text-start">
                                                <h6 class="mb-3"><i class="fas fa-university me-2"></i>Thông tin tài khoản nhận tiền:</h6>
                                                <table class="table table-sm mb-0">
                                                    @if($qrBankName)
                                                    <tr>
                                                        <th width="40%">Ngân hàng:</th>
                                                        <td><strong>{{ $qrBankName }}</strong></td>
                                                    </tr>
                                                    @endif
                                                    <tr>
                                                        <th>Số tài khoản:</th>
                                                        <td><strong class="font-monospace">{{ $qrAccountNumber }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Chủ tài khoản:</th>
                                                        <td><strong>{{ $qrAccountName }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Số tiền:</th>
                                                        <td><strong class="text-primary">{{ number_format($phiDichVu) }} VNĐ</strong></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Chưa cấu hình thông tin QR code. Vui lòng liên hệ quản trị viên.
                                        </div>
                                    @endif
                                    
                                    @if($useQRImage || ($qrAccountNumber && $qrAccountName))
                                        <!-- Hướng dẫn -->
                                        <div class="alert alert-info text-start">
                                            <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Hướng dẫn thanh toán:</h6>
                                            <ol class="mb-0">
                                                <li>Mở ứng dụng ngân hàng trên điện thoại</li>
                                                <li>Quét QR code ở trên hoặc nhập mã thanh toán: <strong>{{ $payment->ma_giao_dich }}</strong></li>
                                                <li>Kiểm tra thông tin và xác nhận chuyển khoản</li>
                                                <li>Sau khi chuyển khoản, upload ảnh chứng từ bên dưới để được xác nhận</li>
                                            </ol>
                                        </div>
                                        
                                        <!-- Form upload ảnh chứng từ -->
                                        @if($payment->trang_thai_thanh_toan == 'cho_thanh_toan')
                                        <div class="card border-warning mb-3">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0"><i class="fas fa-upload me-2"></i>Upload ảnh chứng từ thanh toán</h6>
                                            </div>
                                            <div class="card-body">
                                                <form method="POST" action="{{ route('payment.upload-proof', $payment->id) }}" enctype="multipart/form-data" id="uploadProofForm">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="form-label">Ảnh chứng từ thanh toán <span class="text-danger">*</span></label>
                                                        <input type="file" name="proof_image" class="form-control" accept="image/*" required>
                                                        <small class="text-muted">Upload ảnh chụp màn hình hoặc biên lai chuyển khoản (JPG, PNG, tối đa 2MB)</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Ghi chú (tùy chọn)</label>
                                                        <textarea name="note" class="form-control" rows="2" placeholder="Thêm ghi chú nếu cần..."></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-warning w-100">
                                                        <i class="fas fa-upload me-2"></i>Upload và gửi xác nhận
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <!-- Nút kiểm tra trạng thái -->
                                        <div class="d-grid gap-2">
                                            <button type="button" id="checkPaymentBtn" class="btn btn-success btn-lg">
                                                <i class="fas fa-sync-alt me-2"></i>Kiểm tra trạng thái thanh toán
                                            </button>
                                            <a href="{{ route('info.index', ['action' => 'tab2']) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                                            </a>
                                        </div>
                                        
                                        <!-- Thông tin về cách xác nhận -->
                                        <div class="alert alert-secondary mt-3 text-start">
                                            <h6 class="mb-2"><i class="fas fa-question-circle me-2"></i>Cách hệ thống xác nhận thanh toán:</h6>
                                            <ul class="mb-0 small">
                                                <li><strong>Tự động:</strong> Nếu bạn upload ảnh chứng từ, admin sẽ xem và xác nhận</li>
                                                <li><strong>Kiểm tra thủ công:</strong> Nhấn nút "Kiểm tra trạng thái" để cập nhật trạng thái mới nhất</li>
                                                <li><strong>Webhook:</strong> Nếu tích hợp với ngân hàng, hệ thống sẽ tự động xác nhận khi nhận được thông báo</li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($existingPayment && $existingPayment->trang_thai_thanh_toan == 'that_bai')
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Lần thanh toán trước đã thất bại. Vui lòng thử lại.
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(isset($qrContent) && $qrContent && !$useQRImage)
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    // Tạo QR code tự động
    if (document.getElementById("qrcode")) {
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $qrContent }}",
            width: 300,
            height: 300,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }
</script>
@endif
<script>
    
    // Kiểm tra trạng thái thanh toán
    let checkInterval;
    const checkPaymentBtn = document.getElementById('checkPaymentBtn');
    const paymentId = {{ $payment->id }};
    
    if (checkPaymentBtn) {
        checkPaymentBtn.addEventListener('click', function() {
            checkPaymentStatus();
            
            // Tự động kiểm tra mỗi 5 giây
            if (!checkInterval) {
                checkInterval = setInterval(checkPaymentStatus, 5000);
                checkPaymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang kiểm tra...';
                checkPaymentBtn.disabled = true;
            }
        });
    }
    
    function checkPaymentStatus() {
        fetch(`{{ route('payment.qr.check-status', '') }}/${paymentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'da_thanh_toan') {
                    clearInterval(checkInterval);
                    checkPaymentBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Đã thanh toán thành công!';
                    checkPaymentBtn.classList.remove('btn-success');
                    checkPaymentBtn.classList.add('btn-primary');
                    checkPaymentBtn.disabled = true;
                    
                    // Chuyển hướng sau 2 giây
                    setTimeout(() => {
                        window.location.href = `{{ route('payment.show', '') }}/${paymentId}`;
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    // Tự động kiểm tra khi trang được load (nếu đang chờ thanh toán)
    @if($payment->trang_thai_thanh_toan == 'cho_thanh_toan')
    setTimeout(() => {
        if (checkPaymentBtn) {
            checkPaymentStatus();
            checkInterval = setInterval(checkPaymentStatus, 5000);
        }
    }, 3000);
    @endif

    // Copy mã thanh toán
    function copyPaymentCode(code) {
        // Tạo một textarea tạm thời để copy
        const textarea = document.createElement('textarea');
        textarea.value = code;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        textarea.setSelectionRange(0, 99999); // Cho mobile
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                // Hiển thị thông báo thành công
                showCopyNotification('Đã copy mã thanh toán!');
            } else {
                // Fallback: Sử dụng Clipboard API
                navigator.clipboard.writeText(code).then(() => {
                    showCopyNotification('Đã copy mã thanh toán!');
                }).catch(() => {
                    showCopyNotification('Không thể copy. Vui lòng copy thủ công.', 'error');
                });
            }
        } catch (err) {
            // Fallback: Sử dụng Clipboard API
            navigator.clipboard.writeText(code).then(() => {
                showCopyNotification('Đã copy mã thanh toán!');
            }).catch(() => {
                showCopyNotification('Không thể copy. Vui lòng copy thủ công.', 'error');
            });
        }
        
        document.body.removeChild(textarea);
    }

    // Hiển thị thông báo copy
    function showCopyNotification(message, type = 'success') {
        // Tạo thông báo
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : 'success'} position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
        notification.innerHTML = `
            <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            notification.style.transition = 'opacity 0.5s';
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 500);
        }, 3000);
    }
</script>
@endpush


