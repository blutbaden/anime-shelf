<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\AuditLog;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::with('anime')->latest()->paginate(20);
        return view('admin.quotes.index', compact('quotes'));
    }

    public function create()
    {
        $animes = Anime::select('id', 'title')->orderBy('title')->get();
        return view('admin.quotes.create', compact('animes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'anime_id'       => 'required|exists:animes,id',
            'character_name' => 'required|max:255',
            'body'           => 'required|max:1000',
        ]);

        Quote::create($validated);
        Cache::forget('daily_quote');
        AuditLog::record('quote.created', null, [], ['character' => $validated['character_name']]);

        return back()->with('success', 'Quote created successfully.');
    }

    public function edit($id)
    {
        $quote  = Quote::findOrFail($id);
        $animes = Anime::select('id', 'title')->orderBy('title')->get();
        return view('admin.quotes.edit', compact('quote', 'animes'));
    }

    public function update(Request $request, $id)
    {
        $quote     = Quote::findOrFail($id);
        $validated = $request->validate([
            'anime_id'       => 'required|exists:animes,id',
            'character_name' => 'required|max:255',
            'body'           => 'required|max:1000',
        ]);
        $quote->update($validated);
        Cache::forget('daily_quote');

        return back()->with('success', 'Quote updated successfully.');
    }

    public function destroy($id)
    {
        Quote::findOrFail($id)->delete();
        Cache::forget('daily_quote');

        return redirect('/admin/quotes')->with('deleted_quote', 'Quote deleted.');
    }
}
