<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Đăng Ký Tạm Trú</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li>Đăng Ký Tạm Trú</li>
            </ul>
        </div>

        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col">
                <form action="{{ route('temp-residence.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="Tìm kiếm theo họ tên. v.v." value="{{ request('search') }}">
                        <select name="approval_status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ request('approval_status') == '1' ? 'selected' : '' }}>Đã phê
                                duyệt</option>
                            <option value="0" {{ request('approval_status') == '0' ? 'selected' : '' }}>Chưa phê
                                duyệt</option>
                            <option value="2" {{ request('approval_status') == '2' ? 'selected' : '' }}>Bị từ
                                chối
                            </option>
                        </select>
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        {{-- <a href="{{ route('temp-residence.create') }}" class="btn btn-primary">Tạo mới</a> --}}
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ngày đăng ký</th>
                            <th>Họ và tên</th>
                            <th>Số CCCD</th>
                            <th>Địa chỉ</th>
                            <th>Ngày bắt đàu</th>
                            <th>Ngày kết thúc</th>
                            {{-- <th>Ngày sinh</th> --}}
                            <th>Giới tính</th>
                            <th>Trạng thái phê duyệt</th>
                            <th>Trạng thái thanh toán</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tempResidences as $registration)
                            <tr>
                                <td>{{ $registration->id }}</td>
                                <td>{{ $registration->registration_date }}</td>
                                <td>{{ $registration->full_name }}</td>
                                <td>{{ $registration->cccd }}</td>
                                <td>{{ $registration->address }}</td>
                                <td>{{ $registration->start_date }}</td>
                                <td>{{ $registration->end_date }}</td>
                                {{-- <td>{{ $registration->registration_date }}</td> --}}
                                <td>{{ $registration->gender == 'A' ? 'Chưa xác định' : ($registration->gender == 'N' ? 'Nam' : 'Nữ') }}
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $registration->approval_status == 1 ? 'bg-success' : ($registration->approval_status == 0 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $registration->approval_status == 1 ? 'Đã phê duyệt' : ($registration->approval_status == 0 ? 'Chưa phê duyệt' : 'Bị từ chối') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($registration->payment()->where('form_type', 'tempResidenceRegistrations')->where('record_id', $registration->id)->first())
                                        <span
                                            class="badge {{ $registration->payment()->where('form_type', 'tempResidenceRegistrations')->where('record_id', $registration->id)->first()->payment_status == 1? 'bg-success': ($registration->payment->payment_status == 0? 'bg-warning': 'bg-danger') }}">
                                            {{ $registration->payment()->where('form_type', 'tempResidenceRegistrations')->where('record_id', $registration->id)->first()->payment_status == 1? 'Đã thanh toán': 'Chưa thanh toán' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Chưa có thanh
                                            toán</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- View Details Button -->
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-{{ $registration->id }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <!-- Edit Button -->
                                    {{-- <a href="{{ route('temp-residence.edit', $registration->id) }}"
                                        class="btn btn-warning btn-sm">
                                        Sửa
                                    </a> --}}

                                    <!-- Modal for Detailed View -->
                                    <div class="modal fade" id="modal-{{ $registration->id }}" tabindex="-1"
                                        aria-labelledby="modalLabel-{{ $registration->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content shadow-lg border-0 rounded">
                                                <!-- Header -->
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title fw-bold"
                                                        id="modalLabel-{{ $registration->id }}">
                                                        <i class="fas fa-address-card me-2"></i> Chi Tiết Đăng Ký Tạm
                                                        Trú
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                <!-- Body -->
                                                <div class="modal-body px-4 py-3">
                                                    <!-- Quốc hiệu -->
                                                    <div class="text-center mb-4">
                                                        <h4 class="fw-bold text-uppercase mb-1">Cộng Hòa Xã Hội Chủ
                                                            Nghĩa Việt Nam</h4>
                                                        <h5 class="text-muted fst-italic">Độc lập - Tự do - Hạnh phúc
                                                        </h5>
                                                        <hr class="w-50 mx-auto">
                                                    </div>

                                                    <!-- Thông tin bảng -->
                                                    <table class="table table-striped table-hover align-middle">
                                                        <tbody>
                                                            <tr>
                                                                <th class="text-end bg-light w-30">Ngày đăng ký:</th>
                                                                <td>{{ $registration->registration_date }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-end bg-light">Họ và tên:</th>
                                                                <td>{{ $registration->full_name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-end bg-light">Số CCCD:</th>
                                                                <td>{{ $registration->cccd }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-end bg-light">Địa chỉ:</th>
                                                                <td>{{ $registration->address }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-end bg-light">Ngày sinh:</th>
                                                                <td>{{ $registration->birth_date }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-end bg-light">Giới tính:</th>
                                                                <td>{{ $registration->gender == 'A' ? 'Chưa xác định' : ($registration->gender == 'N' ? 'Nam' : 'Nữ') }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-end bg-light">Trạng thái phê duyệt:</th>
                                                                <td>
                                                                    <span
                                                                        class="badge {{ $registration->approval_status == 1 ? 'bg-success' : ($registration->approval_status == 0 ? 'bg-warning' : 'bg-danger') }}">
                                                                        {{ $registration->approval_status == 1 ? 'Đã phê duyệt' : ($registration->approval_status == 0 ? 'Chưa phê duyệt' : 'Bị từ chối') }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-end bg-light"
                                                                    style="vertical-align: middle;">Tình trạng thanh
                                                                    toán:</th>
                                                                <td>
                                                                    @if ($registration->payment()->where('form_type', 'tempResidenceRegistrations')->where('record_id', $registration->id)->first())
                                                                        <span
                                                                            class="badge {{ $registration->payment()->where('form_type', 'tempResidenceRegistrations')->where('record_id', $registration->id)->first()->payment_status == 1? 'bg-success': ($registration->payment->payment_status == 0? 'bg-warning': 'bg-danger') }}">
                                                                            {{ $registration->payment()->where('form_type', 'tempResidenceRegistrations')->where('record_id', $registration->id)->first()->payment_status == 1? 'Đã thanh toán': 'Chưa thanh toán' }}
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Chưa có thanh
                                                                            toán</span>
                                                                    @endif
                                                                </td>

                                                            </tr>
                                                            @if ($registration->payment()->where('form_type', 'tempResidenceRegistrations')->where('record_id', $registration->id)->first())
                                                                @if ($registration->payment()->where('form_type', 'tempResidenceRegistrations')->where('record_id', $registration->id)->first()->payment_status == 1)
                                                                    <tr>
                                                                        <th class="text-end bg-light"
                                                                            style="vertical-align: middle;">Hình ảnh
                                                                            chứng cứ:</th>
                                                                        <td>
                                                                            <div class="mt-3 text-center">
                                                                                @if ($registration->payment()->where('form_type', 'tempResidenceRegistrations')->where('record_id', $registration->id)->first()->image)
                                                                                    <img src="{{ asset('storage/' .$registration->payment()->where('form_type', 'tempResidenceRegistrations')->where('record_id', $registration->id)->first()->image) }}"
                                                                                        alt="Hình ảnh thanh toán"
                                                                                        class="img-thumbnail"
                                                                                        style="max-width: 300px; max-height: 200px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;">
                                                                                @else
                                                                                    <p class="text-muted">Không có hình
                                                                                        ảnh.</p>
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endif
                                                            <tr id="reject-reason-row-{{ $registration->id }}"
                                                                style="display: none;">
                                                                <th class="text-end bg-light">
                                                                    <label
                                                                        for="reject-reason-{{ $registration->id }}">Lý
                                                                        do từ chối:</label>
                                                                </th>
                                                                <td>
                                                                    <textarea id="reject-reason-{{ $registration->id }}" class="form-control" rows="3"></textarea>
                                                                    <button type="button" class="btn btn-primary mt-2"
                                                                        data-status="2"
                                                                        data-id="{{ $registration->id }}"
                                                                        id="submit-reject-btn-{{ $registration->id }}">Gửi
                                                                        lý do</button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <!-- Footer -->
                                                <div class="modal-footer d-flex justify-content-end">
                                                    @if (
                                                        $registration->approval_status == 0 &&
                                                            $registration->payment()->where('form_type', 'tempResidenceRegistrations')->where('record_id', $registration->id)->first() &&
                                                            $registration->payment()->where('form_type', 'tempResidenceRegistrations')->where('record_id', $registration->id)->first()->payment_status == 1)
                                                        <button type="button" class="btn btn-success me-2"
                                                            id="approve-btn" data-status="1"
                                                            data-id="{{ $registration->id }}" data-bs-dismiss="modal">
                                                            <i class="fas fa-check-circle"></i> Đã phê duyệt
                                                        </button>
                                                        <button type="button" class="btn btn-danger me-2"
                                                            id="reject-btn" data-status="2"
                                                            data-id="{{ $registration->id }}">
                                                            <i class="fas fa-times-circle"></i> Bị từ chối
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        Đóng
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                    <!-- Delete Button -->
                                    <form action="{{ route('temp-residence.destroy', $registration->id) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this user?');"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i
                                                class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $tempResidences->links() }}
        </div>
    </div>
</div>

@section('scripts')
    <script>
        $(document).ready(function() {
            // Khi nút "Đã phê duyệt" được nhấn
            $('#approve-btn').on('click', function() {
                var registrationId = $(this).data('id');
                var status = $(this).data('status');

                // Gửi yêu cầu AJAX
                $.ajax({
                    url: '/admin/temp-residence/update-status', // Địa chỉ route của bạn
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Thêm CSRF token cho bảo mật
                        registration_id: registrationId,
                        status: status
                    },
                    success: function(response) {
                        // Xử lý thành công, ví dụ thông báo người dùng
                        alert('Cập nhật trạng thái thành công!');
                        // Bạn có thể tự động cập nhật lại dữ liệu hoặc đóng modal tùy theo yêu cầu
                        window.location
                            .reload(); // Reload lại trang hoặc bạn có thể dùng jQuery để cập nhật giao diện
                    },
                    error: function(xhr, status, error) {
                        // Xử lý lỗi
                        alert('Đã có lỗi xảy ra, vui lòng thử lại!');
                    }
                });
            });
            $('[id^=reject-btn]').on('click', function() {
                var registrationId = $(this).data('id');
                console.log(registrationId);

                $('#reject-reason-row-' + registrationId).show(); // Hiển thị ô nhập lý do
            });
            // Khi nút "Bị từ chối" được nhấn
            $('[id^=submit-reject-btn]').on('click', function() {
                var registrationId = $(this).data('id');
                var status = $(this).data('status');
                var reason = $('#reject-reason-' + registrationId).val();

                // Gửi yêu cầu AJAX
                $.ajax({
                    url: '/admin/temp-residence/update-status',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        registration_id: registrationId,
                        status: status,
                        reason: reason
                    },
                    success: function(response) {
                        // Xử lý thành công

                        window.location
                            .reload();
                        $.notify({
                            message: "Cập nhật trạng thái thành công!"
                        }, {
                            type: 'success',
                            allow_dismiss: true,
                            delay: 5000,
                            placement: {
                                from: "top",
                                align: "right"
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        // Xử lý lỗi
                        alert('Đã có lỗi xảy ra, vui lòng thử lại!');
                    }
                });
            });
        });
    </script>
@endsection
