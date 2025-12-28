@php
    use App\Models\HoSo;
    use Illuminate\Support\Str;
@endphp
<div class="tab-pane fade {{ request()->query('action', 'tab2') == 'tab2' ? 'show active' : '' }}" id="tab2"
    role="tabpanel">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">Hồ sơ cá nhân</h4>
            <p class="text-muted mb-0">Quản lý và theo dõi các hồ sơ đăng ký dịch vụ của bạn</p>
        </div>
    </div>

    <!-- Hiển thị thông báo -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">#</th>
                            <th class="fw-semibold">Mã hồ sơ</th>
                            <th class="fw-semibold">Dịch vụ</th>
                            <th class="fw-semibold">Người dùng</th>
                            <th class="fw-semibold">Số điện thoại</th>
                            <th class="fw-semibold">Đơn vị</th>
                            <th class="fw-semibold">Giờ hẹn</th>
                            <th class="fw-semibold">Ngày hẹn</th>
                            <th class="fw-semibold">Ghi chú</th>
                            <th class="fw-semibold">Trạng thái</th>
                            <th class="fw-semibold">File đính kèm</th>
                            <th class="fw-semibold">Lý do hủy</th>
                            <th class="fw-semibold text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ho_so as $item)
                            @php
                                // dd($item);
                            @endphp
                            <tr class="border-bottom">
                                <td class="text-muted">
                                    {{ $loop->iteration + ($ho_so->currentPage() - 1) * $ho_so->perPage() }}</td>
                                <td><span class="fw-semibold text-primary">{{ $item->ma_ho_so }}</span></td>
                                <td><span
                                        class="badge bg-light text-dark">{{ $item->dichVu->ten_dich_vu ?? '-' }}</span>
                                </td>
                                <td>{{ $item->nguoiDung->ten ?? '-' }}</td>
                                <td>{{ $item->nguoiDung->so_dien_thoai ?? '-' }}</td>
                                <td>{{ $item->donVi->ten_don_vi ?? '-' }}</td>
                                <td>{{ $item->gio_hen }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->ngay_hen)->format('d/m/Y') }}</td>
                                <td><span class="text-muted small">{{ Str::limit($item->ghi_chu, 20) }}</span></td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            HoSo::STATUS_RECEIVED => [
                                                'class' => 'bg-secondary',
                                                'icon' => 'fa-check-circle',
                                                'text' => 'Đã tiếp nhận',
                                            ],
                                            HoSo::STATUS_PROCESSING => [
                                                'class' => 'bg-info text-dark',
                                                'icon' => 'fa-spinner fa-spin',
                                                'text' => 'Đang xử lý',
                                            ],
                                            HoSo::STATUS_NEED_SUPPLEMENT => [
                                                'class' => 'bg-warning text-dark',
                                                'icon' => 'fa-exclamation-triangle',
                                                'text' => 'Cần bổ sung hồ sơ',
                                            ],
                                            HoSo::STATUS_COMPLETED => [
                                                'class' => 'bg-success',
                                                'icon' => 'fa-check-double',
                                                'text' => 'Hoàn tất',
                                            ],
                                            HoSo::STATUS_CANCELLED => [
                                                'class' => 'bg-danger',
                                                'icon' => 'fa-times-circle',
                                                'text' => 'Đã hủy',
                                            ],
                                        ];
                                        // So sánh chính xác với trim để tránh lỗi khoảng trắng
                                        $trangThai = trim($item->trang_thai);
                                        $config = $statusConfig[$trangThai] ?? [
                                            'class' => 'bg-secondary',
                                            'icon' => 'fa-circle',
                                            'text' => $trangThai,
                                        ];
                                    @endphp
                                    <span class="badge rounded-pill px-3 py-2 {{ $config['class'] }}">
                                        {{ $config['text'] }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        // Lấy tất cả file từ hoSoFields
                                        $fileFields = $item->hoSoFields->filter(function ($field) {
                                            return str_contains($field->gia_tri ?? '', 'ho-so/') ||
                                                str_contains($field->gia_tri ?? '', 'storage/');
                                        });
                                    @endphp
                                    @if ($fileFields->count() > 0)
                                        <div class="d-flex gap-1 flex-wrap">
                                            @foreach ($fileFields as $fileField)
                                                <a href="{{ asset('storage/' . $fileField->gia_tri) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1"
                                                    style="font-size: 0.75rem; min-width: auto;">
                                                    <i class="fas fa-download me-1" style="font-size: 0.7rem;"></i>{{ $loop->iteration }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 0.75rem;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->trang_thai === HoSo::STATUS_CANCELLED)
                                        <span
                                            class="text-muted small">{{ Str::limit($item->ly_do_huy ?? 'Người dùng đã hủy lịch', 30) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                                        <!-- Nút gửi thông tin -->
                                        <button type="button"
                                            class="btn btn-info btn-sm rounded-pill btn-send-info {{ $item->trang_thai == HoSo::STATUS_NEED_SUPPLEMENT ? '' : 'd-none' }}"
                                            data-bs-toggle="modal" data-bs-target="#sendInfoModal"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->nguoiDung->ten ?? 'Người dùng' }}"
                                            data-phone="{{ $item->nguoiDung->so_dien_thoai ?? 'Không có' }}"
                                            data-missing="{{ $item->file_path ? '' : 'Thiếu file đính kèm' }}">
                                            Yêu cầu
                                        </button>

                                        <!-- Nút đánh giá và bình luận -->
                                        @if ($item->trang_thai == HoSo::STATUS_COMPLETED && !$item->rating)
                                            <a href="{{ route('rating.create', $item->id) }}"
                                                class="btn btn-warning btn-sm rounded-pill">
                                                <i class="fas fa-star me-1"></i>Đánh giá
                                            </a>
                                        @endif

                                        <!-- Hiển thị đánh giá nếu đã có -->
                                        @if ($item->rating)
                                            <button type="button" class="btn btn-success btn-sm rounded-pill"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewRatingModal{{ $item->id }}">
                                                Xem
                                            </button>
                                        @endif

                                        @if ($item->canBeEdited())
                                            <a href="{{ route('ho-so.edit', $item->id) }}"
                                                class="btn btn-primary btn-sm rounded-pill">
                                                Chỉnh sửa
                                            </a>
                                        @endif

                                        @if ($item->canBeCancelled())
                                            <a href="{{ route('website.ho-so.cancel.form', $item->id) }}"
                                                class="btn btn-outline-danger btn-sm rounded-pill">
                                                Hủy
                                            </a>
                                        @endif

                                        <!-- Nút thanh toán -->
                                        @php
                                            $servicePhuong = $item->dichVu->getServiceForPhuong($item->don_vi_id);
                                            $phiDichVu = $servicePhuong ? $servicePhuong->phi_dich_vu : 0;
                                            $payment = \App\Models\Payment::where('ho_so_id', $item->id)
                                                ->where('nguoi_dung_id', Auth::guard('web')->id())
                                                ->first();
                                        @endphp
                                        @if($phiDichVu > 0)
                                            @if($payment && $payment->trang_thai_thanh_toan == 'da_thanh_toan')
                                                <span class="badge bg-success">Đã thanh toán</span>
                                            @else
                                                <a href="{{ route('payment.create', $item->id) }}"
                                                    class="btn btn-success btn-sm rounded-pill">
                                                    <i class="fas fa-credit-card me-1"></i>Thanh toán
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>


                            </tr>


                        @empty
                            <tr>
                                <td colspan="13" class="text-center py-5">
                                    <div class="text-muted">
                                        <p class="mb-0">Không có hồ sơ nào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($ho_so->hasPages())
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-center">
                    {{ $ho_so->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal xem đánh giá (đặt ngoài table để tránh bị ảnh hưởng bởi CSS table) -->
@foreach ($ho_so as $item)
    @if ($item->rating)
        <div class="modal fade" id="viewRatingModal{{ $item->id }}" tabindex="-1"
            aria-labelledby="viewRatingModalLabel{{ $item->id }}" aria-hidden="true"
            style="z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
                <div class="modal-content" style="pointer-events: auto;">
                    <div class="modal-header py-2">
                        <h5 class="modal-title fs-6" id="viewRatingModalLabel{{ $item->id }}">Đánh giá của bạn</h5>
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"
                            aria-label="Close" style="font-size: 0.75rem;"></button>
                    </div>
                    <div class="modal-body py-3">
                        <div class="mb-2">
                            <strong class="small">Điểm đánh giá tổng thể:</strong>
                            <div class="d-inline-block ms-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        class="fas fa-star {{ $i <= $item->rating->diem ? 'text-warning' : 'text-muted' }}" style="font-size: 0.9rem;"></i>
                                @endfor
                                <span class="ms-2 small">{{ $item->rating->diem }}/5</span>
                            </div>
                        </div>

                        @if (
                            $item->rating->diem_thai_do ||
                                $item->rating->diem_thoi_gian ||
                                $item->rating->diem_chat_luong ||
                                $item->rating->diem_co_so_vat_chat)
                            <hr class="my-2">
                            <h6 class="mb-2 small">Đánh giá chi tiết:</h6>

                            @if ($item->rating->diem_thai_do)
                                <div class="mb-1">
                                    <strong class="small">Thái độ phục vụ:</strong>
                                    <span class="ms-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $item->rating->diem_thai_do ? 'text-warning' : 'text-muted' }}"
                                                style="font-size: 0.8rem;"></i>
                                        @endfor
                                        <span class="small">({{ $item->rating->diem_thai_do }}/5)</span>
                                    </span>
                                </div>
                            @endif

                            @if ($item->rating->diem_thoi_gian)
                                <div class="mb-1">
                                    <strong class="small">Thời gian xử lý:</strong>
                                    <span class="ms-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $item->rating->diem_thoi_gian ? 'text-warning' : 'text-muted' }}"
                                                style="font-size: 0.8rem;"></i>
                                        @endfor
                                        <span class="small">({{ $item->rating->diem_thoi_gian }}/5)</span>
                                    </span>
                                </div>
                            @endif

                            @if ($item->rating->diem_chat_luong)
                                <div class="mb-1">
                                    <strong class="small">Chất lượng dịch vụ:</strong>
                                    <span class="ms-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $item->rating->diem_chat_luong ? 'text-warning' : 'text-muted' }}"
                                                style="font-size: 0.8rem;"></i>
                                        @endfor
                                        <span class="small">({{ $item->rating->diem_chat_luong }}/5)</span>
                                    </span>
                                </div>
                            @endif

                            @if ($item->rating->diem_co_so_vat_chat)
                                <div class="mb-1">
                                    <strong class="small">Cơ sở vật chất:</strong>
                                    <span class="ms-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $item->rating->diem_co_so_vat_chat ? 'text-warning' : 'text-muted' }}"
                                                style="font-size: 0.8rem;"></i>
                                        @endfor
                                        <span class="small">({{ $item->rating->diem_co_so_vat_chat }}/5)</span>
                                    </span>
                                </div>
                            @endif

                            <hr class="my-2">
                        @endif

                        @if ($item->rating->co_nen_gioi_thieu !== null)
                            <div class="mb-2">
                                <strong class="small">Giới thiệu cho người khác:</strong>
                                <span
                                    class="badge badge-sm {{ $item->rating->co_nen_gioi_thieu ? 'bg-success' : 'bg-secondary' }} ms-2" style="font-size: 0.7rem;">
                                    {{ $item->rating->co_nen_gioi_thieu ? 'Có' : 'Không' }}
                                </span>
                            </div>
                        @endif

                        @if ($item->rating->binh_luan)
                            <div class="mb-2">
                                <strong class="small">Bình luận:</strong>
                                <p class="mt-1 mb-0 small">{{ $item->rating->binh_luan }}</p>
                            </div>
                        @endif

                        @if ($item->rating->y_kien_khac)
                            <div class="mb-2">
                                <strong class="small">Ý kiến khác:</strong>
                                <p class="mt-1 mb-0 small">{{ $item->rating->y_kien_khac }}</p>
                            </div>
                        @endif

                        <div class="text-muted" style="font-size: 0.7rem;">
                            <i class="far fa-clock me-1"></i>Đánh giá vào:
                            {{ \Carbon\Carbon::parse($item->rating->created_at)->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="modal-footer py-2" style="pointer-events: auto;">
                        <button type="button" class="btn btn-secondary btn-sm"
                            data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<!-- Modal Gửi thông tin (đặt ngoài vòng lặp để tránh duplicate ID) -->
<div class="modal fade" id="sendInfoModal" tabindex="-1" aria-labelledby="sendInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="sendInfoForm" method="POST" action="{{ route('file.sendInfo') }}">
            @csrf
            <input type="hidden" name="ho_so_id" id="modalHoSoId">
            <input type="hidden" name="nguoi_dung_id" id="modalNguoiDungId">
            <input type="hidden" name="dich_vu_id" id="modalDichVuId">
            <input type="hidden" name="ngay_hen" id="modalNgayHen">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendInfoModalLabel">Gửi thông tin nhắc nhở</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Người dùng:</strong> <span id="modalUserName"></span></p>
                    <p><strong>Số điện thoại:</strong> <span id="modalUserPhone"></span></p>

                    <div class="mb-3">
                        <label for="modalMessage" class="form-label">Nội dung gửi:</label>
                        <textarea name="message" id="modalMessage" class="form-control" rows="4"
                            placeholder="Nhập nội dung cần gửi cho người dùng..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Gửi</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý modal gửi thông tin
        const sendInfoModal = document.getElementById('sendInfoModal');
        if (sendInfoModal) {
            sendInfoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const hoSoId = button.getAttribute('data-id');
                const userName = button.getAttribute('data-name');
                const userPhone = button.getAttribute('data-phone');

                // Lấy thông tin từ button data attributes
                document.getElementById('modalHoSoId').value = hoSoId || '';
                document.getElementById('modalUserName').textContent = userName || 'Người dùng';
                document.getElementById('modalUserPhone').textContent = userPhone || 'Không có';

                // Reset form
                document.getElementById('modalMessage').value = '';
            });
        }
    });
</script>

<style>
    /* Ngăn modal bị ảnh hưởng bởi hover effects của table */
    #tab2 .modal {
        z-index: 1055 !important;
        position: fixed !important;
    }

    #tab2 .modal-backdrop {
        z-index: 1050 !important;
    }

    #tab2 .modal .modal-dialog {
        transform: none !important;
        transition: none !important;
    }

    #tab2 .modal .modal-content {
        pointer-events: auto !important;
        transform: none !important;
        transition: none !important;
        will-change: auto !important;
    }

    #tab2 .modal .modal-content *,
    #tab2 .modal .modal-content *::before,
    #tab2 .modal .modal-content *::after {
        pointer-events: auto !important;
        transform: none !important;
        transition: none !important;
        will-change: auto !important;
    }

    /* Đảm bảo modal không bị ảnh hưởng bởi table hover */
    #tab2 .modal .table-hover tbody tr:hover {
        transform: none !important;
        background-color: transparent !important;
    }

    /* Ngăn các icon trong modal bị hover effect */
    #tab2 .modal .fas.fa-star,
    #tab2 .modal .far.fa-star,
    #tab2 .modal .fa-star {
        pointer-events: none !important;
        transform: none !important;
        transition: none !important;
        will-change: auto !important;
    }

    #tab2 .modal .badge,
    #tab2 .modal .badge * {
        transform: none !important;
        transition: none !important;
        will-change: auto !important;
    }

    #tab2 .modal .badge:hover,
    #tab2 .modal .badge:hover * {
        transform: none !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }

    /* Ngăn tất cả elements trong modal bị transform khi hover */
    #tab2 .modal *:hover,
    #tab2 .modal *:hover *,
    #tab2 .modal *:focus,
    #tab2 .modal *:focus * {
        transform: none !important;
        transition: none !important;
    }

    /* Đảm bảo modal body và footer không bị ảnh hưởng */
    #tab2 .modal .modal-body,
    #tab2 .modal .modal-header,
    #tab2 .modal .modal-footer {
        transform: none !important;
        transition: none !important;
    }

    #tab2 .modal .modal-body *,
    #tab2 .modal .modal-header *,
    #tab2 .modal .modal-footer * {
        transform: none !important;
        transition: none !important;
    }
</style>
