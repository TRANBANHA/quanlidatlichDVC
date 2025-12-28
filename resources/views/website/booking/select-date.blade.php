@extends('website.components.layout')

@section('title', 'Chọn ngày hẹn')

@section('content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5 animate-fade-in-up">
            <h1 class="display-4 fw-bold mb-3 text-gradient">Chọn ngày hẹn</h1>
            <p class="text-muted fs-5 mb-2">
                <i class="fas fa-building text-primary me-2"></i>Phường: <strong>{{ $donVi->ten_don_vi }}</strong> | 
                <i class="fas fa-concierge-bell text-success me-2"></i>Dịch vụ: <strong>{{ $dichVu->ten_dich_vu }}</strong>
            </p>
            <p class="text-muted">Bước 3: Chọn ngày và giờ bạn muốn đến</p>
        </div>

        <!-- Progress Steps -->
        <div class="row justify-content-center mb-5 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="col-md-10">
                <div class="progress-step">
                    <div class="progress-step-item">
                        <div class="progress-step-circle completed bg-gradient-success text-white shadow-beautiful">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="mt-3 mb-0 fw-semibold">Chọn phường</p>
                    </div>
                    <div class="progress-step-line"></div>
                    <div class="progress-step-item">
                        <div class="progress-step-circle completed bg-gradient-success text-white shadow-beautiful">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="mt-3 mb-0 fw-semibold">Chọn dịch vụ</p>
                    </div>
                    <div class="progress-step-line"></div>
                    <div class="progress-step-item">
                        <div class="progress-step-circle active bg-gradient-primary text-white shadow-beautiful">
                            <strong>3</strong>
                        </div>
                        <p class="mt-3 mb-0 fw-semibold">Chọn ngày</p>
                    </div>
                    <div class="progress-step-line"></div>
                    <div class="progress-step-item">
                        <div class="progress-step-circle bg-secondary text-white">
                            <strong>4</strong>
                        </div>
                        <p class="mt-3 mb-0 text-muted">Upload hồ sơ</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10">
                @if(empty($availableDates))
                    <div class="alert alert-warning text-center rounded-beautiful shadow-beautiful py-5 animate-fade-in-up">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3" style="opacity: 0.7;"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Không có ngày khả dụng</h4>
                        <p class="mb-4 fs-5">Hiện tại không có ngày nào còn trống cho dịch vụ này. Vui lòng thử lại sau.</p>
                        <form action="{{ route('booking.select-service') }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="don_vi_id" value="{{ $donVi->id }}">
                            <button type="submit" class="btn btn-primary btn-lg shadow-beautiful">
                                <i class="fas fa-arrow-left me-2"></i>Chọn lại dịch vụ
                            </button>
                        </form>
                    </div>
                @else
                    <form action="{{ route('booking.upload-form') }}" method="POST" id="dateForm">
                        @csrf
                        <input type="hidden" name="don_vi_id" value="{{ $donVi->id }}">
                        <input type="hidden" name="dich_vu_id" value="{{ $dichVu->id }}">
                        
                        <div class="row g-4">
                            @foreach($availableDates as $index => $dateInfo)
                                <div class="col-md-4 col-sm-6 animate-fade-in-up" style="animation-delay: {{ $index * 0.05 }}s;">
                                    <div class="card date-card h-100 shadow-beautiful hover-lift" data-date="{{ $dateInfo['date'] }}">
                                        <div class="card-body text-center p-4">
                                            <div class="mb-3">
                                                <h5 class="card-title fw-bold text-primary mb-2">{{ $dateInfo['day_name'] }}</h5>
                                                <p class="card-text mb-0">
                                                    <strong class="fs-5">{{ $dateInfo['display'] }}</strong>
                                                </p>
                                            </div>
                                            <div class="mb-3 p-3 bg-light rounded-beautiful">
                                                <small class="text-muted d-block mb-1">
                                                    <i class="fas fa-clock text-primary me-1"></i>Giờ làm việc:
                                                </small>
                                                <strong class="text-primary">
                                                    {{ $dateInfo['schedule']->gio_bat_dau }} - {{ $dateInfo['schedule']->gio_ket_thuc }}
                                                </strong>
                                            </div>
                                            <div class="mb-4">
                                                <span class="badge bg-gradient-info text-white rounded-beautiful px-3 py-2" style="font-size: 0.9rem;">
                                                    <i class="fas fa-users me-1"></i>Còn {{ $dateInfo['available_slots'] }} chỗ
                                                </span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm w-100 shadow-beautiful select-date-btn" 
                                                    data-date="{{ $dateInfo['date'] }}"
                                                    data-gio="{{ $dateInfo['schedule']->gio_bat_dau }}">
                                                <i class="fas fa-check me-1"></i>Chọn ngày này
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" name="ngay_hen" id="selected_date">
                        <input type="hidden" name="gio_hen" id="selected_gio">

                        <div class="text-center mt-5 animate-fade-in-up" style="animation-delay: 0.4s;">
                            <form action="{{ route('booking.select-service') }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="don_vi_id" value="{{ $donVi->id }}">
                                <button type="submit" class="btn btn-secondary btn-lg shadow-beautiful">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </button>
                            </form>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.date-card {
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.date-card:hover {
    border-color: #667eea;
    transform: translateY(-8px);
}
.date-card.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateCards = document.querySelectorAll('.date-card');
    const dateForm = document.getElementById('dateForm');
    const selectedDateInput = document.getElementById('selected_date');
    const selectedGioInput = document.getElementById('selected_gio');

    dateCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            dateCards.forEach(c => c.classList.remove('selected'));
            // Add selected class to clicked card
            this.classList.add('selected');
            
            const date = this.dataset.date;
            const btn = this.querySelector('.select-date-btn');
            const gio = btn.dataset.gio;
            
            selectedDateInput.value = date;
            selectedGioInput.value = gio;
            
            // Auto submit form after short delay
            setTimeout(() => {
                dateForm.submit();
            }, 300);
        });
    });
});
</script>
@endsection

