<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title')</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('img') }}/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ asset('js') }}/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('css') }}/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>
    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('css') }}/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ asset('css') }}/plugins.min.css" />
    <link rel="stylesheet" href="{{ asset('css') }}/kaiadmin.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="{{ asset('css') }}/demo.css" />
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.1.0/ckeditor5.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @yield('styles')
    @stack('styles')
    <style>
        /* Định nghĩa màu cho mục menu đang được chọn */
        .nav-item.active>a {
            background-color: #4CAF50;
            /* Màu nền cho mục đang active */
            color: #fff;
            /* Màu chữ khi active */
            font-weight: bold;
            /* Chữ đậm */
        }

        /* Màu cho các mục con khi active */
        .nav-collapse li.active>a {
            color: #FFC107;
            /* Màu chữ khi active */
            font-weight: bold;
            /* Chữ đậm */
        }

        /* Màu nền và chữ khi hover */
        .nav-item>a:hover {
            background-color: #3e8e41;
            color: white;
        }
    </style>
</head>
