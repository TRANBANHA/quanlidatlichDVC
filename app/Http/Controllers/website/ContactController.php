<?php

namespace App\Http\Controllers\website;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    /**
     * Display the contact page.
     */
    public function index()
    {
        return view('website.contact.index');
    }

    /**
     * Store a newly created contact message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $data = [
            'ten' => $validated['name'],
            'email' => $validated['email'],
            'so_dien_thoai' => $validated['phone'] ?? null,
            'chu_de' => $validated['subject'] ?? null,
            'tin_nhan' => $validated['message'],
            'trang_thai' => 'new',
        ];

        Contact::create($data);

        return redirect()->back()->with('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.');
    }
}
