@extends('backend.components.layout')

@section('title', 'Chi Tiết Thanh Toán')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-money-bill-wave me-2"></i>Chi Tiết Thanh Toán
                    </h6>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Quay lại
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Thông tin thanh toán -->
                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Thông Tin Thanh Toán</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Mã giao dịch:</th>
                                    <td><strong class="font-monospace">{{ $payment->ma_giao_dich ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Số tiền:</th>
                                    <td><strong class="text-primary fs-5">{{ number_format($payment->so_tien) }} VNĐ</strong></td>
                                </tr>
                                <tr>
                                    <th>Phương thức:</th>
                                    <td>
                                        @if($payment->phuong_thuc_thanh_toan == 'vnpay')
                                            <span class="badge bg-primary">VNPay</span>
                                        @elseif($payment->phuong_thuc_thanh_toan == 'tien_mat')
                                            <span class="badge bg-secondary">Tiền mặt</span>
                                        @elseif($payment->phuong_thuc_thanh_toan == 'chuyen_khoan')
                                            <span class="badge bg-info">Chuyển khoản</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $payment->phuong_thuc_thanh_toan ?? 'N/A' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                        @if($payment->trang_thai_thanh_toan == 'da_thanh_toan')
                                            <span class="badge bg-success fs-6">Đã thanh toán</span>
                                        @elseif($payment->trang_thai_thanh_toan == 'cho_thanh_toan')
                                            <span class="badge bg-warning fs-6">Chờ thanh toán</span>
                                        @elseif($payment->trang_thai_thanh_toan == 'that_bai')
                                            <span class="badge bg-danger fs-6">Thất bại</span>
                                        @elseif($payment->trang_thai_thanh_toan == 'hoan_tien')
                                            <span class="badge bg-secondary fs-6">Hoàn tiền</span>
                                        @else
                                            <span class="badge bg-secondary fs-6">{{ $payment->trang_thai_thanh_toan }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày tạo:</th>
                                    <td>{{ $payment->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @if($payment->ngay_thanh_toan)
                                <tr>
                                    <th>Ngày thanh toán:</th>
                                    <td><strong class="text-success">{{ $payment->ngay_thanh_toan->format('d/m/Y H:i:s') }}</strong></td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        <!-- Thông tin người dùng và hồ sơ -->
                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="fas fa-user me-2"></i>Thông Tin Người Dùng</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Họ tên:</th>
                                    <td>{{ $payment->user->ten ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Số điện thoại:</th>
                                    <td>{{ $payment->user->so_dien_thoai ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $payment->user->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Mã hồ sơ:</th>
                                    <td>
                                        @php
                                            $hoSo = $payment->hoSo;
                                            $daDenNgay = true;
                                            if ($hoSo && $hoSo->ngay_hen) {
                                                $ngayHen = \Carbon\Carbon::parse($hoSo->ngay_hen)->startOfDay();
                                                $ngayHienTai = \Carbon\Carbon::now()->startOfDay();
                                                $daDenNgay = $ngayHienTai->gte($ngayHen);
                                            }
                                        @endphp
                                        @if($daDenNgay && $hoSo)
                                            <a href="{{ route('admin.ho-so.show', $payment->ho_so_id) }}" target="_blank">
                                                {{ $hoSo->ma_ho_so ?? 'N/A' }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ $hoSo->ma_ho_so ?? 'N/A' }}</span>
                                            @if($hoSo && $hoSo->ngay_hen)
                                                <br><small class="text-warning">(Chưa đến ngày: {{ \Carbon\Carbon::parse($hoSo->ngay_hen)->format('d/m/Y') }})</small>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dịch vụ:</th>
                                    <td>{{ $payment->hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Đơn vị/Phường:</th>
                                    <td>{{ $payment->hoSo->donVi->ten_don_vi ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Thông tin VNPay (nếu có) -->
                    @if($payment->phuong_thuc_thanh_toan == 'vnpay' && $payment->du_lieu_vnpay)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Thông Tin Giao Dịch VNPay</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Mã giao dịch VNPay:</th>
                                            <td><strong class="font-monospace">{{ $payment->du_lieu_vnpay['order_id'] ?? $payment->ma_giao_dich }}</strong></td>
                                        </tr>
                                        @if(isset($payment->du_lieu_vnpay['bank_code']))
                                        <tr>
                                            <th>Ngân hàng:</th>
                                            <td><strong>{{ $payment->du_lieu_vnpay['bank_code'] }}</strong></td>
                                        </tr>
                                        @endif
                                        @if(isset($payment->du_lieu_vnpay['bank_tran_no']))
                                        <tr>
                                            <th>Mã giao dịch ngân hàng:</th>
                                            <td><strong class="font-monospace">{{ $payment->du_lieu_vnpay['bank_tran_no'] }}</strong></td>
                                        </tr>
                                        @endif
                                        @if(isset($payment->du_lieu_vnpay['card_type']))
                                        <tr>
                                            <th>Loại thẻ:</th>
                                            <td>{{ $payment->du_lieu_vnpay['card_type'] }}</td>
                                        </tr>
                                        @endif
                                        @if(isset($payment->du_lieu_vnpay['pay_date']))
                                        <tr>
                                            <th>Ngày thanh toán VNPay:</th>
                                            <td>
                                                @php
                                                    $payDate = $payment->du_lieu_vnpay['pay_date'];
                                                    // Format: YYYYMMDDHHmmss
                                                    if(strlen($payDate) == 14) {
                                                        $formattedDate = \Carbon\Carbon::createFromFormat('YmdHis', $payDate)->format('d/m/Y H:i:s');
                                                    } else {
                                                        $formattedDate = $payDate;
                                                    }
                                                @endphp
                                                <strong class="text-success">{{ $formattedDate }}</strong>
                                            </td>
                                        </tr>
                                        @endif
                                        @if(isset($payment->du_lieu_vnpay['response_code']))
                                        <tr>
                                            <th>Mã phản hồi:</th>
                                            <td>
                                                @if($payment->du_lieu_vnpay['response_code'] == '00')
                                                    <span class="badge bg-success">{{ $payment->du_lieu_vnpay['response_code'] }} - Thành công</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $payment->du_lieu_vnpay['response_code'] }} - Thất bại</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if(isset($payment->du_lieu_vnpay['transaction_status']))
                                        <tr>
                                            <th>Trạng thái giao dịch:</th>
                                            <td>
                                                @if($payment->du_lieu_vnpay['transaction_status'] == '00')
                                                    <span class="badge bg-success">Thành công</span>
                                                @else
                                                    <span class="badge bg-danger">Thất bại</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Ảnh chứng từ -->
                    @if($payment->hinh_anh)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3"><i class="fas fa-image me-2"></i>Ảnh Chứng Từ Thanh Toán</h5>
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $payment->hinh_anh) }}" class="img-fluid border rounded shadow" style="max-height: 500px;" alt="Ảnh chứng từ">
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Ghi chú/Giải trình -->
                    @if($payment->giai_trinh)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3"><i class="fas fa-sticky-note me-2"></i>Ghi Chú/Giải Trình</h5>
                            <div class="alert alert-light border">
                                <pre class="mb-0">{{ $payment->giai_trinh }}</pre>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Hành động - CHỈ Admin phường mới được xét duyệt -->
                    @php
                        $currentUser = Auth::guard('admin')->user();
                        $canApprove = $currentUser->isAdminPhuong() && $payment->trang_thai_thanh_toan == 'cho_thanh_toan';
                    @endphp
                    @if($canApprove)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Xét Duyệt Thanh Toán</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success" onclick="approvePayment({{ $payment->id }})">
                                            <i class="fas fa-check me-1"></i>Xác nhận thanh toán
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                            <i class="fas fa-times me-1"></i>Từ chối
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @elseif($currentUser->isAdmin() && $payment->trang_thai_thanh_toan == 'cho_thanh_toan')
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Lưu ý:</strong> Admin tổng chỉ có quyền xem thanh toán. Vui lòng liên hệ Admin phường để xét duyệt.
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal từ chối -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Từ Chối Thanh Toán</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                        <textarea name="ly_do" class="form-control" rows="3" required placeholder="Nhập lý do từ chối..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function approvePayment(id) {
    if (!confirm('Bạn có chắc chắn muốn xác nhận thanh toán này?')) {
        return;
    }

    fetch(`{{ url('admin/payments') }}/${id}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra!');
    });
}

document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const lyDo = this.querySelector('textarea[name="ly_do"]').value;
    if (!lyDo.trim()) {
        alert('Vui lòng nhập lý do từ chối!');
        return;
    }

    if (!confirm('Bạn có chắc chắn muốn từ chối thanh toán này?')) {
        return;
    }

    fetch(`{{ url('admin/payments') }}/{{ $payment->id }}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ly_do: lyDo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra!');
    });
});
</script>
@endpush

