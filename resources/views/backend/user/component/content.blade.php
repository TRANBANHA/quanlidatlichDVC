<style>
    input[type="file"] {
        display: block;
        margin: 10px 0;
        padding: 10px;
        border: 1px solid #007bff;
        border-radius: 4px;
        width: 100%;
        font-size: 16px;
    }

    button {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 10px 15px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #0056b3;
    }
</style>
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
                    <a href="{{ route('quantri.index') }}">Quản lí tài khoản </a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Danh sách Tài khoản</div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.index') }}" method="GET"
                            class="d-flex align-items-center gap-3 p-2 rounded">
                            <div class="flex-grow-1">
                                <input type="text" id="username" name="username" class="form-control"
                                    placeholder="Username" value="{{ request('username') }}">
                            </div>
                            <div class="flex-grow-1">
                                <input type="text" id="address" name="address" class="form-control"
                                    placeholder="Address" value="{{ request('address') }}">
                            </div>
                            <div class="flex-grow-1">
                                <input type="text" id="phone_number" name="phone_number" class="form-control"
                                    placeholder="Phone Number" value="{{ request('phone_number') }}">
                            </div>
                            <button type="submit" class="btn btn-warning">Search</button>
                            <a class="btn btn-primary" href="{{ route('users.create') }}">Thêm Tài khoản</a>
                            <a class="btn btn-secondary" href="{{ route('users.index') }}">Reset</a>
                        </form>
                        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data"
                            class="d-flex justify-content-end" id="importForm">
                            @csrf
                            <input type="file" name="file" required id="importExcell" style="display: none;">
                            <button type="button" id="importButton" class="btn btn-info btn-sm">
                                <i class="fas fa-file-import"></i> Import file
                            </button>
                        </form>
                        <script>
                            document.getElementById('importButton').addEventListener('click', function() {
                                document.getElementById('importExcell').click();
                            });

                            document.getElementById('importExcell').addEventListener('change', function() {
                                if (this.files.length > 0) { // Kiểm tra nếu có file được chọn
                                    document.getElementById('importForm').submit(); // Gửi biểu mẫu
                                } else {
                                    alert('Vui lòng chọn một file trước khi nhập!');
                                }
                            });
                        </script>
                        <table class="table mt-3 border">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th scope="col">Username</th>
                                    <th scope="col" width="20%">email</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Phone Number</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listService as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td width="20%">{{ $item->email }}</td>
                                        <td>{{ $item->region->name }} - {{ $item->region->block }}</td>
                                        <td>{{ $item->phone }}</td>
                                        <td class="d-flex" style="column-gap: 20px">
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('users.edit', $item->id) }}"
                                                    class="btn btn-sm btn-warning"
                                                    style="align-items: center;display: flex"><i
                                                        class="fa-solid fa-pen-to-square"></i></a>
                                            </div>
                                            <form action="{{ route('users.destroy', $item->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i
                                                        class="fa-solid fa-trash"></i></button>
                                            </form>
                                            <button type="button" class="btn btn-info btn-sm btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#modal-{{ $item->id }}">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <!-- Modal for Detailed View -->
                                            <div class="modal fade" id="modal-{{ $item->id }}" tabindex="-1"
                                                aria-labelledby="modalLabel-{{ $item->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="modalLabel-{{ $item->id }}">
                                                                Chi tiết thông tin người thân</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>STT</th>
                                                                        <th scope="col">Full Name</th>
                                                                        <th scope="col">Birth Date</th>
                                                                        <th scope="col">Email</th>
                                                                        <th scope="col">Gender</th>
                                                                        <th scope="col">Residence</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($item->citizens()->where("residence_status", 1)->get() as $index => $item)
                                                                        {{-- Lặp qua danh sách các bản ghi --}}
                                                                        <tr>
                                                                            <td>{{ $index + 1 }}</td>
                                                                            <td>{{ $item->full_name }}</td>
                                                                            <td>{{ $item->birth_date }}</td>
                                                                            <td>{{ $item->email }}</td>
                                                                            <td>{{ $item->gender }}</td>
                                                                            <td>{{ $item->residence }}</td>
                                                                        </tr>
                                                                    @empty {{-- Nếu danh sách trống --}}
                                                                        <tr>
                                                                            <td colspan="17" class="text-center">
                                                                                Không có dữ liệu</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                    </div>
                    </td>
                    </tr>
                    @endforeach
                    </tbody>
                    </table>
                    {{ $listService->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
</div>
