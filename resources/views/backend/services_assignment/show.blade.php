@extends('backend.components.layout')
@section('title')
    {{ $config['seo']['title'] ?? '' }}
@endsection
@section('style')
    <style>
        .pb-2 {
            padding-bottom: 10px;
        }

        .w-100 {
            width: 100%;
        }

        .mr-2 {
            margin-right: 10px !important;
        }

        .mr-3 {
            margin-right: 30px !important;
        }

        strong {
            color: red;
        }

        .mb-2 {
            margin-bottom: 7px !important;
        }

        .pb-27 {
            padding-bottom: 27px !important;
        }

        .right {
            float: left;
        }

        .button {
            float: right;
            margin-right: 69%;
            margin-top: 20px;
        }

        .table-responsive {
            overflow-x: hidden;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <form action="{{ route('admin.users.destroy', $user->id) }}" class="form" method="post">
                                @csrf
                                @method('DELETE')
                                <div id="DataTables_Table_0_wrapper"
                                    class="dataTables_wrapper form-inline dt-bootstrap pb-27">
                                    <div class="col-sm-3">
                                        <div>Bạn có chắc chắn xóa tài khoản trên </div>
                                        <div>Một khi đã xóa thì không thể khôi phục được</div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="form-group col-sm-6 mb-2">
                                            <label>Tên người dùng <strong>(*)</strong> </label>
                                            <input type="text" class="form-control" name="name" disabled
                                                value="{{ old('name', $user->name ?? '') }}" style="width:100% !important">
                                            @error('name')
                                                <p style="color:red">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-6 mb-2">
                                            <label>Email <strong>(*)</strong> </label>
                                            <input type="text" class="form-control" name="email" disabled
                                                value="{{ old('email', $user->email ?? '') }}"
                                                style="width:100% !important">
                                            @error('email')
                                                <p style="color:red">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary button">Gửi đi</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
