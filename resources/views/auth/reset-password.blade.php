<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Reset Password Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
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

    <!-- Reset Password Section Begin -->
    <div class="signin">
        <div class="signin__warp">
            <div class="signin__content">
                <div class="signin__logo text-center">
                    <a href="#"><img src="{{ asset('img') }}/logo.jpg" alt="" width="400"
                            height="200"></a>
                </div>
                <h2 class="text-center">Đặt lại mật khẩu</h2>
                <p class="text-center">Vui lòng nhập mật khẩu mới của bạn.</p>

                <div class="signin__form">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <!-- Token Hidden Field -->
                        <input type="hidden" name="token" value="{{ $token }}">

                        <!-- Email Address -->
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
                </div>
            </div>
        </div>
    </div>
    <!-- Reset Password Section End -->

    <!-- Js Plugins -->
    <script src="{{ asset('website_coppy') }}/js/jquery-3.3.1.min.js"></script>
    <script src="{{ asset('website_coppy') }}/js/bootstrap.min.js"></script>
    <script src="{{ asset('website_coppy') }}/js/jquery.slicknav.js"></script>
    <script src="{{ asset('website_coppy') }}/js/owl.carousel.min.js"></script>
    <script src="{{ asset('website_coppy') }}/js/main.js"></script>
</body>

</html>
