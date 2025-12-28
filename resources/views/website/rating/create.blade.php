@extends('website.components.layout')

@section('title', 'Đánh giá dịch vụ')

@section('content')
<div class="container-fluid py-5 bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-primary text-white py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-star me-2"></i>Đánh giá dịch vụ
                        </h3>
                    </div>
                    <div class="card-body p-5">
                        <!-- Thông tin hồ sơ -->
                        <div class="alert alert-info mb-4 border-0 shadow-sm">
                            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Thông tin hồ sơ</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-2"><strong>Mã hồ sơ:</strong><br>{{ $hoSo->ma_ho_so }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-2"><strong>Dịch vụ:</strong><br>{{ $hoSo->dichVu->ten_dich_vu }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-2"><strong>Phường:</strong><br>{{ $hoSo->donVi->ten_don_vi }}</p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('rating.store', $hoSo->id) }}" method="POST" id="ratingForm">
                            @csrf
                            
                            <!-- Đánh giá tổng thể -->
                            <div class="mb-5">
                                <label class="form-label h5 mb-3">
                                    <i class="fas fa-star text-warning me-2"></i>Đánh giá tổng thể <span class="text-danger">*</span>
                                </label>
                                <div class="rating-section">
                                    <div class="rating-stars text-center mb-2" style="font-size: 2.5rem;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star star-rating-main" data-rating="{{ $i }}" style="cursor: pointer; color: #ddd; margin: 0 5px;"></i>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="diem" id="rating_value" value="5" required>
                                    <div class="text-center">
                                        <span id="rating_text" class="badge bg-warning text-dark px-3 py-2">Rất hài lòng</span>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-5">

                            <!-- Đánh giá chi tiết -->
                            <h5 class="mb-4"><i class="fas fa-clipboard-list me-2"></i>Đánh giá chi tiết</h5>
                            
                            <!-- Thái độ phục vụ -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-smile text-primary me-2"></i>Thái độ phục vụ
                                </label>
                                <div class="rating-stars-small text-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star star-rating-item" data-field="thai_do" data-rating="{{ $i }}" style="cursor: pointer; color: #ddd; font-size: 1.5rem; margin: 0 3px;"></i>
                                    @endfor
                                </div>
                                <input type="hidden" name="diem_thai_do" id="rating_thai_do" value="5">
                            </div>

                            <!-- Thời gian xử lý -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-clock text-success me-2"></i>Thời gian xử lý
                                </label>
                                <div class="rating-stars-small text-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star star-rating-item" data-field="thoi_gian" data-rating="{{ $i }}" style="cursor: pointer; color: #ddd; font-size: 1.5rem; margin: 0 3px;"></i>
                                    @endfor
                                </div>
                                <input type="hidden" name="diem_thoi_gian" id="rating_thoi_gian" value="5">
                            </div>

                            <!-- Chất lượng dịch vụ -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-award text-info me-2"></i>Chất lượng dịch vụ
                                </label>
                                <div class="rating-stars-small text-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star star-rating-item" data-field="chat_luong" data-rating="{{ $i }}" style="cursor: pointer; color: #ddd; font-size: 1.5rem; margin: 0 3px;"></i>
                                    @endfor
                                </div>
                                <input type="hidden" name="diem_chat_luong" id="rating_chat_luong" value="5">
                            </div>

                            <!-- Cơ sở vật chất -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-building text-danger me-2"></i>Cơ sở vật chất
                                </label>
                                <div class="rating-stars-small text-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star star-rating-item" data-field="co_so_vat_chat" data-rating="{{ $i }}" style="cursor: pointer; color: #ddd; font-size: 1.5rem; margin: 0 3px;"></i>
                                    @endfor
                                </div>
                                <input type="hidden" name="diem_co_so_vat_chat" id="rating_co_so_vat_chat" value="5">
                            </div>

                            <hr class="my-5">

                            <!-- Câu hỏi khác -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-question-circle text-warning me-2"></i>Bạn có muốn giới thiệu dịch vụ này cho người khác không?
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="co_nen_gioi_thieu" id="gioi_thieu_yes" value="1" checked>
                                    <label class="form-check-label" for="gioi_thieu_yes">Có</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="co_nen_gioi_thieu" id="gioi_thieu_no" value="0">
                                    <label class="form-check-label" for="gioi_thieu_no">Không</label>
                                </div>
                            </div>

                            <!-- Bình luận -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-comment-alt me-2"></i>Bình luận (tùy chọn)
                                </label>
                                <textarea name="binh_luan" class="form-control" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về dịch vụ..."></textarea>
                                <small class="form-text text-muted">Tối đa 1000 ký tự</small>
                            </div>

                            <!-- Ý kiến khác -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-lightbulb me-2"></i>Ý kiến khác (tùy chọn)
                                </label>
                                <textarea name="y_kien_khac" class="form-control" rows="3" placeholder="Bạn có ý kiến gì khác muốn chia sẻ?"></textarea>
                            </div>

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mt-5">
                                <a href="{{ route('info.index', ['action' => 'tab2']) }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </a>
                                <button type="submit" class="btn btn-warning btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi đánh giá
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.star-rating-main:hover,
.star-rating-main.active {
    color: #ffc107 !important;
    transform: scale(1.1);
    transition: all 0.2s;
}

.star-rating-item:hover,
.star-rating-item.active {
    color: #ffc107 !important;
    transform: scale(1.1);
    transition: all 0.2s;
}

.rating-section {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 10px;
}

.rating-stars-small {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingTexts = {
        1: 'Rất không hài lòng',
        2: 'Không hài lòng',
        3: 'Bình thường',
        4: 'Hài lòng',
        5: 'Rất hài lòng'
    };

    // Xử lý đánh giá tổng thể
    const mainStars = document.querySelectorAll('.star-rating-main');
    const ratingInput = document.getElementById('rating_value');
    const ratingText = document.getElementById('rating_text');

    mainStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            ratingText.textContent = ratingTexts[rating];
            
            mainStars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });

        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            mainStars.forEach((s, index) => {
                if (index < rating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            const currentRating = parseInt(ratingInput.value);
            mainStars.forEach((s, index) => {
                if (index < currentRating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });

    // Set initial rating
    mainStars.forEach((s, index) => {
        if (index < 5) {
            s.classList.add('active');
        }
    });

    // Xử lý đánh giá chi tiết
    const itemStars = document.querySelectorAll('.star-rating-item');
    itemStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            const field = this.dataset.field;
            const inputId = `rating_${field}`;
            document.getElementById(inputId).value = rating;
            
            // Update stars trong cùng nhóm
            const groupStars = document.querySelectorAll(`[data-field="${field}"]`);
            groupStars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });

        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            const field = this.dataset.field;
            const groupStars = document.querySelectorAll(`[data-field="${field}"]`);
            groupStars.forEach((s, index) => {
                if (index < rating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            const field = this.dataset.field;
            const currentRating = parseInt(document.getElementById(`rating_${field}`).value);
            const groupStars = document.querySelectorAll(`[data-field="${field}"]`);
            groupStars.forEach((s, index) => {
                if (index < currentRating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });

    // Set initial ratings cho các item
    ['thai_do', 'thoi_gian', 'chat_luong', 'co_so_vat_chat'].forEach(field => {
        const groupStars = document.querySelectorAll(`[data-field="${field}"]`);
        groupStars.forEach((s, index) => {
            if (index < 5) {
                s.classList.add('active');
            }
        });
    });
});
</script>
@endsection
