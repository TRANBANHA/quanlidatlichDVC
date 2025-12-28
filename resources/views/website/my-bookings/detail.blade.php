@extends('website.components.layout')

@section('title', 'Chi tiết lịch hẹn')

@section('content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="mb-4">
            <a href="{{ route('my-bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>Chi tiết hồ sơ: {{ $hoSo->ma_ho_so }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Thông tin cơ bản -->
                        <h5 class="border-bottom pb-2 mb-3">Thông tin cơ bản</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Dịch vụ:</strong> {{ $hoSo->dichVu->ten_dich_vu }}</p>
                                <p><strong>Phường/Đơn vị:</strong> {{ $hoSo->donVi->ten_don_vi }}</p>
                                <p><strong>Ngày hẹn:</strong> {{ \Carbon\Carbon::parse($hoSo->ngay_hen)->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Giờ hẹn:</strong> {{ $hoSo->gio_hen }}</p>
                                <p><strong>Trạng thái:</strong> 
                                    <span class="badge 
                                        @if($hoSo->trang_thai == 'Hoàn tất') bg-success
                                        @elseif($hoSo->trang_thai == 'Đang xử lý') bg-warning
                                        @elseif($hoSo->trang_thai == 'Cần bổ sung hồ sơ') bg-danger
                                        @elseif($hoSo->trang_thai == 'Đã hủy') bg-secondary
                                        @else bg-info
                                        @endif">
                                        {{ $hoSo->trang_thai }}
                                    </span>
                                </p>
                                @if($hoSo->quanTriVien)
                                    <p><strong>Cán bộ xử lý:</strong> {{ $hoSo->quanTriVien->ho_ten }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Thông tin form động -->
                        @if($hoSo->hoSoFields && $hoSo->hoSoFields->count() > 0)
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Thông tin đã điền</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Trường</th>
                                            <th>Giá trị</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hoSo->hoSoFields as $field)
                                            <tr>
                                                <td><strong>{{ $field->ten_truong }}</strong></td>
                                                <td>
                                                    @if(str_contains($field->gia_tri, 'storage/') || str_contains($field->gia_tri, 'ho-so/'))
                                                        <a href="{{ Storage::url($field->gia_tri) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-download me-1"></i>Tải file
                                                        </a>
                                                    @else
                                                        {{ $field->gia_tri }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <!-- Ghi chú -->
                        @if($hoSo->ghi_chu)
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Ghi chú</h5>
                            <div class="alert alert-info">
                                {!! nl2br(e($hoSo->ghi_chu)) !!}
                            </div>
                        @endif

                        <!-- Lý do hủy -->
                        @if($hoSo->trang_thai == 'Đã hủy' && $hoSo->ly_do_huy)
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Lý do hủy</h5>
                            <div class="alert alert-warning">
                                {{ $hoSo->ly_do_huy }}
                            </div>
                        @endif

                        <!-- Đánh giá -->
                        @if($hoSo->rating)
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Đánh giá của bạn</h5>
                            <div class="alert alert-success">
                                <div class="d-flex align-items-center mb-2">
                                    <strong>Điểm: </strong>
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $hoSo->rating->diem ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-2">({{ $hoSo->rating->diem }}/5)</span>
                                </div>
                                @if($hoSo->rating->binh_luan)
                                    <p class="mb-0"><strong>Bình luận:</strong> {{ $hoSo->rating->binh_luan }}</p>
                                @endif
                                <a href="{{ route('rating.edit', $hoSo->rating->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-edit me-1"></i>Chỉnh sửa đánh giá
                                </a>
                            </div>
                        @elseif($hoSo->isCompleted())
                            <div class="mt-4">
                                <a href="{{ route('rating.create', $hoSo->id) }}" class="btn btn-warning">
                                    <i class="fas fa-star me-2"></i>Đánh giá dịch vụ
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Hành động -->
                <div class="card shadow mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Hành động</h5>
                    </div>
                    <div class="card-body">
                        @if($hoSo->canBeCancelled())
                            <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="fas fa-times me-2"></i>Hủy lịch hẹn
                            </button>
                        @endif
                        <a href="{{ route('tracking.show', $hoSo->ma_ho_so) }}" class="btn btn-primary w-100 mb-2" target="_blank">
                            <i class="fas fa-share me-2"></i>Chia sẻ liên kết
                        </a>
                        <a href="{{ route('my-bookings.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-list me-2"></i>Danh sách lịch hẹn
                        </a>
                    </div>
                </div>

                <!-- Thông tin liên hệ -->
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Liên hệ</h5>
                    </div>
                    <div class="card-body">
                        <p><i class="fas fa-building me-2"></i>{{ $hoSo->donVi->ten_don_vi }}</p>
                        @if($hoSo->quanTriVien)
                            <p><i class="fas fa-user me-2"></i>{{ $hoSo->quanTriVien->ho_ten }}</p>
                            @if($hoSo->quanTriVien->so_dien_thoai)
                                <p><i class="fas fa-phone me-2"></i>{{ $hoSo->quanTriVien->so_dien_thoai }}</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal hủy -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('my-bookings.cancel', $hoSo->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Hủy lịch hẹn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn hủy lịch hẹn <strong>{{ $hoSo->ma_ho_so }}</strong>?</p>
                    <div class="mb-3">
                        <label class="form-label">Lý do hủy <span class="text-danger">*</span></label>
                        <textarea name="ly_do_huy" class="form-control" rows="3" required placeholder="Nhập lý do hủy..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

