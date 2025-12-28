    <div id="chat-circle" class="btn btn-raised">
        <div id="chat-overlay"></div>
        <i class="fab fa-rocketchat"></i>
    </div>

    <!-- Modal chọn phường -->
    <div class="modal fade" id="selectPhuongModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-building me-2"></i>Chọn phường để chat
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Vui lòng chọn phường/đơn vị và nhân viên bạn muốn liên hệ để được hỗ trợ tốt nhất.</p>
                    <div class="mb-3">
                        <label class="form-label">Chọn phường/đơn vị <span class="text-danger">*</span></label>
                        <select id="phuong-select" class="form-select form-select-lg" required>
                            <option value="">-- Chọn phường --</option>
                            @php
                                $donVis = \App\Models\DonVi::orderBy('ten_don_vi')->get();
                            @endphp
                            @foreach($donVis as $donVi)
                                <option value="{{ $donVi->id }}">{{ $donVi->ten_don_vi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="nhan-vien-container" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Chọn nhân viên (tùy chọn)</label>
                            <div id="nhan-vien-loading" class="text-center mb-2" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                                <span class="ms-2 text-muted">Đang tải danh sách nhân viên...</span>
                            </div>
                            <select id="nhan-vien-select" class="form-select form-select-lg">
                                <option value="">-- Chọn nhân viên (hoặc để trống để AI chat) --</option>
                            </select>
                            <small class="form-text text-muted">Nếu không chọn nhân viên, AI chatbot sẽ trả lời bạn.</small>
                        </div>
                    </div>
                    <div id="phuong-loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                        <p class="mt-2 text-muted">Đang tìm cán bộ phù hợp...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="btn-start-chat">
                        <i class="fas fa-comments me-2"></i>Bắt đầu chat
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="chat-box" style="display: none;">
        <div class="chat-box-header">
            <span id="chat-header-title">ChatBot</span>
            <span class="chat-box-toggle"><i class="far fa-window-close"></i></span>
        </div>
        <div class="chat-box-body">
            <div class="chat-box-overlay">
            </div>
            <div class="chat-logs">

            </div><!--chat-log -->
        </div>
        <div class="chat-input">
            <form>
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <meta name="user-name" content="{{ auth()->check() ? (auth()->user()->ho_ten ?? auth()->user()->email) : 'Khách' }}">
                <meta name="user-email" content="{{ auth()->check() ? auth()->user()->email : '' }}">
                <meta name="user-phone" content="{{ auth()->check() ? (auth()->user()->so_dien_thoai ?? '') : '' }}">
                <input type="text" id="chat-input" placeholder="Nhập tin nhắn..." />
                <button type="button" class="chat-submit" id="chat-submit">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
