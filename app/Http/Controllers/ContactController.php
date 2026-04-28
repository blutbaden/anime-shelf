<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // ── Admin ────────────────────────────────────────────────────────────────

    public function index()
    {
        $contacts = Contact::latest()->paginate(20);
        return view('admin.contacts.index', compact('contacts'));
    }

    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['is_read' => true]);
        return view('admin.contacts.show', compact('contact'));
    }

    public function destroy($id)
    {
        Contact::findOrFail($id)->delete();
        return redirect('/admin/contacts')->with('deleted_contact', 'Contact deleted.');
    }

    // ── Public ───────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|max:255',
            'body'    => 'required|min:10|max:2000',
        ]);

        Contact::create($validated);

        return back()->with('success', __('Your message has been sent. We\'ll get back to you soon.'));
    }
}
