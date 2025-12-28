<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Foodeiblog Template">
    <meta name="keywords" content="Foodeiblog, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Log In</title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800,900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Unna:400,700&display=swap" rel="stylesheet">
    <!-- Css Styles -->
    <link rel="stylesheet" href="{{ asset('website_coppy') }}/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="{{ asset('website_coppy') }}/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="{{ asset('website_coppy') }}/css/style.css" type="text/css">
    <style>
        .input-group .btn {
            border-radius: 0;
            background: none;
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-group .btn i {
            font-size: 1.2rem;
            color: #6c757d;
        }

        .input-group .form-control {
            border-radius: 0;
            box-shadow: none;
        }

        .input-group .btn:hover i {
            color: #495057;
        }
    </style>
</head>

<body class="vh-100 gradient-form" style="background-color: #eee;">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-xl-10">
                <div class="card rounded-3 text-black">
                    <div class="row g-0">
                        <div class="col-lg-6">
                            <div class="card-body p-md-5 mx-md-4">

                                {{-- <div class="text-center">
                                    <img src="{{ asset('storage') }}/{{ get('logo') }}"
                                        style="width: 185px; width: 150px;
                                    height: 150px;
                                    object-fit: cover;"
                                        alt="logo">
                                    <h4 class="mt-1 mb-5 pb-1">hệ thống đăng ký và nhận kết quả trực tuyến cho thành phố Đà Nẵng</h4>
                                </div> --}}

                                <form action="{{ route('login') }}" method="POST">
                                    @csrf
                                    <p>Vui lòng đăng nhập vào tài khoản của bạn</p>

                                    <div data-mdb-input-init class="form-outline mb-2">
                                        <input type="login" id="form2Example11" name="login" class="form-control"
                                            placeholder="Phone number or phone address" required />
                                        {{-- <label class="form-label" for="form2Example11">Username</label> --}}
                                    </div>

                                    <div data-mdb-input-init class="form-outline mb-2">
                                        <div class="input-group">
                                            <input type="password" id="form2Example22" name="password"
                                                class="form-control" required />
                                            <button class="btn" type="button" id="togglePassword">
                                                <i id="passwordIcon" class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                        {{-- <label class="form-label" for="form2Example22">Password</label> --}}
                                    </div>

                                    <div class="text-center pt-1 mb-5 pb-1">
                                        <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3"
                                            type="submit">
                                            Đăng Nhập
                                        </button>
                                        <a class="text-muted d-block mb-2" href="#" data-bs-toggle="modal"
                                            data-bs-target="#forgotPasswordModal">
                                            Quên Mật Khẩu?
                                        </a>

                                        <p class="mt-3 mb-0">
                                            Bạn chưa có tài khoản?
                                            <a href="/registers" class="text-primary fw-bold"
                                            >Đăng ký ngay</a>
                                        </p>
                                    </div>


                                    {{-- <div class="d-flex align-items-center justify-content-center pb-4">
                                        <p class="mb-0 me-2">Don't have an account?</p>
                                        <button type="button" class="btn btn-outline-danger">Create new</button>
                                    </div> --}}

                                </form>

                            </div>
                        </div>
                        <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                            <div class=" px-3 py-4 p-md-5 mx-md-4">
                                <h4 class="mb-4">We are more than just a company</h4>
                                <p class="small mb-0">Join us to access seamless services including citizen
                                    registration, temporary residence, and more. Manage your tasks efficiently with our
                                    platform.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="reset-email" class="form-label">Enter your email</label>
                            <input type="email" name="email" class="form-control" id="reset-email"
                                placeholder="Email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Js Plugins -->
    <script src="{{ asset('website_coppy') }}/js/jquery-3.3.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('website_coppy') }}/js/main.js"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#form2Example22');
        const passwordIcon = document.querySelector('#passwordIcon');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            passwordIcon.classList.toggle('fa-eye');
            passwordIcon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>
