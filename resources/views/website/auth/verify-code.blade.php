<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác minh tài khoản</title>
    <link rel="stylesheet" href="{{ asset('website_coppy/css/bootstrap.min.css') }}">
</head>
<body class="vh-100 d-flex justify-content-center align-items-center bg-light">
    <div class="card shadow p-4" style="width: 400px;">
        <h3 class="text-center mb-3">Nhập mã xác minh</h3>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('web.verifyCode.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Mã xác minh</label>
                <input type="text" name="code" class="form-control" maxlength="7" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Xác minh</button>
        </form>
    </div>
</body>
</html>
