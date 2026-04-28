<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterMail;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriberController extends Controller
{
    // ── Admin ────────────────────────────────────────────────────────────────

    public function index()
    {
        $subscribers = Subscriber::latest()->paginate(20);
        return view('admin.subscribers.index', compact('subscribers'));
    }

    public function destroy($id)
    {
        Subscriber::findOrFail($id)->delete();
        return redirect('/admin/subscribers')->with('deleted_subscriber', 'Subscriber removed.');
    }

    public function sendNewsletter(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|max:255',
            'body'    => 'required',
        ]);

        Subscriber::chunk(100, function ($subscribers) use ($validated) {
            foreach ($subscribers as $subscriber) {
                Mail::to($subscriber->email)->queue(new NewsletterMail($validated['subject'], $validated['body']));
            }
        });

        return back()->with('success', 'Newsletter queued for delivery.');
    }

    // ── Public ───────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);

        Subscriber::firstOrCreate(['email' => $request->email]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('You have successfully subscribed.'));
    }

    public function unsubscribe(Request $request)
    {
        $email = $request->query('email');

        if ($email) {
            Subscriber::where('email', $email)->delete();
        }

        return view('unsubscribed');
    }
}
