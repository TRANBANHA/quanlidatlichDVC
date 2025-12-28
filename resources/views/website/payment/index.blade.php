@extends('website.components.layout')

@section('title', 'Danh Sách Thanh Toán')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh Sách Thanh Toán</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã giao dịch</th>
                                    <th>Hồ sơ</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $index => $payment)
                                <tr>
                                    <td>{{ ($payments->currentPage() - 1) * $payments->perPage() + $index + 1 }}</td>
                                    <td><strong>{{ $payment->ma_giao_dich ?? 'N/A' }}</strong></td>
                                    <td>{{ $payment->hoSo->ma_ho_so ?? 'N/A' }}</td>
                                    <td><strong class="text-primary">{{ number_format($payment->so_tien) }} VNĐ</strong></td>
                                    <td>
                                        @if($payment->trang_thai_thanh_toan == 'da_thanh_toan')
                                            <span class="badge bg-success">Đã thanh toán</span>
                                        @elseif($payment->trang_thai_thanh_toan == 'cho_thanh_toan')
                                            <span class="badge bg-warning">Chờ thanh toán</span>
                                        @elseif($payment->trang_thai_thanh_toan == 'that_bai')
                                            <span class="badge bg-danger">Thất bại</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $payment->trang_thai_thanh_toan }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('payment.show', $payment->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i>Xem
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

