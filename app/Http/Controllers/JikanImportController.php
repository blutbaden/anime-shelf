<?php

namespace App\Http\Controllers;

use App\Services\JikanImportService;
use App\Services\JikanService;
use Illuminate\Http\Request;

class JikanImportController extends Controller
{
    public function __construct(
        private JikanService $jikan,
        private JikanImportService $importer,
    ) {}

    public function index()
    {
        return view('admin.jikan-import.index');
    }

    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2|max:100']);

        $results = $this->jikan->search($request->q, $request->get('page', 1));

        return response()->json($results);
    }

    public function import(Request $request)
    {
        $request->validate([
            'mal_ids'   => 'required|array|max:25',
            'mal_ids.*' => 'integer|min:1',
        ]);

        $imported = 0;

        foreach ($request->mal_ids as $malId) {
            $data = $this->jikan->getAnime((int) $malId);
            if (! empty($data['data'])) {
                $this->importer->importAnime($data['data']);
                $imported++;
            }
        }

        return response()->json([
            'success' => true,
            'imported' => $imported,
            'message' => "{$imported} anime imported successfully.",
        ]);
    }

    public function importTop(Request $request)
    {
        $request->validate([
            'count'  => 'nullable|integer|min:1|max:100',
            'filter' => 'nullable|in:bypopularity,airing,upcoming',
        ]);

        $count  = (int) $request->get('count', 25);
        $filter = $request->get('filter', 'bypopularity');
        $pages  = (int) ceil($count / 25);
        $all    = [];

        for ($page = 1; $page <= $pages; $page++) {
            $results = $this->jikan->getTopAnime($page, $filter);
            $all     = array_merge($all, $results['data'] ?? []);
        }

        $all      = array_slice($all, 0, $count);
        $imported = $this->importer->importBulk($all);

        return response()->json([
            'success'  => true,
            'imported' => $imported,
            'message'  => "{$imported} anime imported from top list.",
        ]);
    }
}
