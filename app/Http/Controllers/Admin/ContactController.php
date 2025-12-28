<?php

namespace App\Http\Controllers\Admin;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    /**
     * Display a listing of contacts.
     */
    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('ten', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('chu_de', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status);
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => Contact::count(),
            'new' => Contact::where('trang_thai', 'new')->count(),
            'read' => Contact::where('trang_thai', 'read')->count(),
            'replied' => Contact::where('trang_thai', 'replied')->count(),
        ];

        return view('backend.contacts.index', compact('contacts', 'stats'));
    }

    /**
     * Display the specified contact.
     */
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        
        // Đánh dấu đã đọc nếu chưa đọc
        if ($contact->trang_thai === 'new') {
            $contact->markAsRead();
        }

        return view('backend.contacts.show', compact('contact'));
    }

    /**
     * Update contact reply.
     */
    public function reply(Request $request, $id)
    {
        $validated = $request->validate([
            'reply' => 'required|string|max:5000',
        ]);

        $contact = Contact::findOrFail($id);
        $contact->markAsReplied($validated['reply']);

        return redirect()->route('admin.contacts.show', $id)->with('success', 'Phản hồi đã được gửi thành công!');
    }

    /**
     * Remove the specified contact.
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Liên hệ đã được xóa thành công!');
    }
}

