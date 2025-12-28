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

                    <form action="{{ route('temp-residence.update', $tempResidence->id) }}" method="POST">
                        @csrf
                        @method('PUT') <!-- Sử dụng PUT method -->

                        <div class="row">
                            <!-- Người dùng -->
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">Người dùng</label>
                                <select id="user_id" name="user_id" class="form-select" required>
                                    <option value="" disabled>Chọn người dùng</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $user->id == $tempResidence->user_id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Họ và tên -->
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Họ và tên</label>
                                <input type="text" id="full_name" name="full_name" class="form-control"
                                    value="{{ old('full_name', $tempResidence->full_name) }}" required>
                            </div>

                            <!-- Số CCCD -->
                            <div class="col-md-6 mb-3">
                                <label for="cccd" class="form-label">Số CCCD</label>
                                <input type="text" id="cccd" name="cccd" class="form-control"
                                    value="{{ old('cccd', $tempResidence->cccd) }}" required>
                            </div>

                            <!-- Địa chỉ -->
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <input type="text" id="address" name="address" class="form-control"
                                    value="{{ old('address', $tempResidence->address) }}" required>
                            </div>

                            <!-- Ngày sinh -->
                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Ngày sinh</label>
                                <input type="date" id="birth_date" name="birth_date" class="form-control"
                                    value="{{ old('birth_date', $tempResidence->birth_date) }}" required>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="{{ old('email', $tempResidence->email) }}" placeholder="optional">
                            </div>

                            <!-- Giới tính -->
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Giới tính</label>
                                <select id="gender" name="gender" class="form-select">
                                    <option value="A" {{ $tempResidence->gender == 'A' ? 'selected' : '' }}>Chưa xác
                                        định</option>
                                    <option value="N" {{ $tempResidence->gender == 'N' ? 'selected' : '' }}>Nam
                                    </option>
                                    <option value="F" {{ $tempResidence->gender == 'F' ? 'selected' : '' }}>Nữ
                                    </option>
                                </select>
                            </div>

                            <!-- Số điện thoại -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="text" id="phone" name="phone" class="form-control"
                                    value="{{ old('phone', $tempResidence->phone) }}" required>
                            </div>

                            <!-- Nơi cư trú hiện tại -->
                            <div class="col-md-6 mb-3">
                                <label for="current_residence" class="form-label">Nơi cư trú hiện tại</label>
                                <input type="text" id="current_residence" name="current_residence" class="form-control"
                                    value="{{ old('current_residence', $tempResidence->current_residence) }}" required>
                            </div>

                            <!-- Nơi sinh -->
                            <div class="col-md-6 mb-3">
                                <label for="birth_place" class="form-label">Nơi sinh</label>
                                <input type="text" id="birth_place" name="birth_place" class="form-control"
                                    value="{{ old('birth_place', $tempResidence->birth_place) }}" required>
                            </div>

                            <!-- Nơi tạm trú -->
                            <div class="col-md-6 mb-3">
                                <label for="temp_residence_place" class="form-label">Nơi tạm trú</label>
                                <input type="text" id="temp_residence_place" name="temp_residence_place"
                                    class="form-control"
                                    value="{{ old('temp_residence_place', $tempResidence->temp_residence_place) }}"
                                    required>
                            </div>

                            <!-- Quốc tịch -->
                            <div class="col-md-6 mb-3">
                                <label for="nationality" class="form-label">Quốc tịch</label>
                                <input type="text" id="nationality" name="nationality" class="form-control"
                                    value="{{ old('nationality', $tempResidence->nationality) }}" required>
                            </div>

                            <!-- Dân tộc -->
                            <div class="col-md-6 mb-3">
                                <label for="ethnicity" class="form-label">Dân tộc</label>
                                <input type="text" id="ethnicity" name="ethnicity" class="form-control"
                                    value="{{ old('ethnicity', $tempResidence->ethnicity) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="occupation" class="form-label">Nghề nghiệp</label>
                                <input type="text" id="occupation" name="occupation" class="form-control"
                                    value="{{ old('ethnicity', $tempResidence->occupation) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="permanent_residence" class="form-label">Nơi cư trú thường trú</label>
                                <input type="text" id="permanent_residence" name="permanent_residence"
                                    value="{{ old('ethnicity', $tempResidence->permanent_residence) }}"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="registration_date" class="form-label">Ngày đăng ký</label>
                                <input type="date" id="registration_date" name="registration_date"
                                    value="{{ old('ethnicity', $tempResidence->registration_date) }}"
                                    class="form-control" required>
                            </div>
                            <!-- Trạng thái phê duyệt -->
                            <div class="col-md-6 mb-3">
                                <label for="approval_status" class="form-label">Trạng thái phê duyệt</label>
                                <select id="approval_status" name="approval_status" class="form-select">
                                    <option value="1" {{ $tempResidence->approval_status == 1 ? 'selected' : '' }}>Đã
                                        phê duyệt</option>
                                    <option value="0" {{ $tempResidence->approval_status == 0 ? 'selected' : '' }}>
                                        Chưa phê duyệt</option>
                                    <option value="2" {{ $tempResidence->approval_status == 2 ? 'selected' : '' }}>Bị
                                        từ chối</option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-success">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
