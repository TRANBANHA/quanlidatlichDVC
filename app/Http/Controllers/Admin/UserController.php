<?php

namespace App\Http\Controllers\Admin;

use Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Region;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\AdminService;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function import(Request $request)
    {
        // Xác thực file
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        // Nhập dữ liệu từ file
        $file = $request->file('file');
        $data = Excel::toArray([], $file); // Nếu bạn sử dụng Maatwebsite Excel

        // Bỏ qua hàng đầu tiên (tiêu đề)
        $rows = array_slice($data[0], 1); // Lấy tất cả các hàng bắt đầu từ hàng thứ hai

        // Duyệt qua từng hàng dữ liệu
        foreach ($rows as $row) {
            // Kiểm tra nếu tên và email không rỗng
            if (!empty($row[0]) && !empty($row[1])) {
                // Tìm region tương ứng
                $input = $row[3]; // Ví dụ: "Tổ 1, Thôn 1"

                // Tách chuỗi trước và sau dấu phẩy
                list($block, $name) = array_map('trim', explode(',', $input));

                // Query từ bảng regions
                $region = Region::where('block', 'LIKE', '%' . $block . '%')
                    ->where('name', 'LIKE', '%' . $name . '%')
                    ->first();
                if ($region) { // Nếu tìm thấy region
                    // Kiểm tra xem email đã tồn tại chưa
                    if (!User::where('email', $row[1])->exists()) {
                        User::create([
                            'name' => $row[0], // Tên
                            'email' => $row[1], // Email
                            'password' => Hash::make("123456"), // Băm mật khẩu
                            'phone' => $row[2], // Số điện thoại
                            'region_id' => $region->id, // ID của region tìm được
                            'publish' => 1 // Trạng thái mặc định
                        ]);
                    } else {
                        // Xử lý khi email đã tồn tại
                        return redirect()->back()->with('error', 'Email đã tồn tại: ' . $row[1]);
                    }
                } else {
                    // Xử lý khi không tìm thấy phường/xã trong bảng regions
                    return redirect()->back()->with('error', 'Không tìm thấy phường/xã: ' . $row[4]);
                }
            } else {
                // Xử lý khi tên hoặc email rỗng
                return redirect()->back()->with('error', 'Hàng không hợp lệ: ' . implode(', ', $row));
            }
        }


        return redirect()->back()->with('success', 'Nhập người dùng thành công!');
    }
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('username')) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }
        $query->orderBy('created_at', 'asc');
        $listService = $query->paginate(10)->withQueryString();
        return view("backend.user.index", compact("listService"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regions = Region::all();
        return view("backend.user.create", compact( 'regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'phone' => 'required|string',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'publish' => $request->publish ?? 1,
            'region_id' => $request->region_id,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $regions = Region::all();
        // Lấy thông tin admin cần chỉnh sửa
        $user = User::findOrFail($id);

        // Trả về view edit với dữ liệu của admin
        return view('backend.user.edit', compact('user', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string',
            'phone' => 'required|string',
        ]);
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->publish = $request->publish ?? 1;
        $user->region_id = $request->region_id;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->phone = $request->phone;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $admin = User::findOrFail($id);
            $admin->delete();
            return redirect()->route('users.index')->with('success', 'Admin deleted successfully!');
        } catch (\Exception $e) {

        }
    }
}
