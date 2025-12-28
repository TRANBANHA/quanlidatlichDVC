@extends('backend.components.layout')
@section('title', 'Báo Cáo Tổng Hợp - Admin Tổng')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="mb-0 text-white"><i class="fas fa-chart-line me-2 text-white"></i>Báo Cáo Tổng Hợp - Admin Tổng</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="tu_ngay" class="form-control" value="{{ $tuNgay }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="den_ngay" class="form-control" value="{{ $denNgay }}" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-2"></i>Lọc
                            </button>
                            <a href="{{ route('reports.export-excel', request()->all()) }}" class="btn btn-success">
                                <i class="fas fa-file-excel me-2"></i>Xuất Excel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class=" mb-1">Tổng số hồ sơ</h6>
                            <h3 class="mb-0">{{ number_format($tongHoSo) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class=" mb-1">Tổng số phường</h6>
                            <h3 class="mb-0">{{ $donVis->count() }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class=" mb-1">Tổng số dịch vụ</h6>
                            <h3 class="mb-0">{{ $hoSoTheoDichVu->count() }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-concierge-bell fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class=" mb-1">Tổng số cán bộ</h6>
                            <h3 class="mb-0">{{ $soNguoiLamTheoPhuong->sum('so_luong') }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng các phường -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Báo Cáo Theo Phường</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Phường/Đơn vị</th>
                                    <th>Số hồ sơ</th>
                                    <th>Tổng phí (VNĐ)</th>
                                    <th>Số cán bộ</th>
                                    <th>Số dịch vụ</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hoSoTheoPhuong as $index => $item)
                                @php
                                    $tongTienItem = $tongTienTheoPhuong->where('don_vi_id', $item->don_vi_id)->first();
                                    $tongTien = $tongTienItem ? $tongTienItem->tong_tien : 0;
                                    $soCanBoItem = $soNguoiLamTheoPhuong->where('don_vi_id', $item->don_vi_id)->first();
                                    $soCanBo = $soCanBoItem ? $soCanBoItem->so_luong : 0;
                                    $soDichVu = $chiTietDichVuTheoPhuong->where('don_vi_id', $item->don_vi_id)->count();
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->donVi->ten_don_vi ?? 'N/A' }}</strong>
                                    </td>
                                    <td><span class="badge bg-primary">{{ number_format($item->so_luong) }}</span></td>
                                    <td><span class="badge bg-success">{{ number_format($tongTien) }}</span></td>
                                    <td><span class="badge bg-info">{{ $soCanBo }}</span></td>
                                    <td><span class="badge bg-warning">{{ $soDichVu }}</span></td>
                                    <td>
                                        <a href="{{ route('reports.phuong.detail', ['donViId' => $item->don_vi_id, 'tu_ngay' => $tuNgay, 'den_ngay' => $denNgay]) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i>Xem chi tiết
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
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết dịch vụ theo phường -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Chi Tiết Dịch Vụ Theo Phường</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Phường</th>
                                    <th>Dịch vụ</th>
                                    <th>Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($chiTietDichVuTheoPhuong as $item)
                                <tr>
                                    <td>{{ $item->donVi->ten_don_vi ?? 'N/A' }}</td>
                                    <td>{{ $item->dichVu->ten_dich_vu ?? 'N/A' }}</td>
                                    <td><span class="badge bg-primary">{{ number_format($item->so_luong) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tổng phí theo phường -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Tổng Phí Theo Phường</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Phường/Đơn vị</th>
                                    <th>Tổng phí (VNĐ)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tongTienTheoPhuong as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->donVi->ten_don_vi ?? 'N/A' }}</strong></td>
                                    <td><span class="badge bg-success fs-6">{{ number_format($item->tong_tien ?? 0) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

