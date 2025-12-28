<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Cấu hình Lịch Dịch vụ</h3>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                @foreach (['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6'] as $day)
                                    <th>{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach (range(1, 5) as $thu)
                                    @php
                                        $schedule = $lichDichVu->firstWhere('thu_trong_tuan', $thu);
                                    @endphp
                                    <td>
                                        {{-- Dịch vụ --}}
                                        <label class="fw-bold">Dịch vụ:</label>
                                        <select name="dich_vu_id[{{ $thu }}]"
                                            class="form-select form-select-sm">
                                            @foreach ($allServices as $service)
                                                @php
                                                @endphp
                                                <option value="{{ $service->id }}"
                                                    {{ $service->id === $schedule->dich_vu_id ? 'selected' : '' }}>
                                                    {{ $service->ten_dich_vu }}
                                                </option>
                                            @endforeach
                                        </select>

                                        {{-- Giờ bắt đầu --}}
                                        <label>Giờ bắt đầu:</label>
                                        <input type="time" name="gio_bat_dau[{{ $thu }}]"
                                            value="{{ $schedule->gio_bat_dau ?? '' }}" class="form-control mb-2">

                                        {{-- Giờ kết thúc --}}
                                        <label>Giờ kết thúc:</label>
                                        <input type="time" name="gio_ket_thuc[{{ $thu }}]"
                                            value="{{ $schedule->gio_ket_thuc ?? '' }}" class="form-control mb-2">

                                        {{-- Số lượng --}}
                                        <label>Số lượng tối đa:</label>
                                        <input type="number" name="so_luong_toi_da[{{ $thu }}]"
                                            value="{{ $schedule->so_luong_toi_da ?? 10 }}" class="form-control mb-2">

                                        {{-- Trạng thái --}}
                                        <label>Trạng thái:</label>
                                        <select name="trang_thai[{{ $thu }}]" class="form-select mb-2">
                                            <option value="1"
                                                {{ $schedule && $schedule->trang_thai ? 'selected' : '' }}>Hoạt động
                                            </option>
                                            <option value="0"
                                                {{ $schedule && !$schedule->trang_thai ? 'selected' : '' }}>Không hoạt
                                                động</option>
                                        </select>
                                        {{-- File đính kèm --}}
                                        <label>File mẫu:</label>
                                        <input type="file" name="file_dinh_kem[{{ $thu }}]"
                                            value="{{ $schedule->file_dinh_kem ?? '' }}" class="form-control mb-2">
                                        @if ($schedule->file_dinh_kem)
                                            <a  class="d-flex justify-content-start" href="{{ asset('storage/' . $schedule->file_dinh_kem) }}" download>
                                                {{ 'File Mẫu' }}
                                            </a>
                                        @endif
                                        {{-- Ghi chú --}}
                                        <label>Ghi chú:</label>
                                        <textarea name="ghi_chu[{{ $thu }}]" rows="2" class="form-control">{{ $schedule->ghi_chu ?? '' }}</textarea>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">💾 Lưu cấu hình</button>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    td label {
        display: flex;
        justify-content: start;
    }
</style>
