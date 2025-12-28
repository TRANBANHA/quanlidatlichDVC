<!-- Footer Start -->
<div class="container-fluid bg-primary text-light footer mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-4 col-md-6 footer-item-1">
                <h4 class="text-white mb-4">{{ $settings['site_name'] ?? 'UBND Thành Phố Đà Nẵng' }}</h4>
                <p class="mb-4">{{ $settings['site_description'] ?? 'Hệ thống đăng ký dịch vụ công trực tuyến của Ủy ban Nhân dân Thành phố Đà Nẵng' }}</p>
                <div class="d-flex pt-2">
                    @if(isset($settings['facebook_url']) && $settings['facebook_url'])
                        <a class="btn btn-square btn-outline-light rounded-circle me-1" href="{{ $settings['facebook_url'] }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    @if(isset($settings['youtube_url']) && $settings['youtube_url'])
                        <a class="btn btn-square btn-outline-light rounded-circle me-1" href="{{ $settings['youtube_url'] }}" target="_blank"><i class="fab fa-youtube"></i></a>
                    @endif
                    @if(isset($settings['zalo_url']) && $settings['zalo_url'])
                        <a class="btn btn-square btn-outline-light rounded-circle me-1" href="{{ $settings['zalo_url'] }}" target="_blank"><i class="fab fa-instagram"></i></a>
                    @endif
                </div>
            </div>
            <div class="col-lg-4 col-md-6 footer-item-2">
                <h5 class="text-white mb-4">Liên kết nhanh</h5>
                <a class="btn btn-link text-light" href="{{ route('index') }}">Trang chủ</a>
                <a class="btn btn-link text-light" href="{{ route('posts.index') }}">Bài viết</a>
                <a class="btn btn-link text-light" href="{{ route('about.index') }}">Giới thiệu</a>
                <a class="btn btn-link text-light" href="{{ route('booking.select-phuong') }}">Đăng ký dịch vụ</a>
                <a class="btn btn-link text-light" href="{{ route('contact.index') }}">Liên hệ</a>
            </div>
            <div class="col-lg-4 col-md-6 footer-item-3">
                <h5 class="text-white mb-4">Thông tin liên hệ</h5>
                @if(isset($settings['contact_address']) && $settings['contact_address'])
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>{{ $settings['contact_address'] }}</p>
                @else
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>24 Trần Phú, Hải Châu, Đà Nẵng</p>
                @endif
                @if(isset($settings['contact_phone']) && $settings['contact_phone'])
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>{{ $settings['contact_phone'] }}</p>
                @else
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+84 236 3892 222</p>
                @endif
                @if(isset($settings['contact_email']) && $settings['contact_email'])
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>{{ $settings['contact_email'] }}</p>
                @else
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@danang.gov.vn</p>
                @endif
                <div class="d-flex pt-2">
                    @if(isset($settings['facebook_url']) && $settings['facebook_url'])
                        <a class="btn btn-square btn-outline-light rounded-circle me-1" href="{{ $settings['facebook_url'] }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    @if(isset($settings['youtube_url']) && $settings['youtube_url'])
                        <a class="btn btn-square btn-outline-light rounded-circle me-1" href="{{ $settings['youtube_url'] }}" target="_blank"><i class="fab fa-youtube"></i></a>
                    @endif
                    @if(isset($settings['zalo_url']) && $settings['zalo_url'])
                        <a class="btn btn-square btn-outline-light rounded-circle me-1" href="{{ $settings['zalo_url'] }}" target="_blank"><i class="fab fa-instagram"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="copyright">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <a class="border-bottom" href="#">{{ $settings['site_name'] ?? 'UBND Thành Phố Đà Nẵng' }}</a>, All Right Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    Thiết kế bởi <a class="border-bottom" href="#">Hệ thống Quản lý Dịch vụ Công</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->

