<?php

return [
    'menu' => [
        // ========== PHẦN 1: QUẢN LÝ CƠ BẢN ==========
        [
            'name' => 'Quản lý đơn vị/phường',
            'icon' => 'fa fa-building',
            'route' => 'admin/don-vi',
            'show_all' => true,
            'single' => true,
        ],
        [
            'name' => 'Quản trị hệ thống',
            'icon' => 'fa fa-cogs',
            'route' => 'admin-management',
            'show_all' => true,
            'subModule' => [
                [
                    'title' => 'Quản lý tài khoản',
                    'route' => 'admin/quantri'
                ],
            ]
        ],

        // ========== PHẦN 2: QUẢN LÝ DỊCH VỤ ==========
        [
            'name' => 'Dịch vụ',
            'icon' => 'fa fa-briefcase',
            'route' => 'services',
            'subModule' => [
                [
                    'title' => 'Quản lí dịch vụ',
                    'route' => 'admin/services'
                ],
                [
                    'title' => 'Lịch dịch vụ',
                    'route' => 'admin/services-schedules'
                ],
                [
                    'title' => 'Phân công dịch vụ',
                    'route' => 'admin/service-assignments'
                ],
            ]
        ],
        [
            'name' => 'Dịch vụ phường',
            'icon' => 'fa fa-building',
            'route' => 'service-phuong',
            'subModule' => [
                [
                    'title' => 'Quản lý dịch vụ',
                    'route' => 'admin/service-phuong'
                ],
                [
                    'title' => 'Lịch và phân công',
                    'route' => 'admin/service-phuong/schedule'
                ],
            ]
        ],

        // ========== PHẦN 3: QUẢN LÝ NGHIỆP VỤ ==========
        [
            'name' => 'Hồ sơ',
            'icon' => 'fa fa-folder',
            'route' => 'ho-so',
            'subModule' => [
                [
                    'title' => 'Quản lí hồ sơ',
                    'route' => 'admin/ho-so'
                ]
            ]
        ],
        [
            'name' => 'Thanh toán',
            'icon' => 'fa fa-money-bill-wave',
            'route' => 'payments',
            'subModule' => [
                [
                    'title' => 'Quản lý thanh toán',
                    'route' => 'admin/payments'
                ],
                [
                    'title' => 'Cấu hình QR Code',
                    'route' => 'admin/qr-code'
                ]
            ]
        ],
        [
            'name' => 'Chat',
            'icon' => 'fa fa-comments',
            'route' => 'room-chats',
            'subModule' => [
                [
                    'title' => 'Quản lí phòng chat',
                    'route' => 'admin/room-chats'
                ],
            ]
        ],

        // ========== PHẦN 4: BÁO CÁO ==========
        [
            'name' => 'Báo cáo',
            'icon' => 'fas fa-chart-bar',
            'route' => 'admin/reports',
            'show_all' => true,
            'subModule' => [
                [
                    'title' => 'Báo cáo tổng hợp',
                    'route' => 'admin/reports'
                ],
                [
                    'title' => 'Đánh giá nhân viên',
                    'route' => 'admin/reports/staff-rating'
                ],
            ]
        ],

        // ========== PHẦN 5: CẤU HÌNH (CHỈ ADMIN TỔNG) ==========
        [
            'name' => 'Cấu hình website',
            'icon' => 'fas fa-cog',
            'route' => 'admin/settings',
            'show_all' => false,
            'single' => true,
        ],
        // Menu: Cán bộ báo nghỉ (Admin phường và Cán bộ truy cập được)
        [
            'name' => 'Nghỉ phép cán bộ',
            'icon' => 'fas fa-calendar-check',
            'route' => 'admin/can-bo-nghi',
            'show_all' => true,
            'single' => true,
        ],

        // ========== PHẦN 6: TÀI KHOẢN CÁ NHÂN ==========
        [
            'name' => 'Tài khoản',
            'icon' => 'fa fa-user-circle',
            'route' => 'account',
            'show_all' => true,
            'subModule' => [
                [
                    'title' => 'Thông tin tài khoản',
                    'route' => 'admin/account/profile'
                ],
                [
                    'title' => 'Đổi mật khẩu',
                    'route' => 'admin/account/change-password'
                ],
            ]
        ],
    ],
];
