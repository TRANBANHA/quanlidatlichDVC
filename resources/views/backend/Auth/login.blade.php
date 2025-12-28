<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modernize Free</title>
    <link rel="stylesheet" href="{{ asset('css') }}/styles.min.css" />
    <link rel="stylesheet" href="{{ asset('website_coppy') }}/css/bootstrap.min.css" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-4 col-lg-3 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                {{-- <div class="text-center">
                                   // <img src="{{ asset('storage') }}/{{ get('logo') }}"
                                        style="width: 185px; width: 150px;
                                    height: 150px;
                                    object-fit: cover;"
                                        alt="logo">
                                    <h4 class="mt-1 mb-5 pb-1">UBND Xã Triệu Lăng</h4>
                                </div> --}}
                                <form action="{{ route('admin.post.login') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                         <label for="exampleInputPassword1" class="form-label">Email / Số điện thoại / Tên đăng nhập</label>
                                        <input type="text" class="form-control" placeholder="Username" name="login"
                                            required="">
                                    </div>
                                    <div class="mb-4">
                                        <label for="exampleInputPassword1" class="form-label">Mật khẩu</label>
                                        <input type="password" class="form-control" placeholder="Password"
                                            name="password" required="">
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="form-check">
                                            {{-- <input class="form-check-input primary" type="checkbox" value=""
                                                id="flexCheckChecked" checked>
                                            <label class="form-check-label text-dark" for="flexCheckChecked">
                                                Remeber this Device
                                            </label> --}}
                                        </div>
                                        <a class="text-muted" href="#" data-bs-toggle="modal"
                                            data-bs-target="#forgotPasswordModal">Quên Mật Khẩu?</a>

                                    </div>
                                    <button class="btn btn-primary">Sign
                                        In</button>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot
                        Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.password.email') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="reset-email" class="form-label">Nhập email</label>
                            <input type="email" name="email" class="form-control" id="reset-email"
                                placeholder="Email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Gửi đi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
