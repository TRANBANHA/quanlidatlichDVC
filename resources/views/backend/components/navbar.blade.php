<nav class="navbar navbar-expand-lg main-navbar bg-dark">
    <div class="container-fluid">
        <div class="navbar-brand">
            <a href="{{ route('admin.index') }}" class="text-white text-decoration-none">
                Quản trị hệ thống
            </a>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        {{ Auth::guard('admin')->user()->name ?? 'Admin' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.account.profile') }}">
                                <i class="fas fa-user me-2"></i> Thông tin tài khoản
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.account.change-password') }}">
                                <i class="fas fa-key me-2"></i> Đổi mật khẩu
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('admin.logout') }}">
                                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
