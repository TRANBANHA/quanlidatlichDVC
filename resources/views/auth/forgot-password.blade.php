<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Foodeiblog Template">
    <meta name="keywords" content="Foodeiblog, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đăng nhập tài khoản</title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800,900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Unna:400,700&display=swap" rel="stylesheet">
    <!-- Css Styles -->
    <link rel="stylesheet" href="{{ asset('website_coppy') }}/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="{{ asset('website_coppy') }}/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="{{ asset('website_coppy') }}/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="{{ asset('website_coppy') }}/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="{{ asset('website_coppy') }}/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="{{ asset('website_coppy') }}/css/style.css" type="text/css">
</head>

<body class="fixed-position">
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>
    <!-- Sign In Section Begin -->
    <div class="signin">
        <div class="signin__warp">
            <div class="signin__content">
                <h2 class="text-center" style="color: white;">Đặt lại mật khẩu</h2>
                <p class="text-center">Vui lòng nhập mật khẩu mới của bạn.</p>
                <div class="signin__form">
                    <div class="signin__form__text">
                        <p>Đăng nhập tài khoản</p>
                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ request()->token }}">
                            <div class="form-group">
                                <input type="email" name="email" class="form-control" placeholder="Email*" required
                                    autofocus value="{{ $email ?? old('email') }}">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div class="form-group">
                                <input type="password" name="password" class="form-control" placeholder="Mật khẩu mới*"
                                    required>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group">
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Xác nhận mật khẩu mới*" required>
                                @error('password_confirmation')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Đặt lại mật khẩu</button>
                        </form>
                        <!-- Thêm link "Quên mật khẩu?" dưới nút Đăng nhập -->
                        <div class="forgot-password">
                            <a href="#" data-toggle="modal" data-target="#forgotPasswordModal">Quên mật
                                khẩu?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Sign In Section End -->
    <!-- Search End -->
    <!-- Js Plugins -->
    <script src="{{ asset('website_coppy') }}/js/jquery-3.3.1.min.js"></script>
    <script src="{{ asset('website_coppy') }}/js/bootstrap.min.js"></script>
    <script src="{{ asset('website_coppy') }}/js/jquery.slicknav.js"></script>
    <script src="{{ asset('website_coppy') }}/js/owl.carousel.min.js"></script>
    <script src="{{ asset('website_coppy') }}/js/main.js"></script>
</body>

</html>
