@extends('website.components.layout')

@section('title')
    Tra cứu hồ sơ
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="mb-4 text-primary text-center">Tra cứu hồ sơ</h3>

                        <form method="GET" action="{{ route('ho-so.lookup') }}" class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="lookupType" class="form-label">Loại tra cứu</label>
                                <select name="type" id="lookupType" class="form-select">
                                    <option value="ma_ho_so" {{ $type === 'ma_ho_so' ? 'selected' : '' }}>Mã hồ sơ</option>
                                    <option value="cccd" {{ $type === 'cccd' ? 'selected' : '' }}>Số CCCD</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label for="keyword" class="form-label">
                                    {{ $type === 'cccd' ? 'Nhập số CCCD (12 chữ số)' : 'Nhập mã hồ sơ' }}
                                </label>
                                <input type="text" id="keyword" name="keyword" value="{{ $keyword }}"
                                       class="form-control"
                                       placeholder="{{ $type === 'cccd' ? 'Ví dụ: 079012345678' : 'Ví dụ: HS2024111201' }}">
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary px-5">Tra cứu</button>
                            </div>
                        </form>

                        @error('keyword')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        @if ($keyword !== '')
                            <h5 class="mb-3">Kết quả tìm kiếm</h5>
                            @if ($results->isEmpty())
                                <div class="alert alert-warning">
                                    Không tìm thấy hồ sơ phù hợp với thông tin bạn cung cấp.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Mã hồ sơ</th>
                                                <th>Dịch vụ</th>
                                                <th>Phường</th>
                                                <th>Ngày hẹn</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($results as $record)
                                                <tr>
                                                    <td>{{ $record->ma_ho_so }}</td>
                                                    <td>{{ $record->dichVu->ten_dich_vu ?? '-' }}</td>
                                                    <td>{{ $record->donVi->ten_don_vi ?? '-' }}</td>
                                                    <td>{{ optional($record->ngay_hen)->format('d/m/Y') }}</td>
                                                    <td>
                                                        <span class="badge 
                                                            @class([
                                                                'bg-secondary' => $record->trang_thai === \App\Models\HoSo::STATUS_RECEIVED,
                                                                'bg-info text-dark' => $record->trang_thai === \App\Models\HoSo::STATUS_PROCESSING,
                                                                'bg-warning text-dark' => $record->trang_thai === \App\Models\HoSo::STATUS_NEED_SUPPLEMENT,
                                                                'bg-success' => $record->trang_thai === \App\Models\HoSo::STATUS_COMPLETED,
                                                                'bg-danger' => $record->trang_thai === \App\Models\HoSo::STATUS_CANCELLED,
                                                            ])
                                                        ">{{ $record->trang_thai }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

