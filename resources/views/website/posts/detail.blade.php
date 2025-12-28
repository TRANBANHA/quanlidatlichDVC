@extends('website.components.layout');
@section('title')
    Chào mừng bạn đến với
@endsection

@section('content')
    <div class="container-fluid py-5">
        <div class="container py-5">
            <ol class="breadcrumb justify-content-start mb-4">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                <li class="breadcrumb-item active text-dark">Single Page</li>
            </ol>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <a href="#" class="h1 display-5">{{ $postslide->title }}</a>
                    </div>
                    <div class="position-relative rounded overflow-hidden mb-3">
                        <img src="{{ asset('storage') }}/{{ $postslide->image }}" class="img-zoomin img-fluid rounded w-100"
                            alt="{{ $postslide->title }}">
                        <div class="position-absolute text-white px-4 py-2 bg-primary" style="top: 20px; right: 20px;">
                            {{ $postslide->category->name }}
                        </div>
                    </div>
                    {{-- <div class="d-flex justify-content-between">
                        <a href="#" class="text-dark link-hover me-3"><i class="fa fa-clock"></i>
                            {{ \Carbon\Carbon::parse($postslide->published_at)->diffInMinutes() }} minute read</a>
                        <a href="#" class="text-dark link-hover me-3"><i class="fa fa-eye"></i>
                            {{ $postslide->views }} Views</a>
                        <a href="#" class="text-dark link-hover me-3"><i class="fa fa-comment-dots"></i>
                            {{ $postslide->comments_count }} Comment</a>
                        <a href="#" class="text-dark link-hover"><i class="fa fa-arrow-up"></i>
                            {{ $postslide->shares }} Share</a>
                    </div> --}}
                    <p class="my-4">{!! $postslide->content !!}</p>

                    @if ($relatedPosts->count() > 0)
                        <div class="bg-light rounded my-4 p-4">
                            <h4 class="mb-4">Bài viết liên quan</h4>
                            <div class="row g-4">
                                @foreach ($relatedPosts as $relatedPost)
                                    <!-- Giả định bạn có các bài viết liên quan -->
                                    <div class="col-lg-12">
                                        <div class="d-flex bg-white rounded">
                                            <img src="{{ asset('storage') }}/{{ $relatedPost->image }}"
                                                class="img-fluid rounded col-lg-5" alt="{{ $relatedPost->title }}">
                                            <div class="ms-3">
                                                <a href="{{ route('post.detail', $relatedPost->slug) }}"
                                                    class="h5 mb-2">{{ $relatedPost->title }}</a>
                                                <p class="text-dark mt-3 mb-0 me-3"><i class="fa fa-clock"></i>
                                                    {{ \Carbon\Carbon::parse($relatedPost->published_at)->diffInMinutes() }}
                                                    minute read</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($comments->count() > 0)
                        <div class="bg-light rounded p-4">

                            <h4 class="mb-4">Comments</h4>


                            <div class="p-4 bg-white rounded mb-0">
                                <div class="row g-4">
                                    @foreach ($comments as $item)
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between">
                                                <h5>{{ $item->author_name }}</h5>

                                            </div>
                                            <small class="text-body d-block mb-3"><i class="fas fa-calendar-alt me-1"></i>
                                                {{ $item->author_email }}</small>
                                            <p class="mb-0">{{ $item->content }}
                                            </p>
                                        </div>
                                    @endforeach

                                </div>
                            </div>

                        </div>
                    @endif

                    <div class="bg-light rounded p-4 my-4">
                        <h4 class="mb-4">Leave A Comment</h4>
                        <form action="{{ route('website.postComment') }}" method="POST">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $postslide->id }}"> <!-- ID của bài viết -->
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <input type="text" class="form-control py-3" placeholder="Full Name" name="username"
                                        value="{{ Auth::user()->name ?? '' }}" required>
                                </div>
                                <div class="col-lg-6">
                                    <input type="email" class="form-control py-3" value="{{ Auth::user()->email ?? '' }}"
                                        name="email" placeholder="Email Address" required>
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control" name="content" cols="30" rows="7" placeholder="Write Your Comment Here"
                                        required></textarea>
                                </div>
                                <div class="col-12">
                                    <button class="form-control btn btn-primary py-3" type="submit">Submit Now</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="p-3 rounded border">
                                <h4 class="my-4">Thời sự (Vnexpress)</h4>
                                <div class="row g-4">
                                    <div class="col-12">
                                        @foreach (array_slice($rssArticles, 7, 7) as $index => $rssPost)
                                            <div class="row g-4 align-items-center features-item mb-3">
                                                <div class="col-4">
                                                    <div class="rounded-circle position-relative">
                                                        <div class="overflow-hidden rounded-circle">
                                                            <img src="{{ $rssPost['image'] }}"
                                                                class="img-zoomin img-fluid rounded-circle w-100"
                                                                alt="{{ $rssPost['title'] }}">
                                                        </div>
                                                        <span
                                                            class="rounded-circle border border-2 border-white bg-primary btn-sm-square text-white position-absolute"
                                                            style="top: 10%; right: -10px;">{{ $index + 1 }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-8">
                                                    <div class="features-content d-flex flex-column">
                                                        <p class="text-uppercase mb-2">Thời sự</p>
                                                        <a href="{{ $rssPost['link'] }}" target="_blank" class="h6">
                                                            {{ $rssPost['title'] }}
                                                        </a>
                                                        <small class="text-body d-block">
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            {{ date('d M Y, H:i', strtotime($rssPost['published_at'])) }}
                                                        </small>
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
        </div>
    </div>
@endsection
