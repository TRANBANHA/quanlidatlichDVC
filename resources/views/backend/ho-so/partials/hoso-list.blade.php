@php
    // Đảm bảo groupedHoSos là collection hoặc array
    $groupedData = is_array($groupedHoSos) ? collect($groupedHoSos) : $groupedHoSos;
    if (!($groupedData instanceof \Illuminate\Support\Collection)) {
        $groupedData = collect(['default' => $groupedData]);
    }
@endphp

@foreach($groupedData as $ngayKey => $ngayGroup)
    @php
        // Xác định ngày hiển thị
        if ($ngayKey === 'khong_co_ngay') {
            $ngayHienThi = 'Chưa có ngày hẹn';
            $isToday = false;
        } else {
            try {
                $ngayCarbon = \Carbon\Carbon::parse($ngayKey);
                $ngayHienThi = $ngayCarbon->format('d/m/Y');
                $isToday = $ngayCarbon->isToday();
                $isTomorrow = $ngayCarbon->isTomorrow();
            } catch (\Exception $e) {
                $ngayHienThi = $ngayKey;
                $isToday = false;
                $isTomorrow = false;
            }
        }
        
        // Tính tổng hồ sơ trong ngày
        $tongHoSoTrongNgay = is_a($ngayGroup, 'Illuminate\Support\Collection') 
            ? $ngayGroup->flatten()->count() 
            : (is_array($ngayGroup) ? collect($ngayGroup)->flatten()->count() : (is_countable($ngayGroup) ? count($ngayGroup) : 1));
    @endphp
    
    <!-- Header ngày -->
    <div class="bg-info text-white p-2 border-bottom">
        <h6 class="mb-0 fw-bold small">
            <i class="fas fa-calendar me-2"></i>
            {{ $ngayHienThi }}
            @if($isToday)
                <span class="badge bg-warning text-dark ms-2">Hôm nay</span>
            @elseif($isTomorrow)
                <span class="badge bg-secondary ms-2">Ngày mai</span>
            @endif
            <span class="badge bg-light text-dark ms-2">{{ $tongHoSoTrongNgay }} hồ sơ</span>
        </h6>
    </div>
    
    <!-- Nếu là Admin phường hoặc Cán bộ, group tiếp theo cán bộ/dịch vụ -->
    @if(($currentUser->isAdminPhuong() || $currentUser->isCanBo()) && is_array($ngayGroup))
        @foreach($ngayGroup as $subGroupKey => $subGroupHoSos)
            @include('backend.ho-so.partials.hoso-group', [
                'groupHoSos' => $subGroupHoSos,
                'groupKey' => $subGroupKey,
                'currentUser' => $currentUser,
                'canBoMap' => $canBoMap ?? [],
                'canBoList' => $canBoList ?? collect()
            ])
        @endforeach
    @else
        <!-- Admin tổng hoặc hiển thị trực tiếp -->
        @include('backend.ho-so.partials.hoso-group', [
            'groupHoSos' => $ngayGroup,
            'groupKey' => null,
            'currentUser' => $currentUser,
            'canBoMap' => $canBoMap ?? [],
            'canBoList' => $canBoList ?? collect()
        ])
    @endif
@endforeach

