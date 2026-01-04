@extends('backend.components.layout')

@section('title', 'Quản Lý Thanh Toán')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-money-bill-wave me-2"></i>Quản Lý Thanh Toán
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        // Tính toán thống kê từ $thongKeTrangThai
                        $stats = [
                            'total' => $tongSoGiaoDich ?? 0,
                            'cho_thanh_toan' => $thongKeTrangThai->firstWhere('trang_thai_thanh_toan', 'cho_thanh_toan')->so_luong ?? 0,
                            'da_thanh_toan' => $thongKeTrangThai->firstWhere('trang_thai_thanh_toan', 'da_thanh_toan')->so_luong ?? 0,
                            'co_anh' => $payments->whereNotNull('hinh_anh')->where('trang_thai_thanh_toan', 'cho_thanh_toan')->count()
                        ];
                    @endphp
                    <!-- Thống kê -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Tổng thanh toán
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Chờ thanh toán
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['cho_thanh_toan'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Đã thanh toán
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['da_thanh_toan'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Có ảnh chờ duyệt
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['co_anh'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-image fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bộ lọc -->
                    <form method="GET" action="{{ route('admin.payments.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="trang_thai" class="form-select">
                                    <option value="">-- Tất cả trạng thái --</option>
                                    <option value="cho_thanh_toan" {{ request('trang_thai') == 'cho_thanh_toan' ? 'selected' : '' }}>Chờ thanh toán</option>
                                    <option value="da_thanh_toan" {{ request('trang_thai') == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                                    <option value="that_bai" {{ request('trang_thai') == 'that_bai' ? 'selected' : '' }}>Thất bại</option>
                                    <option value="hoan_tien" {{ request('trang_thai') == 'hoan_tien' ? 'selected' : '' }}>Hoàn tiền</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="phuong_thuc" class="form-select">
                                    <option value="">-- Tất cả phương thức --</option>
                                    <option value="vnpay" {{ request('phuong_thuc') == 'vnpay' ? 'selected' : '' }}>VNPay</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm (mã GD, tên, SĐT, mã hồ sơ)..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Bảng danh sách -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Mã giao dịch</th>
                                    <th>Người dùng</th>
                                    <th>Hồ sơ</th>
                                    <th>Số tiền</th>
                                    <th>Phương thức</th>
                                    <th>Trạng thái</th>
                                    <th>Ảnh chứng từ</th>
                                    <th>Ngày tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $index => $payment)
                                <tr>
                                    <td>{{ ($payments->currentPage() - 1) * $payments->perPage() + $index + 1 }}</td>
                                    <td>
                                        <strong class="font-monospace">{{ $payment->ma_giao_dich ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $payment->user->ten ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $payment->user->so_dien_thoai ?? '' }}</small>
                                        </div>
                                    </td>
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
                                    <td>
                                        <strong class="text-primary">{{ number_format($payment->so_tien) }} VNĐ</strong>
                                    </td>
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
                                    <td>
                                        @if($payment->trang_thai_thanh_toan == 'da_thanh_toan')
                                            <span class="badge bg-success">Đã thanh toán</span>
                                        @elseif($payment->trang_thai_thanh_toan == 'cho_thanh_toan')
                                            <span class="badge bg-warning">Chờ thanh toán</span>
                                        @elseif($payment->trang_thai_thanh_toan == 'that_bai')
                                            <span class="badge bg-danger">Thất bại</span>
                                        @elseif($payment->trang_thai_thanh_toan == 'hoan_tien')
                                            <span class="badge bg-secondary">Hoàn tiền</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $payment->trang_thai_thanh_toan }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->hinh_anh)
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#imageModal{{ $payment->id }}">
                                                <i class="fas fa-image me-1"></i>Xem ảnh
                                            </button>
                                        @else
                                            <span class="text-muted">Chưa có</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>Chi tiết
                                        </a>
                                    </td>
                                </tr>

                                <!-- Modal xem ảnh -->
                                @if($payment->hinh_anh)
                                <div class="modal fade" id="imageModal{{ $payment->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Ảnh chứng từ thanh toán</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset('storage/' . $payment->hinh_anh) }}" class="img-fluid" alt="Ảnh chứng từ">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Không có dữ liệu</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    <div class="d-flex justify-content-center">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

