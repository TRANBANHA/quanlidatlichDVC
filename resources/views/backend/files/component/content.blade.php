@php
    use App\Models\HoSo;
@endphp
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Quản lý Hồ sơ</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li>Danh sách Hồ sơ</li>
            </ul>
        </div>

        <!-- Tab dịch vụ -->
        @if(($currentUser->isAdminPhuong() ?? false || $currentUser->isCanBo() ?? false) && isset($services) && $services->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Chọn dịch vụ</h5>
            </div>
            <div class="card-body">
                @php
                    $currentDichVuId = request('dich_vu_id');
                @endphp
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('file.index', array_merge(request()->except('dich_vu_id'), ['dich_vu_id' => ''])) }}" 
                       class="btn btn-{{ !$currentDichVuId ? 'primary' : 'outline-primary' }}">
                        <i class="fas fa-list me-1"></i>Tất cả dịch vụ
                        <span class="badge bg-{{ !$currentDichVuId ? 'light text-dark' : 'primary' }} ms-2">{{ $serviceCounts['all'] ?? $ho_so->total() }}</span>
                    </a>
                    @foreach($services as $service)
                        @php
                            $count = $serviceCounts[$service->id] ?? 0;
                            $isActive = $currentDichVuId == $service->id;
                        @endphp
                        <a href="{{ route('file.index', array_merge(request()->except('dich_vu_id'), ['dich_vu_id' => $service->id])) }}" 
                           class="btn btn-{{ $isActive ? 'primary' : 'outline-primary' }}">
                            <i class="fas fa-briefcase me-1"></i>{{ $service->ten_dich_vu }}
                            <span class="badge bg-{{ $isActive ? 'light text-dark' : 'primary' }} ms-2">{{ $count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col">
                <form action="{{ route('file.index') }}" method="GET" class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Tìm kiếm</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo ghi chú..."
                                value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('file.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                    <!-- Giữ lại dich_vu_id trong form nếu đã chọn -->
                    @if(request('dich_vu_id'))
                        <input type="hidden" name="dich_vu_id" value="{{ request('dich_vu_id') }}">
                    @endif
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mã hồ sơ</th>
                            <th>Dịch vụ</th>
                            <th>Người dùng</th>
                            <th>Số điện thoại</th>
                            <th>Đơn vị</th>
                            <th>Giờ hẹn</th>
                            <th>Ngày hẹn</th>
                            <th>Ghi chú</th>
                            <th>Trạng thái</th>
                            <th>File đính kèm</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ho_so as $item)
                            @php
                                // dd($item);
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration + ($ho_so->currentPage() - 1) * $ho_so->perPage() }}</td>
                                <td>{{ $item->ma_ho_so }}</td>
                                <td>{{ $item->dichVu->ten_dich_vu ?? '-' }}</td>
                                <td>{{ $item->nguoiDung->ten ?? '-' }}</td>
                                <td>{{ $item->nguoiDung->so_dien_thoai ?? '-' }}</td>
                                <td>{{ $item->donVi->ten_don_vi ?? '-' }}</td>
                                <td>{{ $item->gio_hen }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->ngay_hen)->format('d/m/Y') }}</td>
                                <td>{{ $item->ghi_chu }}</td>
                                <td>
                                    <span class="badge 
                                        @class([
                                            'bg-secondary' => $item->trang_thai === HoSo::STATUS_RECEIVED,
                                            'bg-info text-dark' => $item->trang_thai === HoSo::STATUS_PROCESSING,
                                            'bg-warning text-dark' => $item->trang_thai === HoSo::STATUS_NEED_SUPPLEMENT,
                                            'bg-success' => $item->trang_thai === HoSo::STATUS_COMPLETED,
                                            'bg-danger' => $item->trang_thai === HoSo::STATUS_CANCELLED,
                                        ])
                                    ">{{ $item->trang_thai }}</span>
                                </td>
                                <td>
                                    @if ($item->file_path)
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> Tải file
                                        </a>
                                    @else
                                        <span class="text-muted">Không có file</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if ($item->trang_thai !== HoSo::STATUS_COMPLETED && $item->trang_thai !== HoSo::STATUS_CANCELLED)
                                            <button type="button" class="btn btn-warning btn-sm btn-send-info"
                                                data-bs-toggle="modal" data-bs-target="#sendInfoModal"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->nguoiDung->ten ?? 'Người dùng' }}"
                                                data-phone="{{ $item->nguoiDung->so_dien_thoai ?? 'Không có' }}"
                                                data-missing="{{ $item->file_path ? '' : 'Thiếu file đính kèm' }}">
                                                <i class="fa-solid fa-paper-plane"></i> Yêu cầu bổ sung
                                            </button>
                                        @endif
                                        <form action="{{ route('file.sendInfo.success', $item->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"
                                                {{ $item->trang_thai == HoSo::STATUS_COMPLETED ? 'disabled' : '' }}
                                                onclick="return confirm('Bạn có chắc muốn đánh dấu hồ sơ đã hoàn tất không?');">
                                                <i class="fa-solid fa-check"></i> Hoàn tất
                                            </button>
                                        </form>
                                    </div>
                                </td>


                            </tr>
                            <div class="modal fade" id="sendInfoModal" tabindex="-1"
                                aria-labelledby="sendInfoModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form id="sendInfoForm" method="POST" action="{{ route('file.sendInfo') }}">
                                        @csrf
                                        <input type="hidden" name="ho_so_id" id="hoSoId"
                                            value="{{ $item->id }}">
                                        <input type="hidden" name="nguoi_dung_id" id="hoSoId"
                                            value="{{ $item->nguoiDung->id }}">
                                        <input type="hidden" name="dich_vu_id" id="hoSoId"
                                            value="{{ $item->dich_vu_id }}">
                                        <input type="hidden" name="ngay_hen" id="hoSoId"
                                            value="{{ $item->ngay_hen }}">

                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="sendInfoModalLabel">Gửi thông tin nhắc nhở
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Người dùng:</strong> <span
                                                        id="userName">{{ $item->nguoiDung->ten }}</span></p>
                                                <p><strong>Số điện thoại:</strong> <span
                                                        id="userPhone">{{ $item->nguoiDung->so_dien_thoai }}</span></p>

                                                <div class="mb-3">
                                                    <label for="message" class="form-label">Nội dung gửi:</label>
                                                <textarea name="message" id="message" class="form-control" rows="4"
                                                        placeholder="Nhập nội dung cần gửi cho người dùng..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" class="btn btn-primary">Gửi thông tin</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">Không có hồ sơ nào.</td>
                            </tr>
                            <!-- Modal Gửi thông tin -->
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $ho_so->links() }}
        </div>
    </div>
</div>
