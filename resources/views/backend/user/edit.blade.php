@extends('backend.components.layout')
@section('title')
    Chỉnh sửa tài khoản người dùng
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
                        <a href="{{ route('users.index') }}">Chỉnh sửa tài khoản</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Edit Admin</div>
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

                            <form action="{{ route('users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Leave blank to keep current password">
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" id="phone" name="phone" class="form-control"
                                        value="{{ old('phone', $user->phone) }}" required>
                                </div>
                                {{-- <div class="mb-3">
                                    <label for="head_code" class="form-label">Head code</label>
                                    <input type="text" id="head_code" name="head_code" class="form-control"
                                        value="{{ old('head_code', $user->head_code) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="members_count" class="form-label">memberscount</label>
                                    <input type="text" id="members_count" name="members_count" class="form-control"
                                        value="{{ old('members_count', $user->members_count) }}" required>
                                </div> --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="publish" class="form-label">Publish Status</label>
                                            <select id="publish" name="publish" class="form-select">
                                                <option value="" disabled>-- Select Publish Status --</option>
                                                <option value="1"
                                                    {{ old('publish', $user->publish ?? '') == '1' ? 'selected' : '' }}>
                                                    Publish</option>
                                                <option value="0"
                                                    {{ old('publish', $user->publish ?? '') == '0' ? 'selected' : '' }}>
                                                    Unpublish</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="region_id" class="form-label">Địa chỉ khu phố</label>
                                        <select id="region_id" name="region_id" class="form-select">
                                            <option value="" disabled>-- chọn khu phố --</option>
                                            @foreach ($regions as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('region_id', $user->region_id ?? '') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->block }} - {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>


                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
