<!-- Main Post Section Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-7 col-xl-8 mt-0">
                {{-- @foreach ($postslide->take(2) as $post)
                    <div class="position-relative overflow-hidden rounded mb-4">
                        <img src="{{ asset('storage') }}/{{ $post->image }}" class="img-fluid rounded img-zoomin w-100"
                            alt="{{ $post->title }}">
                        <div class="d-flex justify-content-center px-4 position-absolute flex-wrap"
                            style="bottom: 10px; left: 0;">
                            <a href="#" class="text-white me-3 link-hover"><i class="fa fa-clock"></i>
                                {{ $post->published_at }} minute read</a>
                            <a href="#" class="text-white me-3 link-hover"><i class="fa fa-eye"></i>
                                {{ $post->views }} Views</a>
                            <a href="#" class="text-white me-3 link-hover"><i class="fa fa-comment-dots"></i>
                                {{ $post->comments_count }} Comment</a>
                            <a href="#" class="text-white link-hover"><i class="fa fa-arrow-up"></i>
                                {{ $post->shares }} Share</a>
                        </div>
                    </div>
                    <div class="border-bottom py-3">
                        <a href="{{ route('post.detail', $post->slug) }}"
                            class="display-4 text-dark mb-0 link-hover">{!! $post->title !!}</a>
                    </div>
                    {{-- <p class="mt-3 mb-4">{{ Str::limit($post->content, 150, '...') }}</p> --}}
                {{-- @endforeach --}}

                <div class="bg-light p-4 rounded">
                    <div class="news-2">
                        <h3 class="mb-4">Top Story</h3>
                    </div>
                    {{-- <div class="row g-4 align-items-center">
                        @foreach ($postslide->skip(2)->take(2) as $topPost)
                            <!-- Lấy 3 bài viết hàng đầu -->
                            <div class="col-md-6">
                                <div class="rounded overflow-hidden">
                                    <img src="{{ asset('storage') }}/{{ $topPost->image }}"
                                        class="img-fluid rounded img-zoomin w-100" alt="{{ $topPost->title }}">
                                </div>
                                <div class="d-flex flex-column mt-2">
                                    <a href="{{ route('post.detail', $post->slug) }}"
                                        class="h3">{!! $topPost->title !!}</a>
                                    <p class="mb-0 fs-5"><i class="fa fa-clock">
                                            {{ $topPost->published_at }} minute read</i></p>
                                    <p class="mb-0 fs-5"><i class="fa fa-eye"> {{ $topPost->views }} Views</i></p>
                                </div>
                            </div>
                        @endforeach
                    </div> --}}
                </div>
            </div>

            <div class="col-lg-5 col-xl-4">
                <div class="bg-light rounded p-4 pt-0">
                    <div class="row g-4">
                        @foreach (array_slice($rssArticles['thegioi'], 5, 5) as $sidebarPost)
                            <!-- Lấy 5 bài viết cho sidebar -->
                            <div class="col-12">
                                <div class="rounded overflow-hidden">
                                    <img src="{{ $sidebarPost['image'] }}" class="img-fluid rounded img-zoomin w-100"
                                        alt="{{ $sidebarPost['title'] }}">
                                </div>
                                <div class="d-flex flex-column mt-2">
                                    <!-- Sử dụng link từ RSS -->
                                    <a href="{{ $sidebarPost['link'] }}" target="_blank" class="h4 mb-2">
                                        {!! $sidebarPost['title'] !!}
                                    </a>
                                    <p>VnExpress - Thế giới</p>
                                    {{-- <p class="fs-5 mb-0"><i class="fa fa-clock">
                                        {{ $sidebarPost['published_at'] }} minute read</i></p> --}}
                                    {{-- <p class="fs-5 mb-0"><i class="fa fa-eye"> {{ $sidebarPost['views'] }} Views</i>
                                </p> --}}
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Main Post Section End -->


<!-- Banner Start -->
<div class="container-fluid "
    style="background: linear-gradient(rgba(202, 203, 185, 1), rgba(202, 203, 185, 1));">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <h1 class="mb-4 text-primary">Đăng Ký Công Dân Mới</h1>
                <h2 class="mb-4">Vui lòng cung cấp thông tin cá nhân của bạn để hoàn tất đăng ký</h2>
                <p class="text-dark mb-4 pb-2">
                    Để trở thành công dân hợp pháp, bạn cần cung cấp các thông tin cá nhân chính xác dưới đây. Tất cả
                    các dữ liệu sẽ được bảo mật và chỉ sử dụng cho mục đích quản lý công dân.
                </p>
                <div class="d-flex justify-content-start">
                    <a href="/citizens" class="btn btn-primary py-3 px-5 rounded-pill text-white">Đăng kí công dân</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="rounded">
                    <img src="{{ asset('website') }}/img/banner-img.jpg" class="img-fluid rounded w-100 rounded"
                        alt="Banner Image">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Banner End -->

<!-- Banner End -->


<!-- Latest News Start -->
<div class="container-fluid latest-news">
    <div class="container">
        <h2 class="mb-4">Latest News</h2>
        <div class="latest-news-carousel owl-carousel">
            {{-- @foreach ($latestPosts as $post)
                <div class="latest-news-item">
                    <div class="bg-light rounded">
                        <div class="rounded-top overflow-hidden">
                            <img src="{{ asset('storage') }}/{{ $post->image }}"
                                class="img-zoomin img-fluid rounded-top w-100" alt="{{ $post->title }}">
                        </div>
                        <div class="d-flex flex-column p-4">
                            <a href="{{ route('post.detail', $post->slug) }}" class="h4">
                                {{ \Illuminate\Support\Str::limit($post->title, 50) }}
                            </a>
                            <div class="d-flex justify-content-between">
                                <a href="#" class="small text-body link-hover">by
                                    {{ $post->author ?? 'Unknown' }}</a>
                                <small class="text-body d-block">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ $post->created_at }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach --}}
        </div>
    </div>
</div>

<!-- Latest News End -->


<!-- Most Populer News Start -->
<div class="container-fluid populer-news">
    <div class="container">
        <div class="tab-class mb-4">
            <div class="row g-4">
                <div class="col-lg-12 col-xl-12">
                    <h1 class="mb-4">Bài viết mới</h1>
                    <!-- Hiển thị Tabs -->
                    <div class="d-flex flex-column flex-md-row justify-content-md-between border-bottom mb-4">
                        {{-- <ul class="nav nav-pills d-flex flex-wrap text-center" id="myTab" role="tablist">
                            @foreach ($categories as $index => $category)
                                <li class="nav-item mb-2 me-2" role="presentation">
                                    <a class="nav-link rounded-pill {{ $index == 0 ? 'active' : '' }}"
                                        id="tab-{{ $category->slug }}" data-bs-toggle="pill"
                                        href="#content-{{ $category->slug }}" role="tab"
                                        aria-controls="content-{{ $category->slug }}"
                                        aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul> --}}
                    </div>

                    <!-- Hiển thị Nội dung -->
                    {{-- <div class="tab-content mb-4" id="myTabContent">
                        @foreach ($categories as $index => $category)
                            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                id="content-{{ $category->slug }}" role="tabpanel"
                                aria-labelledby="tab-{{ $category->slug }}">
                                <div class="row g-4">
                                    <!-- Bài viết chính lớn -->
                                    <div class="col-lg-8">
                                        @foreach ($category->newsArticles->take(1) as $post)
                                            <div class="position-relative rounded overflow-hidden">
                                                @if ($post->image)
                                                    <img src="{{ asset('storage/' . $post->image) }}"
                                                        class="img-zoomin img-fluid rounded w-100"
                                                        alt="{{ $post->title }}">
                                                @else
                                                    <img src="{{ asset('website/img/default-image.jpg') }}"
                                                        class="img-zoomin img-fluid rounded w-100"
                                                        alt="Default Image">
                                                @endif
                                                <div class="position-absolute text-white px-4 py-2 bg-primary rounded"
                                                    style="top: 20px; right: 20px;">{{ $category->name }}</div>
                                            </div>
                                            <div class="my-4">
                                                @if($post->slug)
                                                    <a href="{{ route('posts.show', $post->slug) }}"
                                                        class="h4">{{ $post->title }}</a>
                                                @else
                                                    <span class="h4">{{ $post->title }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <a href="#" class="text-dark link-hover me-3">
                                                    <i class="fa fa-clock"></i> {{ $post->read_time ?? '5' }} minute
                                                    read
                                                </a>
                                                <a href="#" class="text-dark link-hover me-3">
                                                    <i class="fa fa-eye"></i> {{ $post->views ?? 0 }} Views
                                                </a>
                                                <a href="#" class="text-dark link-hover me-3">
                                                    <i class="fa fa-comment-dots"></i>
                                                    {{ $post->comments_count ?? 0 }} Comments
                                                </a>
                                                <a href="#" class="text-dark link-hover">
                                                    <i class="fa fa-arrow-up"></i> {{ $post->shares ?? 0 }} Shares
                                                </a>
                                            </div>
                                            <p class="my-4">{!! Str::limit($post->content, 150) !!}</p>
                                        @endforeach
                                    </div>

                                    <!-- Bài viết nổi bật bên phải -->
                                    <div class="col-lg-4">
                                        <div class="row g-4">
                                            @foreach ($category->newsArticles->skip(1)->take(4) as $post)
                                                <div class="col-12">
                                                    <div class="row g-4 align-items-center">
                                                        <div class="col-5">
                                                            <div class="overflow-hidden rounded">
                                                                @if ($post->image)
                                                                    <img src="{{ asset('storage/' . $post->image) }}"
                                                                        class="img-zoomin img-fluid rounded w-100"
                                                                        alt="{{ $post->title }}">
                                                                @else
                                                                    <img src="{{ asset('website/img/default-image.jpg') }}"
                                                                        class="img-zoomin img-fluid rounded w-100"
                                                                        alt="Default Image">
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-7">
                                                            <div class="features-content d-flex flex-column">
                                                                <p class="text-uppercase mb-2">{{ $category->name }}
                                                                </p>
                                                                @if($post->slug)
                                                                    <a href="{{ route('posts.show', $post->slug) }}"
                                                                        class="h6">
                                                                        {{ Str::limit($post->title, 50) }}
                                                                    </a>
                                                                @else
                                                                    <span class="h6">{{ Str::limit($post->title, 50) }}</span>
                                                                @endif
                                                                <small class="text-body d-block">
                                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                                    {{ $post->published_at ? $post->published_at : '' }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div> --}}




                    <div class="border-bottom mb-4">
                        <h2 class="my-4">Vnxpress thời sự </h2>
                    </div>
                    <!-- Hiển thị Tin Tức từ RSS - Thời Sự -->
                    <div class="whats-carousel owl-carousel">
                        @foreach (collect($rssArticles['thoisu']) as $news)
                            <div class="whats-item">
                                <div class="bg-light rounded">
                                    <div class="rounded-top overflow-hidden">
                                        <img src="{{ $news['image'] ?: asset('website/img/default-image.jpg') }}"
                                            class="img-zoomin img-fluid rounded-top w-100"
                                            alt="{{ $news['title'] }}">
                                    </div>
                                    <div class="d-flex flex-column p-4">
                                        <a href="{{ $news['link'] }}" class="h4">{{ $news['title'] }}</a>
                                        <div class="d-flex justify-content-between">
                                            <a href="#" class="small text-body link-hover">By RSS Feed</a>
                                            <small class="text-body d-block">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                {{ date('M d, Y', strtotime($news['published_at'])) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- LifeStyle - Hiển Thị Bài Viết từ Thời Sự -->
                    <div class="mt-5 lifestyle">
                        <div class="border-bottom mb-4">
                            <h1 class="mb-4">Báo pháp luật</h1>
                        </div>
                        <div class="row g-4">
                            @foreach (collect($rssArticles['phapluat'])->take(10) as $news)
                                <div class="col-lg-6">
                                    <div class="lifestyle-item rounded">
                                        <img src="{{ $news['image'] ?: asset('website/img/default-image.jpg') }}"
                                            class="img-fluid w-100 rounded" alt="{{ $news['title'] }}">
                                        <div class="lifestyle-content">
                                            <div class="mt-auto">
                                                <a href="{{ $news['link'] }}" class="h4 text-white link-hover">
                                                    {{ Str::limit($news['title'], 60) }}
                                                </a>
                                                <div class="d-flex justify-content-between mt-4">
                                                    <a href="#" class="small text-white link-hover">By RSS
                                                        Feed</a>
                                                    <small class="text-white d-block">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        {{ date('M d, Y', strtotime($news['published_at'])) }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<!-- Most Populer News End -->
