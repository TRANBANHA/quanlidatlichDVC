<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use App\Models\ServiceField;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceFieldController extends Controller
{

    public function create($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        return view('backend.services.fields.create', compact('service'));
    }


    public function store(Request $request, $serviceId)
    {
        $request->merge([
            'bat_buoc' => $request->has('bat_buoc') ? true : false
        ]);

        $request->validate([
            'ten_truong' => 'required|string|max:255|regex:/^[a-z0-9_]+$/',
            'nhan_hien_thi' => 'required|string|max:255',
            'loai_truong' => 'required|in:text,textarea,file,date,select,number,email',
            'bat_buoc' => 'required|boolean',
            'placeholder' => 'nullable|string|max:255',
            'goi_y' => 'nullable|string|max:500',
            'thu_tu' => 'nullable|integer|min:0',
            'tuy_chon' => 'nullable|string',
        ]);


        $service = Service::findOrFail($serviceId);

        // Kiểm tra tên trường đã tồn tại chưa
        $existingField = ServiceField::where('dich_vu_id', $serviceId)
            ->where('ten_truong', $request->ten_truong)
            ->first();

        if ($existingField) {
            return back()->withErrors(['ten_truong' => 'Tên trường này đã tồn tại cho dịch vụ này.'])->withInput();
        }


        $tuyChon = null;
        if ($request->loai_truong === 'select' && $request->tuy_chon) {
            $options = array_filter(array_map('trim', explode("\n", $request->tuy_chon)));
            $tuyChon = json_encode($options);
        }


        $thuTu = $request->thu_tu ?? ServiceField::where('dich_vu_id', $serviceId)->max('thu_tu') + 1;

        ServiceField::create([
            'dich_vu_id' => $serviceId,
            'ten_truong' => $request->ten_truong,
            'nhan_hien_thi' => $request->nhan_hien_thi,
            'loai_truong' => $request->loai_truong,
            'bat_buoc' => $request->bat_buoc ?? false,
            'placeholder' => $request->placeholder,
            'goi_y' => $request->goi_y,
            'thu_tu' => $thuTu,
            'tuy_chon' => $tuyChon,
        ]);

        return redirect()->route('services.edit', $serviceId)
            ->with('success', 'Thêm trường form thành công!');
    }


    public function edit($serviceId, $fieldId)
    {
        $service = Service::findOrFail($serviceId);
        $field = ServiceField::where('dich_vu_id', $serviceId)
            ->findOrFail($fieldId);

        return view('backend.services.fields.edit', compact('service', 'field'));
    }


    public function update(Request $request, $serviceId, $fieldId)
    {
        // Merge giá trị mặc định cho checkbox nếu không có
        $request->merge([
            'bat_buoc' => $request->has('bat_buoc') ? true : false
        ]);

        $request->validate([
            'nhan_hien_thi' => 'required|string|max:255',
            'loai_truong' => 'required|in:text,textarea,file,date,select,number,email',
            'bat_buoc' => 'required|boolean',
            'placeholder' => 'nullable|string|max:255',
            'goi_y' => 'nullable|string|max:500',
            'thu_tu' => 'nullable|integer|min:0',
            'tuy_chon' => 'nullable|string',
        ]);

        $field = ServiceField::where('dich_vu_id', $serviceId)
            ->findOrFail($fieldId);


        $tuyChon = null;
        if ($request->loai_truong === 'select' && $request->tuy_chon) {
            $options = array_filter(array_map('trim', explode("\n", $request->tuy_chon)));
            $tuyChon = json_encode($options);
        }

        $field->update([
            'nhan_hien_thi' => $request->nhan_hien_thi,
            'loai_truong' => $request->loai_truong,
            'bat_buoc' => $request->bat_buoc ?? false,
            'placeholder' => $request->placeholder,
            'goi_y' => $request->goi_y,
            'thu_tu' => $request->thu_tu ?? $field->thu_tu,
            'tuy_chon' => $tuyChon,
        ]);

        return redirect()->route('services.edit', $serviceId)
            ->with('success', 'Cập nhật trường form thành công!');
    }

    public function destroy($serviceId, $fieldId)
    {
        $field = ServiceField::where('dich_vu_id', $serviceId)
            ->findOrFail($fieldId);

        $field->delete();

        return redirect()->route('services.edit', $serviceId)
            ->with('success', 'Xóa trường form thành công!');
    }
}
