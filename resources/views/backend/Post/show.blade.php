@extends('backend.components.layout')
@section('title')
    Chi tiết bài viết
@endsection
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Chi tiết bài viết</h5>
                        <div>
                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Tiêu đề:</strong>
                                <p>{{ $post->title }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Slug:</strong>
                                <p>{{ $post->slug }}</p>
                            </div>
                        </div>
                        
                        @if($post->image)
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Hình ảnh:</strong>
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" 
                                         class="img-fluid" style="max-width: 500px;">
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($post->excerpt)
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Tóm tắt:</strong>
                                <p>{{ $post->excerpt }}</p>
                            </div>
                        </div>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Nội dung:</strong>
                                <div class="mt-2">
                                    {!! $post->content !!}
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Tác giả:</strong>
                                <p>{{ $post->author ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Trạng thái:</strong>
                                <p>
                                    @if($post->status === 'published')
                                        <span class="badge bg-success">Đã xuất bản</span>
                                    @else
                                        <span class="badge bg-secondary">Bản nháp</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-3">
                                <strong>Nổi bật:</strong>
                                <p>
                                    @if($post->is_featured)
                                        <span class="badge bg-warning">Có</span>
                                    @else
                                        <span class="badge bg-secondary">Không</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-3">
                                <strong>Lượt xem:</strong>
                                <p>{{ $post->views ?? 0 }}</p>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Ngày tạo:</strong>
                                <p>{{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Ngày cập nhật:</strong>
                                <p>{{ \Carbon\Carbon::parse($post->updated_at)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
