<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Quản lý Bài viết Tin tức</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li>Bài viết Tin tức</li>
            </ul>
        </div>

        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col">
                <form action="{{ route('posts.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="Tìm kiếm theo tiêu đề, nội dung, tác giả..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        <a href="{{ route('posts.create') }}" class="btn btn-primary">Tạo mới</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tiêu đề</th>
                            <th>Tác giả</th>
                            <th>Danh mục</th>
                            <th>Ngày xuất bản</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr>
                                <td>{{ $post->id }}</td>
                                <td>{{ $post->title }}</td>
                                <td>{{ $post->author }}</td>
                                <td>{{ $post->category->name ?? "" }}</td>
                                <td>{{ $post->published_at ? $post->published_at : 'Chưa xuất bản' }}
                                </td>
                                <td>{{ $post->is_published ? 'Đã xuất bản' : 'Chưa xuất bản' }}</td>
                                <td>
                                    <a href="{{ route('posts.edit', $post->id) }}"
                                        class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>
</div>

@section('scripts')
    <script>
        // JavaScript có thể thêm vào đây nếu cần
    </script>
@endsection
