<div class="container-fluid sticky-top px-0">
    <div class="container-fluid bg-white shadow-sm header-navbar">
        <div class="container px-0">
            <nav class="navbar navbar-light navbar-expand-xl py-3">
                <a href="/" class="navbar-brand mt-3" style="display: flex; align-items: center;">
                    @if(isset($settings['site_logo']) && $settings['site_logo'])
                        <img src="{{ asset('storage/' . $settings['site_logo']) }}"
                            style="width: 115px; height: 115px; object-fit: cover; margin-right: 15px;" alt="logo">
                    @endif
                    <p class="text-gradient display-6 mb-2 fw-bold navbar-title" style="line-height: 1.5; margin: 0; font-size: 1.8rem;">
                        {{ $settings['site_name'] ?? 'UBND Thành Phố Đà Nẵng' }}<br>
                        <span style="font-size: 0.9em;">{{ $settings['site_description'] ?? '' }}</span>
                    </p>
                </a>


                <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-primary"></span>
                </button>
                <div class="collapse navbar-collapse bg-white py-3" id="navbarCollapse">
                    <div class="navbar-nav mx-auto">
                        <a href="/" class="nav-item nav-link {{ Request::is('/') ? 'active' : '' }}">Trang chủ</a>
                        <a href="/posts" class="nav-item nav-link {{ Request::is('posts') ? 'active' : '' }}">Bài viết</a>
                        <a href="/gioi-thieu"
                            class="nav-item nav-link {{ Request::is('gioi-thieu') ? 'active' : '' }}">Giới thiệu</a>
                        <a href="/contact" class="nav-item nav-link {{ Request::is('contact') ? 'active' : '' }}">Liên hệ</a>
                        <a href="{{ route('booking.select-phuong') }}"
                            class="nav-item nav-link nav-link-special {{ Request::is('dat-lich*') || Request::is('/register-services') ? 'active' : '' }}">Đăng kí dịch vụ</a>
                    </div>

                    <!-- User Account Dropdown -->
                    <div class="d-flex justify-content-end align-items-center ms-auto">
                        <div class="dropdown user-dropdown">
                            <button
                                class="btn-user btn border-0 rounded-circle bg-white shadow-sm my-auto dropdown-toggle"
                                type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user text-primary"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="dropdownMenuButton">
                                @if (Auth::check())
                                    <li><h6 class="dropdown-header">{{ Auth::user()->ten ?? Auth::user()->name ?? 'Người dùng' }}</h6></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="/info?action=tab1"><i class="fas fa-user-circle me-2"></i>Thông tin tài khoản</a></li>
                                @if (!Auth::check())
                                    <li><a class="dropdown-item" href="/login"><i class="fas fa-sign-in-alt me-2"></i>Đăng nhập</a></li>
                                @else
                                    <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng Xuất</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

            </nav>
        </div>
    </div>
</div>
