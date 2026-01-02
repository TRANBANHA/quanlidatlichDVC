<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <div class="logo-header pb-2" style="height: 130px;background-color:black;">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid" style="max-height: 70px;">
            @else
                <img src="{{ asset('images/default-avatar.png') }}" alt="Logo" class="img-fluid" style="max-height: 100px;">
            @endif
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>
    </div>

    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                @foreach (config('route.menu') as $menu)
                    @php
                        // Lấy đường dẫn hiện tại
                        $currentPath = Request::path();
                        $currentRoute = Request::route()->getName() ?? '';
                        
                        // Kiểm tra route cha - so sánh với cả path và route name
                        $menuRoute = ltrim($menu['route'], '/');
                        $isActiveParent = false;
                        
                        // Kiểm tra path
                        if (strpos($currentPath, $menuRoute) === 0) {
                            $isActiveParent = true;
                        }
                        
                        // Nếu có submenu, kiểm tra xem có submenu nào active không
                        $hasActiveChild = false;
                        if (isset($menu['subModule'])) {
                            foreach ($menu['subModule'] as $sub) {
                                $subRoute = ltrim($sub['route'], '/');
                                // Kiểm tra path chính xác hơn
                                if ($currentPath === $subRoute || strpos($currentPath, $subRoute . '/') === 0) {
                                    $hasActiveChild = true;
                                    $isActiveParent = true;
                                    break;
                                }
                            }
                        }
                        
                        // Kiểm tra quyền hiển thị menu dựa trên guard
                        $showMenu = true;
                        if (Auth::guard('admin')->check()) {
                            $admin = Auth::guard('admin')->user();
                            
                            // Admin tổng (quyen = 1): Xem tất cả menu TRỪ menu "Dịch vụ", "Dịch vụ phường", "Thanh toán", "Hồ sơ"
                            // CHỈ Admin tổng mới thấy "Cấu hình website" và "Thông báo"
                            if ($admin->quyen === 1) {
                                // Chỉ Admin tổng mới thấy "Cấu hình website" và "Thông báo"
                                if ($menu['name'] === 'Cấu hình website' || $menu['name'] === 'Thông báo') {
                                    $showMenu = true;
                                } else {
                                    // Các menu khác: Xem tất cả TRỪ "Dịch vụ", "Dịch vụ phường", "Thanh toán", "Hồ sơ"
                                    $showMenu = $menu['name'] !== 'Dịch vụ' 
                                             && $menu['name'] !== 'Dịch vụ phường' 
                                             && $menu['name'] !== 'Thanh toán' 
                                             && $menu['name'] !== 'Hồ sơ';
                                }
                            }
                            // Admin phường (quyen = 2): Xem menu có show_all hoặc menu quản lý, KHÔNG thấy "Cấu hình website", "Thông báo", "Chat", "Quản lý đơn vị/phường"
                            elseif ($admin->quyen === 2) {
                                // Admin phường KHÔNG thấy "Cấu hình website", "Thông báo", "Chat", "Quản lý đơn vị/phường"
                                if ($menu['name'] === 'Cấu hình website' || $menu['name'] === 'Thông báo' || $menu['name'] === 'Chat' || $menu['name'] === 'Quản lý đơn vị/phường') {
                                    $showMenu = false;
                                } else {
                                    $showMenu = isset($menu['show_all']) || in_array($menu['name'], ['Quản trị hệ thống', 'Hồ sơ', 'Dịch vụ phường', 'Báo cáo', 'Thanh toán','Nghỉ phép cán bộ']);
                                }
                            }
                            // Cán bộ (quyen = 0): Xem Hồ sơ, Chat, Tài khoản, Báo cáo, Cán bộ báo nghỉ
                            else {
                                // Cán bộ KHÔNG thấy "Cấu hình website", "Thông báo", "Thanh toán"
                                if ($menu['name'] === 'Cấu hình website' || $menu['name'] === 'Thông báo' || $menu['name'] === 'Thanh toán') {
                                    $showMenu = false;
                                } else {
                                    $showMenu = in_array($menu['name'], ['Hồ sơ', 'Chat', 'Tài khoản', 'Báo cáo', 'Nghỉ phép cán bộ']);
                                }
                            }
                        }
                    @endphp

                    @if($showMenu)
                        <li class="nav-item {{ $isActiveParent ? 'active' : '' }}">
                            @if(isset($menu['single']) && $menu['single'])
                                {{-- Menu đơn, không có submenu --}}
                                <a href="{{ url($menu['route']) }}" class="{{ $isActiveParent ? 'active' : '' }}">
                                    <i class="{{ $menu['icon'] }}"></i>
                                    <p>{{ $menu['name'] }}</p>
                                </a>
                            @else
                                {{-- Menu có submenu --}}
                                <a data-bs-toggle="collapse"
                                    href="#{{ Str::slug($menu['name']) }}"
                                    class="{{ $isActiveParent ? 'active' : 'collapsed' }}"
                                    aria-expanded="{{ $isActiveParent ? 'true' : 'false' }}">
                                    <i class="{{ $menu['icon'] }}"></i>
                                    <p>{{ $menu['name'] }}</p>
                                    <span class="caret"></span>
                                </a>

                                <div class="collapse {{ $isActiveParent ? 'show' : '' }}" id="{{ Str::slug($menu['name']) }}">
                                    <ul class="nav nav-collapse">
                                        @foreach ($menu['subModule'] as $sub)
                                            @php
                                                $subRoute = ltrim($sub['route'], '/');
                                                // Kiểm tra path chính xác
                                                $isActiveChild = ($currentPath === $subRoute || strpos($currentPath, $subRoute . '/') === 0);
                                                
                                                // Kiểm tra quyền hiển thị submenu dựa trên guard
                                                $showSubMenu = true;
                                                if (Auth::guard('admin')->check()) {
                                                    $admin = Auth::guard('admin')->user();
                                                    
                                                    // Admin tổng: Xem tất cả submenu TRỪ "Đánh giá nhân viên"
                                                    if ($admin->quyen === 1) {
                                                        $showSubMenu = $sub['title'] !== 'Đánh giá nhân viên';
                                                    }
                                                    // Admin phường: Xem tất cả submenu
                                                    elseif ($admin->quyen === 2) {
                                                        $showSubMenu = true;
                                                    }
                                                    // Cán bộ: Xem Hồ sơ, Chat, Tài khoản, Báo cáo
                                                    else {
                                                        $showSubMenu = in_array($sub['title'], ['Quản lí hồ sơ', 'Quản lí phòng chat', 'Thông tin tài khoản', 'Đổi mật khẩu', 'Đánh giá nhân viên']);
                                                    }
                                                }
                                            @endphp
                                            
                                            @if($showSubMenu)
                                                <li class="{{ $isActiveChild ? 'active' : '' }}">
                                                    <a href="{{ url($sub['route']) }}" class="{{ $isActiveChild ? 'active' : '' }}">
                                                        {{ $sub['title'] }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>

<style>
    /* Menu cha active */
    .nav-item.active {
        background-color: rgba(0, 83, 179, 0.1);
    }
    
    .nav-item.active > a,
    .nav-item.active > a.active {
        color: #fff !important;
        font-weight: 600;
    }
    
    .nav-item.active > a .caret {
        transform: rotate(180deg);
        transition: transform 0.3s ease;
    }

    /* Menu con active */
    .nav-collapse li.active {
        background-color: rgba(255, 255, 255, 0.15);
        border-left: 3px solid #007bff;
    }
    
    .nav-collapse li.active a,
    .nav-collapse li.active a.active {
        color: #fff !important;
        font-weight: 600;
        padding-left: 15px;
    }

    /* Đảm bảo menu con có màu chữ khi không active */
    .nav-collapse li a {
        color: rgba(255, 255, 255, 0.7);
        padding-left: 18px;
        transition: all 0.3s ease;
        display: block;
    }

    .nav-collapse li a:hover {
        color: #fff !important;
        background-color: rgba(255, 255, 255, 0.1);
        padding-left: 20px;
    }
    
    /* Menu đơn active */
    .nav-item:not(.active) > a {
        color: rgba(255, 255, 255, 0.7);
    }
    
    .nav-item:not(.active) > a:hover {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.05);
    }
    
    /* Đảm bảo collapse mở khi active */
    .nav-item.active .collapse {
        display: block !important;
    }
</style>
