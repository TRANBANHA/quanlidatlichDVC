@extends('backend.components.layout')
@section('title', 'Chi tiết hồ sơ')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Chi tiết hồ sơ: {{ $hoSo->ma_ho_so }}</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="/admin"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li><a href="{{ route('admin.ho-so.index') }}">Quản lý hồ sơ</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li>Chi tiết</li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Thông tin hồ sơ -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Thông tin hồ sơ</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Mã hồ sơ:</strong>
                                <p class="text-primary fs-5">{{ $hoSo->ma_ho_so }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Trạng thái:</strong>
                                @php
                                    $statusColors = [
                                        'Đã tiếp nhận' => 'info',
                                        'Đang xử lý' => 'warning',
                                        'Cần bổ sung hồ sơ' => 'danger',
                                        'Hoàn tất' => 'success',
                                        'Đã hủy' => 'secondary',
                                    ];
                                    $color = $statusColors[$hoSo->trang_thai] ?? 'secondary';
                                @endphp
                                <p><span class="badge bg-{{ $color }} fs-6">{{ $hoSo->trang_thai }}</span></p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Dịch vụ:</strong>
                                <p>{{ $hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Phường:</strong>
                                <p>{{ $hoSo->donVi->ten_don_vi ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Ngày hẹn:</strong>
                                <p>{{ \Carbon\Carbon::parse($hoSo->ngay_hen)->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Giờ hẹn:</strong>
                                <p>{{ $hoSo->gio_hen }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Cán bộ xử lý:</strong>
                                <p>
                                    @if($hoSo->quanTriVien)
                                        <span class="badge bg-info">{{ $hoSo->quanTriVien->ho_ten }}</span>
                                    @else
                                        <span class="badge bg-secondary">Chưa phân công</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>Ngày tạo:</strong>
                                <p>{{ $hoSo->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if($hoSo->ghi_chu)
                            <div class="mb-3">
                                <strong>Ghi chú xử lý:</strong>
                                <div class="border rounded p-3 bg-light">
                                    {!! nl2br(e($hoSo->ghi_chu)) !!}
                                </div>
                            </div>
                        @endif

                        @if($hoSo->trang_thai == 'Đã hủy' && $hoSo->ly_do_huy)
                            <div class="alert alert-danger">
                                <strong>Lý do hủy:</strong><br>
                                {{ $hoSo->ly_do_huy }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Thông tin người dân -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Thông tin người dân</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Họ tên:</strong>
                                <p>{{ $hoSo->nguoiDung->ten ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>CCCD:</strong>
                                <p>{{ $hoSo->nguoiDung->cccd ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Email:</strong>
                                <p>{{ $hoSo->nguoiDung->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Số điện thoại:</strong>
                                <p>{{ $hoSo->nguoiDung->so_dien_thoai ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hồ sơ đính kèm -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Hồ sơ đính kèm</h4>
                    </div>
                    <div class="card-body">
                        @if($hoSo->hoSoFields->isEmpty())
                            <p class="text-muted">Chưa có hồ sơ đính kèm</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tên trường</th>
                                            <th>Giá trị</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hoSo->hoSoFields as $field)
                                            @php
                                                // Lấy tên hiển thị từ ServiceField nếu có
                                                $serviceField = \App\Models\ServiceField::where('dich_vu_id', $hoSo->dich_vu_id)
                                                    ->where('ten_truong', $field->ten_truong)
                                                    ->first();
                                                $fieldLabel = $serviceField ? $serviceField->nhan_hien_thi : $field->ten_truong;
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $fieldLabel }}</strong></td>
                                                <td>
                                                    @if(str_contains($field->gia_tri ?? '', 'storage/') || str_contains($field->gia_tri ?? '', 'ho-so/'))
                                                        <a href="{{ asset('storage/' . $field->gia_tri) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-download me-1"></i>Tải file
                                                        </a>
                                                    @else
                                                        {{ $field->gia_tri ?? 'N/A' }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar: Cập nhật trạng thái (CHỈ CÁN BỘ) -->
            <div class="col-md-4">
                @if(Auth::guard('admin')->user()->isCanBo())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Cập nhật trạng thái</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.ho-so.update-status', $hoSo->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="trang_thai" class="form-select" required>
                                        <option value="Đã tiếp nhận" {{ $hoSo->trang_thai == 'Đã tiếp nhận' ? 'selected' : '' }}>Đã tiếp nhận</option>
                                        <option value="Đang xử lý" {{ $hoSo->trang_thai == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
                                        <option value="Cần bổ sung hồ sơ" {{ $hoSo->trang_thai == 'Cần bổ sung hồ sơ' ? 'selected' : '' }}>Cần bổ sung hồ sơ</option>
                                        <option value="Hoàn tất" {{ $hoSo->trang_thai == 'Hoàn tất' ? 'selected' : '' }}>Hoàn tất</option>
                                        <option value="Đã hủy" {{ $hoSo->trang_thai == 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ghi chú xử lý</label>
                                    <textarea name="ghi_chu_xu_ly" class="form-control" rows="3" placeholder="Nhập ghi chú..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-2"></i>Cập nhật
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Admin tổng và Admin phường: Chỉ xem thông tin trạng thái -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Thông tin trạng thái</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Trạng thái hiện tại:</label>
                                @php
                                    $statusColors = [
                                        'Đã tiếp nhận' => 'info',
                                        'Đang xử lý' => 'warning',
                                        'Cần bổ sung hồ sơ' => 'danger',
                                        'Hoàn tất' => 'success',
                                        'Đã hủy' => 'secondary',
                                    ];
                                    $color = $statusColors[$hoSo->trang_thai] ?? 'secondary';
                                @endphp
                                <p class="mb-0">
                                    <span class="badge bg-{{ $color }} fs-6">{{ $hoSo->trang_thai }}</span>
                                </p>
                            </div>
                            @if($hoSo->ghi_chu)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ghi chú:</label>
                                    <div class="border rounded p-3 bg-light">
                                        {!! nl2br(e($hoSo->ghi_chu)) !!}
                                    </div>
                                </div>
                            @endif
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Chỉ cán bộ được phân công mới có quyền cập nhật trạng thái hồ sơ.</small>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Phân công cán bộ (Admin tổng và Admin phường) -->
                @if(Auth::guard('admin')->user()->isAdmin() || Auth::guard('admin')->user()->isAdminPhuong())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Phân công cán bộ</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.ho-so.assign', $hoSo->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Cán bộ xử lý</label>
                                    <select name="quan_tri_vien_id" class="form-select">
                                        @php
                                            // Lấy thứ trong tuần của ngày hẹn (1 = Thứ 2, 7 = Chủ nhật)
                                            $ngayHen = \Carbon\Carbon::parse($hoSo->ngay_hen);
                                            $thuTrongTuan = $ngayHen->dayOfWeek; // 0 = Chủ nhật, 1 = Thứ 2, ..., 6 = Thứ 7
                                            // Chuyển đổi: 0 (Chủ nhật) -> 7, 1-6 giữ nguyên
                                            $thuTrongTuan = $thuTrongTuan == 0 ? 7 : $thuTrongTuan;
                                            
                                            // Map thứ trong tuần sang tên
                                            $tenThu = [
                                                1 => 'Thứ 2',
                                                2 => 'Thứ 3',
                                                3 => 'Thứ 4',
                                                4 => 'Thứ 5',
                                                5 => 'Thứ 6',
                                                6 => 'Thứ 7',
                                                7 => 'Chủ nhật'
                                            ];
                                            
                                            // Tìm schedule của dịch vụ vào thứ đó
                                            $schedule = \App\Models\ServiceSchedule::where('dich_vu_id', $hoSo->dich_vu_id)
                                                ->where('thu_trong_tuan', $thuTrongTuan)
                                                ->where('trang_thai', true)
                                                ->first();
                                            
                                            $canBos = collect();
                                            $donViId = Auth::guard('admin')->user()->isAdmin() ? $hoSo->don_vi_id : Auth::guard('admin')->user()->don_vi_id;
                                            
                                            if ($schedule) {
                                                // Lấy các cán bộ đã được phân công vào schedule này
                                                $canBoIds = \App\Models\ServiceScheduleStaff::where('schedule_id', $schedule->id)
                                                    ->pluck('can_bo_id')
                                                    ->toArray();
                                                
                                                if (!empty($canBoIds)) {
                                                    $canBos = \App\Models\Admin::where('don_vi_id', $donViId)
                                                        ->where('quyen', 0)
                                                        ->whereIn('id', $canBoIds)
                                                        ->orderBy('ho_ten')
                                                        ->get();
                                                }
                                            }
                                            
                                            // CHỈ lấy cán bộ từ schedule, không thêm cán bộ đã được phân công trước đó nếu không có trong schedule
                                        @endphp
                                        @if($canBos->isEmpty())
                                            <option value="" disabled selected>Chưa phân công cán bộ</option>
                                        @else
                                            <option value="">-- Chọn cán bộ --</option>
                                            @foreach($canBos as $canBo)
                                                <option value="{{ $canBo->id }}" {{ $hoSo->quan_tri_vien_id == $canBo->id ? 'selected' : '' }}>
                                                    {{ $canBo->ho_ten }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if($canBos->isEmpty())
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Vui lòng phân công cán bộ vào lịch dịch vụ trước.
                                        </small>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-info w-100" {{ $canBos->isEmpty() ? 'disabled' : '' }}>
                                    <i class="fas fa-user-tie me-2"></i>Phân công
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
