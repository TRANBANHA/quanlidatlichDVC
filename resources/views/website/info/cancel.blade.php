@extends('website.components.layout')

@section('title', 'Hủy lịch hẹn')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-times-circle me-2"></i>
                        Hủy lịch hẹn
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>Lưu ý:</strong> Bạn có chắc chắn muốn hủy hồ sơ <strong>{{ $hoSo->ma_ho_so }}</strong>?
                    </div>

                    <div class="mb-4">
                        <h5>Thông tin hồ sơ:</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Mã hồ sơ:</th>
                                <td>{{ $hoSo->ma_ho_so }}</td>
                            </tr>
                            <tr>
                                <th>Dịch vụ:</th>
                                <td>{{ $hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Đơn vị:</th>
                                <td>{{ $hoSo->donVi->ten_don_vi ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Ngày hẹn:</th>
                                <td>{{ $hoSo->ngay_hen ? $hoSo->ngay_hen->format('d/m/Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Giờ hẹn:</th>
                                <td>{{ $hoSo->gio_hen ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>

                    <form method="POST" action="{{ route('website.ho-so.cancel', $hoSo->id) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="ly_do_huy_type" class="form-label">
                                Lý do hủy <span class="text-muted">(không bắt buộc)</span>
                            </label>
                            <select name="ly_do_huy_type" id="ly_do_huy_type" class="form-select">
                                <option value="">-- Chọn lý do hủy (không bắt buộc) --</option>
                                <option value="Thay đổi kế hoạch">Thay đổi kế hoạch</option>
                                <option value="Đã đăng ký ở nơi khác">Đã đăng ký ở nơi khác</option>
                                <option value="Không còn nhu cầu">Không còn nhu cầu</option>
                                <option value="Lý do cá nhân">Lý do cá nhân</option>
                                <option value="khac">Khác (vui lòng nhập lý do)</option>
                            </select>
                            @error('ly_do_huy_type')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="ly_do_huy_khac_container" style="display: none;">
                            <label for="ly_do_huy_khac" class="form-label">
                                Nhập lý do hủy
                            </label>
                            <textarea name="ly_do_huy_khac" id="ly_do_huy_khac" class="form-control" rows="4"
                                placeholder="Nhập lý do bạn muốn hủy lịch hẹn..."></textarea>
                            <small class="text-muted">Tối đa 255 ký tự</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/info?action=tab2" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Quay lại
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times-circle me-1"></i>
                                Xác nhận hủy
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('ly_do_huy_type');
    const container = document.getElementById('ly_do_huy_khac_container');
    const textarea = document.getElementById('ly_do_huy_khac');
    
    select.addEventListener('change', function() {
        if (this.value === 'khac') {
            container.style.display = 'block';
            textarea.setAttribute('required', 'required');
        } else {
            container.style.display = 'none';
            textarea.removeAttribute('required');
            textarea.value = '';
        }
    });
    
    // Giới hạn độ dài textarea
    textarea.addEventListener('input', function() {
        if (this.value.length > 255) {
            this.value = this.value.substring(0, 255);
        }
    });
});
</script>
@endsection

