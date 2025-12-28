@extends('backend.components.layout')
@section('title', 'Chi Tiết Đánh Giá')

@section('content')
<div class=" py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Chi Tiết Đánh Giá</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Mã hồ sơ:</strong>
                            <p class="mb-0">{{ $rating->hoSo->ma_ho_so ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Dịch vụ:</strong>
                            <p class="mb-0">{{ $rating->hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Người đánh giá:</strong>
                            <p class="mb-0">{{ $rating->nguoiDung->ten ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Ngày đánh giá:</strong>
                            <p class="mb-0">{{ $rating->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <strong>Điểm đánh giá:</strong>
                        <div class="mt-2">
                            <span class="badge bg-{{ $rating->diem >= 4 ? 'success' : ($rating->diem >= 3 ? 'warning' : 'danger') }} fs-6">
                                {{ $rating->diem }}/5 sao
                            </span>
                            <div class="mt-2" style="font-size: 2rem;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $rating->diem ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <strong>Bình luận:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            {{ $rating->binh_luan ?? 'Không có bình luận' }}
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('staff-ratings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

