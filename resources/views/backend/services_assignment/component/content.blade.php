<div class="container">
    <div class="page-inner">
        <div class="page-header d-flex flex-column justify-content-start">
            <h3 class="fw-bold mb-3">Cấu hình Lịch Dịch vụ</h3>
            <p>Cấu hình dịch vụ được tạo ra sau 2 tuần sau kể từ tuần hiện tại.</p>
        </div>


        <form action="{{ route('service-assignment.store') }}" method="POST">
            @csrf

            <div class="row">
                @foreach ($dates as $item)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header text-center fw-bold">
                                THỨ {{ $item['thu'] + 1 }} <br>
                                ({{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }})
                            </div>
                            <div class="card-body">
                                {{-- Dịch vụ --}}
                                <select name="assignments[{{ $item['date'] }}][ma_dich_vu]" class="form-select" required
                                    style="pointer-events: none; background-color: #eee;">
                                    <option value="">-- Chọn dịch vụ --</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ isSelectedService($serviceSchedule, $service->id, $item['thu']) }}>
                                            {{ $service->ten_dich_vu ?? '' }}
                                        </option>
                                    @endforeach
                                </select>


                                {{-- Mã cán bộ --}}
                                <select class="form-select select2" name="assignments[{{ $item['date'] }}][ma_can_bo][]"
                                    multiple required>
                                    @foreach ($canBoList as $canBo)
                                        <option value="{{ $canBo->id }}"
                                            {{ disableExistUserInDate($serviceAssignments, $item['date'], $canBo) }}>
                                            {{ $canBo->ho_ten . ' ' . ($canBo->quyen == '1' ? '(Cán bộ)' : '(Quản trị viên)') }}
                                        </option>
                                    @endforeach
                                </select>



                                {{-- Ghi chú --}}
                                <div class="mb-3">
                                    <label class="form-label">Ghi chú:</label>
                                    <textarea class="form-control" name="assignments[{{ $item['date'] }}][ghi_chu]" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary px-5">💾 Lưu phân công</button>
            </div>
        </form>


    </div>
</div>
<script></script>
