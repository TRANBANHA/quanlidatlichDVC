@extends('backend.components.layout')
@section('title')
    Thêm đơn vị/phường
@endsection
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Thêm đơn vị/phường</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="/admin">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('don-vi.index') }}">Danh sách đơn vị</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li>Thêm mới</li>
                </ul>
            </div>

            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('don-vi.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="ten_don_vi" class="form-label">Tên đơn vị/phường <span class="text-danger">*</span></label>
                            <input type="text" id="ten_don_vi" name="ten_don_vi" class="form-control"
                                placeholder="Nhập tên đơn vị/phường" value="{{ old('ten_don_vi') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô tả</label>
                            <textarea id="mo_ta" name="mo_ta" class="form-control" rows="3"
                                placeholder="Nhập mô tả">{{ old('mo_ta') }}</textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">Tạo đơn vị</button>
                            <a href="{{ route('don-vi.index') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

