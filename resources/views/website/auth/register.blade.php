<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Unna:400,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('website_coppy/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website_coppy/css/style.css') }}">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Đăng ký tài khoản</h3>

                        {{-- ✅ Thông báo lỗi chung --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- ✅ Thông báo thành công --}}
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('registers.store') }}" method="POST">
                            @csrf

                            {{-- Họ và tên --}}
                            <div class="mb-3">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="ten" value="{{ old('ten') }}" class="form-control" required>
                                @error('ten')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                                @error('email')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Mật khẩu --}}
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu</label>
                                <input type="password" name="mat_khau" class="form-control" required>
                                @error('mat_khau')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Số điện thoại --}}
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="so_dien_thoai" value="{{ old('so_dien_thoai') }}" class="form-control" maxlength="10" required>
                                @error('so_dien_thoai')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CCCD --}}
                            <div class="mb-3">
                                <label class="form-label">Số CCCD</label>
                                <input type="text" name="cccd" value="{{ old('cccd') }}" class="form-control" maxlength="12" required>
                                @error('cccd')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phường --}}
                            <div class="mb-3">
                                <label class="form-label">Phường <span class="text-danger">*</span></label>
                                <select name="don_vi_id" class="form-select" required>
                                    <option value="">-- Chọn phường --</option>
                                    @foreach ($donVis as $donVi)
                                        <option value="{{ $donVi->id }}" {{ old('don_vi_id') == $donVi->id ? 'selected' : '' }}>
                                            {{ $donVi->ten_don_vi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('don_vi_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Địa chỉ --}}
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                                <input type="text" name="dia_chi" value="{{ old('dia_chi') }}" class="form-control" placeholder="Ví dụ: 123 Đường Nguyễn Văn Linh, Phường Hòa Cường Bắc" required>
                                <small class="form-text text-muted">Nhập địa chỉ chi tiết (số nhà, tên đường, phường/xã)</small>
                                @error('dia_chi')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Đăng ký</button>

                            <p class="text-center mt-3">
                                Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
