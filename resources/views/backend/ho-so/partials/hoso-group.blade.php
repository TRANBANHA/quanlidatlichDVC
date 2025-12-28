@php
    // Đảm bảo canBoList là collection
    $canBoListCollection = is_array($canBoList) ? collect($canBoList) : $canBoList;
    if (!($canBoListCollection instanceof \Illuminate\Support\Collection)) {
        $canBoListCollection = collect([]);
    }
    
    // Xác định tiêu đề nhóm
    $groupTitle = null;
    if ($groupKey !== null && is_scalar($groupKey)) {
        // Nếu có groupKey, có thể là ID cán bộ hoặc dịch vụ
        if (is_array($canBoMap) && isset($canBoMap[$groupKey]) && is_object($canBoMap[$groupKey])) {
            $groupTitle = 'Cán bộ: ' . ($canBoMap[$groupKey]->ho_ten ?? 'N/A');
        } elseif ($canBoListCollection->isNotEmpty()) {
            // Tìm cán bộ theo ID
            $canBo = $canBoListCollection->firstWhere('id', $groupKey);
            if ($canBo && is_object($canBo)) {
                $groupTitle = 'Cán bộ: ' . ($canBo->ho_ten ?? 'N/A');
            }
        }
    }
    
    // Đảm bảo groupHoSos là collection
    $hoSosToDisplay = is_array($groupHoSos) ? collect($groupHoSos) : $groupHoSos;
    if (!($hoSosToDisplay instanceof \Illuminate\Support\Collection)) {
        $hoSosToDisplay = collect([$hoSosToDisplay]);
    }
@endphp

@if($groupTitle)
    <div class="bg-light p-2 border-bottom">
        <h6 class="mb-0 text-muted small">
            <i class="fas fa-user me-1"></i>{{ $groupTitle }}
            <span class="badge bg-secondary ms-2">{{ $hoSosToDisplay->count() }} hồ sơ</span>
        </h6>
    </div>
@endif

<div class="table-responsive">
    <table class="table table-hover table-striped mb-0">
        <thead class="table-light">
            <tr>
                <th style="width: 120px;">Mã hồ sơ</th>
                <th>Dịch vụ</th>
                <th>Người đăng ký</th>
                <th style="width: 150px;">Ngày hẹn</th>
                <th style="width: 100px;">Giờ hẹn</th>
                <th style="width: 150px;">Trạng thái</th>
                <th style="width: 150px;">Cán bộ xử lý</th>
                <th style="width: 120px;" class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($hoSosToDisplay as $hoSo)
                @php
                    $statusColors = [
                        'Đã tiếp nhận' => 'info',
                        'Đang xử lý' => 'warning',
                        'Cần bổ sung hồ sơ' => 'danger',
                        'Hoàn tất' => 'success',
                        'Đã hủy' => 'secondary',
                    ];
                    $statusColor = $statusColors[$hoSo->trang_thai] ?? 'secondary';
                @endphp
                <tr>
                    <td>
                        <strong class="text-primary">{{ $hoSo->ma_ho_so ?? 'N/A' }}</strong>
                    </td>
                    <td>
                        {{ $hoSo->dichVu->ten_dich_vu ?? 'N/A' }}
                    </td>
                    <td>
                        {{ $hoSo->nguoiDung->ten ?? 'N/A' }}
                        @if($hoSo->nguoiDung && $hoSo->nguoiDung->so_dien_thoai)
                            <br><small class="text-muted">{{ $hoSo->nguoiDung->so_dien_thoai }}</small>
                        @endif
                    </td>
                    <td>
                        @if($hoSo->ngay_hen)
                            {{ \Carbon\Carbon::parse($hoSo->ngay_hen)->format('d/m/Y') }}
                        @else
                            <span class="text-muted">Chưa có</span>
                        @endif
                    </td>
                    <td>
                        {{ $hoSo->gio_hen ?? '-' }}
                    </td>
                    <td>
                        <span class="badge bg-{{ $statusColor }}">{{ $hoSo->trang_thai }}</span>
                    </td>
                    <td>
                        @if($hoSo->quanTriVien)
                            <span class="badge bg-info">{{ $hoSo->quanTriVien->ho_ten }}</span>
                        @else
                            <span class="badge bg-secondary">Chưa chỉ định</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @php
                            $daDenNgay = true;
                            if ($hoSo->ngay_hen) {
                                $ngayHen = \Carbon\Carbon::parse($hoSo->ngay_hen)->startOfDay();
                                $ngayHienTai = \Carbon\Carbon::now()->startOfDay();
                                $daDenNgay = $ngayHienTai->gte($ngayHen);
                            }
                        @endphp
                        @if($daDenNgay)
                            <a href="{{ route('admin.ho-so.show', $hoSo->id) }}" 
                               class="btn btn-sm btn-primary" 
                               title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                        @else
                            <button class="btn btn-sm btn-secondary" 
                                    disabled 
                                    title="Chưa đến ngày hẹn: {{ \Carbon\Carbon::parse($hoSo->ngay_hen)->format('d/m/Y') }}">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        Không có hồ sơ nào
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

