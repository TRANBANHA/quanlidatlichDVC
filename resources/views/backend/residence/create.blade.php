@extends('backend.components.layout')

@section('title')
    Thêm đăng ký tạm trútrú
@endsection

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="#">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                         <a href="/admin">Trang chủ</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('death.index') }}">Thêm đăng ký tạm trútrú</a>
                    </li>
                </ul>
            </div>

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"> Đăng Ký tạm trútrú</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('temp-residence.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">Người dùng</label>
                                <select id="user_id" name="user_id" class="form-select" required>
                                    <option value="" disabled selected>Chọn người dùng</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}
                                            ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Họ và tên</label>
                                <input type="text" id="full_name" name="full_name" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cccd" class="form-label">Số CCCD</label>
                                <input type="text" id="cccd" name="cccd" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <input type="text" id="address" name="address" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Ngày sinh</label>
                                <input type="date" id="birth_date" name="birth_date" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    placeholder="optional">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Giới tính</label>
                                <select id="gender" name="gender" class="form-select">
                                    <option value="A">Chưa xác định</option>
                                    <option value="N">Nam</option>
                                    <option value="N">Nữ</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="text" id="phone" name="phone" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="current_residence" class="form-label">Nơi cư trú hiện tại</label>
                                <input type="text" id="current_residence" name="current_residence" class="form-control"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="birth_place" class="form-label">Nơi sinh</label>
                                <input type="text" id="birth_place" name="birth_place" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="temp_residence_place" class="form-label">Nơi tạm trú</label>
                                <input type="text" id="temp_residence_place" name="temp_residence_place"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nationality" class="form-label">Quốc tịch</label>
                                <input type="text" id="nationality" name="nationality" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="ethnicity" class="form-label">Dân tộc</label>
                                <input type="text" id="ethnicity" name="ethnicity" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="occupation" class="form-label">Nghề nghiệp</label>
                                <input type="text" id="occupation" name="occupation" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="notes" class="form-label">Ghi chú</label>
                                <textarea id="notes" name="notes" class="form-control"></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="permanent_residence" class="form-label">Nơi cư trú thường trú</label>
                                <input type="text" id="permanent_residence" name="permanent_residence"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="registration_date" class="form-label">Ngày đăng ký</label>
                                <input type="date" id="registration_date" name="registration_date"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="approval_status" class="form-label">Trạng thái phê duyệt</label>
                                <select id="approval_status" name="approval_status" class="form-select">
                                    <option value="1">Đã phê duyệt</option>
                                    <option value="0">Chưa phê duyệt</option>
                                    <option value="2">Bị từ chối</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Tạo mới</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
