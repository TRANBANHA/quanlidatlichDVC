<?php

namespace App\Http\Controllers\Admin;

use App\Models\About;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AboutController extends Controller
{
    /**
     * Display the about page.
     */
    public function index()
    {
        $about = About::getFirst();
        return view('backend.about.index', compact('about'));
    }

    /**
     * Update the about page.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mission' => 'nullable|string',
            'vision' => 'nullable|string',
            'values' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $about = About::getFirst();

        $data = [
            'tieu_de' => $validated['title'],
            'noi_dung' => $validated['content'],
            'su_menh' => $validated['mission'] ?? null,
            'tam_nhin' => $validated['vision'] ?? null,
            'gia_tri' => $validated['values'] ?? null,
            'so_dien_thoai' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'dia_chi' => $validated['address'] ?? null,
        ];

        if ($request->hasFile('image')) {
            if ($about->hinh_anh) {
                \Storage::disk('public')->delete($about->hinh_anh);
            }
            $data['hinh_anh'] = $request->file('image')->store('about', 'public');
        }

        if ($about->id) {
            $about->update($data);
        } else {
            About::create($data);
        }

        return redirect()->route('admin.about.index')->with('success', 'Thông tin giới thiệu đã được cập nhật thành công!');
    }
}

