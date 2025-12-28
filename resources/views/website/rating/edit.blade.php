@extends('website.components.layout')

@section('title', 'Chỉnh sửa đánh giá')

@section('content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa đánh giá
                        </h4>
                    </div>
                    <div class="card-body p-5">
                        <form action="{{ route('rating.update', $rating->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <label class="form-label h5">Đánh giá của bạn <span class="text-danger">*</span></label>
                                <div class="rating-stars text-center" style="font-size: 3rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star star-rating {{ $i <= $rating->diem ? 'active' : '' }}" 
                                           data-rating="{{ $i }}" 
                                           style="cursor: pointer; color: {{ $i <= $rating->diem ? '#ffc107' : '#ddd' }};"></i>
                                    @endfor
                                </div>
                                <input type="hidden" name="diem" id="rating_value" value="{{ $rating->diem }}" required>
                                <div class="text-center mt-2">
                                    <span id="rating_text" class="text-muted"></span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label h5">Bình luận (tùy chọn)</label>
                                <textarea name="binh_luan" class="form-control" rows="5" placeholder="Chia sẻ trải nghiệm của bạn về dịch vụ...">{{ $rating->binh_luan }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('my-bookings.show', $rating->ho_so_id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </a>
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fas fa-save me-2"></i>Cập nhật đánh giá
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Similar script as create.blade.php
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-rating');
    const ratingInput = document.getElementById('rating_value');
    const ratingText = document.getElementById('rating_text');
    
    const ratingTexts = {
        1: 'Rất không hài lòng',
        2: 'Không hài lòng',
        3: 'Bình thường',
        4: 'Hài lòng',
        5: 'Rất hài lòng'
    };

    ratingText.textContent = ratingTexts[ratingInput.value];

    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            ratingText.textContent = ratingTexts[rating];
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                    s.style.color = '#ffc107';
                } else {
                    s.classList.remove('active');
                    s.style.color = '#ddd';
                }
            });
        });
    });
});
</script>
@endsection

