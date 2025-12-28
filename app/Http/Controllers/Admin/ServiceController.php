<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    // Hiển thị danh sách các danh mục
    public function index(Request $request)
    {
        $search = $request->get('search');
        $services = Service::when($search, function ($query, $search) {
            return $query->where('ten_dich_vu', 'like', "%$search%");
        })->orderBy('id', 'desc')->paginate(10);

        return view('backend.services.index', compact('services'));
    }

    public function create()
    {
        return view('backend.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_dich_vu' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
        ]);

        Service::create($request->only(['ten_dich_vu', 'mo_ta']));

        return redirect()->route('services.index')->with('success', 'Thêm dịch vụ thành công!');
    }


    public function edit($id)
    {
        $service = Service::with('serviceFields')->findOrFail($id);
        return view('backend.services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_dich_vu' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
        ]);

        $service = Service::findOrFail($id);
        $service->update($request->only(['ten_dich_vu', 'mo_ta']));

        return redirect()->route('services.index')->with('success', 'Cập nhật dịch vụ thành công!');
    }

    public function destroy($id)
    {
        $category = Service::findOrFail($id);
        $category->delete();

        return redirect()->route('services.index')->with('success', 'Danh mục đã bị xóa!');
    }

}
