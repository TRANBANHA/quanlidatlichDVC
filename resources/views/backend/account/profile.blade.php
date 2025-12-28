@extends('backend.components.layout')
@section('title', 'Thông tin tài khoản')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin tài khoản</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.account.update-profile') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="avatar-wrapper text-center">
                                    <img src="{{ $admin->hinh_anh ? asset($admin->hinh_anh) : asset('images/default-avatar.png') }}" 
                                         alt="Avatar" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                    <div class="mt-2">
                                        <label for="hinh_anh" class="btn btn-sm btn-primary">
                                            Đổi ảnh đại diện
                                        </label>
                                        <input type="file" id="hinh_anh" name="hinh_anh" class="d-none" accept="image/*">
                                    </div>
                                    @error('hinh_anh')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group mb-3">
                                    <label for="name">Họ tên</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $admin->ho_ten) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $admin->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="so_dien_thoai">Số điện thoại</label>
                                    <input type="text" class="form-control @error('so_dien_thoai') is-invalid @enderror" 
                                           id="so_dien_thoai" name="so_dien_thoai" value="{{ old('so_dien_thoai', $admin->so_dien_thoai) }}">
                                    @error('so_dien_thoai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Preview avatar before upload
    document.getElementById('hinh_anh').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.avatar-wrapper img').setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endpush
@endsection