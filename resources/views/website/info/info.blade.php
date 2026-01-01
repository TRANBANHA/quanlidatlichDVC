@extends('website.components.layout')
@section('title')
    Chào mừng bạn đến với
@endsection

@section('content')
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row g-4">
                <!-- Tab Dọc -->
                <div class="col-lg-2">
                    <div class="list-group">
                        <!-- Tab Item -->
                        <a href="javascript:void(0)" onclick="window.location.href='/info?action=tab1'"
                            class="list-group-item list-group-item-action  {{ request()->query('action', 'tab1') == 'tab1' ? 'active' : '' }} py-3 px-4 mb-2 shadow-sm rounded"
                            role="tab">
                            <i class="bi bi-house-door-fill me-2"></i>Thông tin tài khoản
                        </a>
                        <a href="javascript:void(0)" onclick="window.location.href='/info?action=tab2'"
                            class="list-group-item list-group-item-action {{ request()->query('action', 'tab2') == 'tab2' ? 'active' : '' }} py-3 px-4 mb-2 shadow-sm rounded"
                            role="tab">
                            <i class="bi bi-gear-fill me-2"></i>Hồ sơ cá nhân
                        </a>
                        <a href="javascript:void(0)" onclick="window.location.href='/info?action=tab3'"
                            class="list-group-item list-group-item-action py-3 {{ request()->query('action', 'tab3') == 'tab3' ? 'active' : '' }} px-4 mb-2 shadow-sm rounded"
                            role="tab">
                            <i class="bi bi-info-circle-fill me-2"></i>Đổi mật khẩu
                        </a>
                        <a href="javascript:void(0)" onclick="window.location.href='/info?action=tab4'"
                            class="list-group-item list-group-item-action py-3 {{ request()->query('action', 'tab4') == 'tab4' ? 'active' : '' }} px-4 mb-2 shadow-sm rounded"
                            role="tab">
                            <i class="bi bi-bell-fill me-2"></i>Thông báo
                        </a>
                        
                    </div>

                </div>

                <!-- Nội dung của tab -->
                <div class="col-lg-10">
                    <div class="tab-content">
                        <!-- Tab 1 - Thông tin tài khoản -->
                        <div class="tab-pane fade {{ request()->query('action', 'tab1') == 'tab1' ? 'show active' : '' }}"
                            id="tab1" role="tabpanel">
                            <form action="{{ route('profile.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="ten" class="form-label">Tên</label>
                                    <input type="text" id="ten" name="ten" class="form-control"
                                        value="{{ old('ten', $user->ten) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" disabled
                                        value="{{ old('email', $user->email) }}" required>
                                </div>

                                {{-- <div class="mb-3">
                                    <label for="mat_khau" class="form-label">Mật khẩu</label>
                                    <input type="password" id="mat_khau" name="mat_khau" class="form-control"
                                        placeholder="Để trống nếu không muốn đổi mật khẩu">
                                </div> --}}

                                <div class="mb-3">
                                    <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                                    <input type="text" id="so_dien_thoai" name="so_dien_thoai" class="form-control"
                                        value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="cccd" class="form-label">Số CCCD</label>
                                    <input type="text" id="cccd" name="cccd" class="form-control"
                                        value="{{ old('cccd', $user->cccd) }}" required maxlength="12">
                                    @error('cccd')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="don_vi_id" class="form-label">Phường <span class="text-danger">*</span></label>
                                    <select name="don_vi_id" id="don_vi_id" class="form-select" required>
                                        <option value="">-- Chọn phường --</option>
                                        @foreach ($donVis as $donVi)
                                            <option value="{{ $donVi->id }}"
                                                {{ $donVi->id == $user->don_vi_id ? 'selected' : '' }}>
                                                {{ $donVi->ten_don_vi }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('don_vi_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="dia_chi" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                                    <input type="text" id="dia_chi" name="dia_chi" class="form-control"
                                        value="{{ old('dia_chi', $user->dia_chi) }}" 
                                        placeholder="Ví dụ: 123 Đường Nguyễn Văn Linh, Phường Hòa Cường Bắc" required>
                                    <small class="form-text text-muted">Nhập địa chỉ chi tiết (số nhà, tên đường, phường/xã)</small>
                                    @error('dia_chi')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary" style="color: white;">Cập nhật</button>
                            </form>
                        </div>


                        <!-- Tab 2 - Thông tin người thân -->
                        @include('website.info.tab2')

                        <!-- Tab 3 - Đổi mật khẩu -->
                        <div class="tab-pane fade {{ request()->query('action', 'tab3') == 'tab3' ? 'show active' : '' }}"
                            id="tab3" role="tabpanel">
                            <h4 class="mb-3">Đổi mật khẩu</h4>

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <form method="POST" action="{{ route('profile.change-password') }}">
                                @csrf

                                {{-- Mật khẩu cũ --}}
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mật khẩu cũ</label>
                                    <input type="password" id="current_password" name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mật khẩu mới --}}
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" id="new_password" name="new_password"
                                        class="form-control @error('new_password') is-invalid @enderror" required>
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Xác nhận mật khẩu mới --}}
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu
                                        mới</label>
                                    <input type="password" id="new_password_confirmation"
                                        name="new_password_confirmation"
                                        class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                        required>
                                    @error('new_password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary" style="color: white">Lưu thay đổi</button>
                            </form>
                        </div>


                        <!-- Tab 4 - Thông báo -->
                        <div class="tab-pane fade {{ request()->query('action', 'tab4') == 'tab4' ? 'show active' : '' }}"
                            id="tab4" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0">Thông báo</h4>
                                @if(isset($unreadCount) && $unreadCount > 0)
                                    <span class="badge bg-danger">{{ $unreadCount }} chưa đọc</span>
                                @endif
                            </div>
                            
                            @if(isset($thongBaos) && $thongBaos->count() > 0)
                                <div class="mb-3">
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-check-double me-1"></i>Đánh dấu tất cả đã đọc
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="list-group">
                                    @foreach($thongBaos as $thongBao)
                                        <div class="list-group-item {{ !$thongBao->is_read ? 'list-group-item-warning' : '' }} mb-2 rounded shadow-sm" 
                                             data-notification-id="{{ $thongBao->id }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-2">
                                                        @if(!$thongBao->is_read)
                                                            <span class="badge bg-danger me-2">Mới</span>
                                                        @endif
                                                        <h6 class="mb-0">{{ $thongBao->hoSo->ma_ho_so ?? 'N/A' }}</h6>
                                                    </div>
                                                    <p class="mb-2">{{ $thongBao->message }}</p>
                                                    <div class="d-flex flex-wrap gap-3 text-muted small">
                                                        @if($thongBao->dichVu)
                                                            <span><i class="fas fa-tag me-1"></i>{{ $thongBao->dichVu->ten_dich_vu }}</span>
                                                        @endif
                                                        @if($thongBao->ngay_hen)
                                                            <span><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($thongBao->ngay_hen)->format('d/m/Y') }}</span>
                                                        @endif
                                                        <span><i class="fas fa-clock me-1"></i>{{ $thongBao->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                                <div class="ms-3">
                                                    @if(!$thongBao->is_read)
                                                        <form action="{{ route('notifications.read', $thongBao->id) }}" method="POST" class="d-inline mark-read-form" data-notification-id="{{ $thongBao->id }}" onsubmit="return false;">
                                                            @csrf
                                                            <button type="button" class="btn btn-sm btn-outline-success mark-read-btn" title="Đánh dấu đã đọc" data-notification-id="{{ $thongBao->id }}">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('notifications.destroy', $thongBao->id) }}" method="POST" class="d-inline delete-notification-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa thông báo này?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-4">
                                    {{ $thongBaos->appends(request()->query())->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Chưa có thông báo nào.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



<!-- Thêm một số phần CSS để làm đẹp -->
@push('styles')
    <style>
        .list-group-item-action {
            transition: all 0.3s ease;
        }

        .list-group-item-action:hover {
            background-color: #f0f0f0;
            border-color: #ddd;
        }

        .list-group-item-action.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            border: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .list-group-item-action i {
            font-size: 1.2rem;
        }

        .tab-content {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            min-height: 500px;
        }

        .tab-content h4 {
            color: #333;
        }

        /* Cải thiện bảng */
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }
        
        /* Ngăn modal bị ảnh hưởng bởi hover effects */
        .modal {
            pointer-events: auto;
        }
        
        .modal * {
            pointer-events: auto;
        }
        
        .modal .table-hover tbody tr:hover {
            transform: none;
            background-color: transparent;
        }

        .table th {
            border-top: none;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
        }

        .card {
            border-radius: 12px;
            overflow: hidden;
        }

        .badge {
            font-weight: 500;
            font-size: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .badge:hover {
            transform: translateY(-1px);
        }
</style>

@push('scripts')
<script>
    (function() {
        function initMarkRead() {
            // Xử lý đánh dấu đã đọc
            const markReadButtons = document.querySelectorAll('.mark-read-btn');
            console.log('Found mark-read buttons:', markReadButtons.length);
            
            markReadButtons.forEach(function(button) {
                // Remove existing listeners to avoid duplicates
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                newButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    const notificationId = this.dataset.notificationId;
                    const form = this.closest('.mark-read-form');
                    
                    if (!form) {
                        console.error('Form not found');
                        return false;
                    }
                    
                    const notificationItem = document.querySelector('[data-notification-id="' + notificationId + '"]');
                    
                    // Disable button trong khi xử lý
                    this.disabled = true;
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    
                    const formData = new FormData(form);
                    const csrfToken = form.querySelector('input[name="_token"]');
                    
                    if (!csrfToken) {
                        console.error('CSRF token not found');
                        this.disabled = false;
                        this.innerHTML = originalHTML;
                        return false;
                    }
                    
                    const button = this; // Lưu reference để dùng trong catch
                    let requestCompleted = false;
                    
                    // Timeout để tránh spinner xoay mãi
                    const timeoutId = setTimeout(function() {
                        if (!requestCompleted) {
                            console.error('Request timeout');
                            requestCompleted = true;
                            button.disabled = false;
                            button.innerHTML = originalHTML;
                            alert('Request timeout. Vui lòng thử lại.');
                        }
                    }, 10000); // 10 giây timeout
                    
                    // Hàm để dừng spinner và khôi phục button
                    const resetButton = function() {
                        if (!requestCompleted) {
                            requestCompleted = true;
                            clearTimeout(timeoutId);
                            button.disabled = false;
                            button.innerHTML = originalHTML;
                        }
                    };
                    
                    // Hàm để xử lý thành công
                    const handleSuccess = function() {
                        if (!requestCompleted) {
                            requestCompleted = true;
                            clearTimeout(timeoutId);
                            
                            // Ẩn form đánh dấu đã đọc
                            form.style.display = 'none';
                            
                            // Xóa badge "Mới"
                            if (notificationItem) {
                                const badge = notificationItem.querySelector('.badge.bg-danger');
                                if (badge && badge.textContent.trim() === 'Mới') {
                                    badge.remove();
                                }
                                
                                // Xóa class warning
                                notificationItem.classList.remove('list-group-item-warning');
                            }
                            
                            // Cập nhật số thông báo chưa đọc ở header
                            const unreadBadge = document.querySelector('h4 + .badge.bg-danger');
                            if (unreadBadge) {
                                const countText = unreadBadge.textContent.trim();
                                const match = countText.match(/(\d+)/);
                                if (match) {
                                    const currentCount = parseInt(match[1]);
                                    if (currentCount > 1) {
                                        unreadBadge.textContent = (currentCount - 1) + ' chưa đọc';
                                    } else {
                                        unreadBadge.remove();
                                    }
                                }
                            }
                        }
                    };
                    
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.value,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers.get('content-type'));
                        
                        // Kiểm tra content-type
                        const contentType = response.headers.get('content-type') || '';
                        
                        // Nếu status OK (200-299), coi như thành công
                        if (response.ok) {
                            // Thử parse JSON
                            if (contentType.includes('application/json')) {
                                return response.json().then(data => {
                                    console.log('Response data:', data);
                                    handleSuccess();
                                    return data;
                                }).catch(err => {
                                    console.warn('JSON parse error, but status OK:', err);
                                    // Nếu không parse được JSON nhưng status OK, vẫn coi như thành công
                                    handleSuccess();
                                    return { success: true };
                                });
                            } else {
                                // Response không phải JSON nhưng status OK
                                console.warn('Response is not JSON but status OK');
                                handleSuccess();
                                return { success: true };
                            }
                        } else {
                            // Status không OK
                            return response.text().then(text => {
                                console.error('Response error:', text.substring(0, 200));
                                throw new Error('Network response was not ok: ' + response.status);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        resetButton();
                        // Chỉ hiển thị alert nếu form chưa ẩn (chưa thành công)
                        if (form.style.display !== 'none') {
                            alert('Có lỗi xảy ra khi đánh dấu đã đọc: ' + (error.message || 'Unknown error'));
                        }
                    });
                    
                    return false;
                });
            });
        }
        
        // Đảm bảo chạy sau khi DOM đã load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMarkRead);
        } else {
            // DOM đã load, chạy ngay
            setTimeout(initMarkRead, 100);
        }
    })();
</script>
@endpush
