@extends('backend.components.layout')

@section('title')
    Tạo mới bài viết
@endsection

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="#">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                         <a href="/admin">Trang chủ</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('birth-registrations.index') }}">Tạo mới bài viết</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Tạo mới bài viết</div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('posts.update', $post->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT') <!-- Thêm phương thức PUT cho yêu cầu cập nhật -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="title" class="form-label">Tiêu đề</label>
                                        <input type="text" id="title" name="title" class="form-control"
                                            value="{{ old('title', $post->title) }}" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="author" class="form-label">Tác giả</label>
                                        <input type="text" id="author" name="author" class="form-control"
                                            value="{{ old('author', $post->author) }}" required>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="content" class="form-label">Nội dung</label>
                                        <textarea id="content" name="content" class="form-control" rows="5" required>{{ old('content', $post->content) }}</textarea>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="image" class="form-label">Hình ảnh</label>
                                        <input type="file" id="image" name="image" class="form-control"
                                            accept="image/*">
                                        <small class="form-text text-muted">Chọn hình ảnh mới nếu bạn muốn thay đổi.</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Thể loại</label>
                                        <input type="text" id="category" name="category" class="form-control"
                                            value="{{ old('category', $post->category) }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="published_at" class="form-label">Ngày xuất bản</label>
                                        <input type="date" id="published_at" name="published_at" class="form-control"
                                            value="{{ old('published_at', $post->published_at) }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="is_published" class="form-label">Trạng thái xuất bản</label>
                                        <select id="is_published" name="is_published" class="form-select">
                                            <option value="1" {{ $post->is_published ? 'selected' : '' }}>Đã xuất bản
                                            </option>
                                            <option value="0" {{ !$post->is_published ? 'selected' : '' }}>Chưa xuất
                                                bản</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Thể loại</label>
                                        <select class="form-control" id="category" name="category_id" required>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                </div>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>

    {{-- <script>
        window.onload = function() {
            CKEDITOR.replace('content'); // Khởi tạo CKEditor cho textarea
        };
    </script> --}}
@endsection
