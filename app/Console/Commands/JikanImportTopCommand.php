<?php

namespace App\Console\Commands;

use App\Services\JikanImportService;
use App\Services\JikanService;
use Illuminate\Console\Command;

class JikanImportTopCommand extends Command
{
    protected $signature = 'jikan:import-top
        {--count=50 : Number of anime to import}
        {--filter=bypopularity : Filter (bypopularity|airing|upcoming)}';

    protected $description = 'Import top anime from MyAnimeList via the Jikan API';

    public function handle(JikanService $jikan, JikanImportService $importer): int
    {
        $count  = (int) $this->option('count');
        $filter = $this->option('filter');
        $pages  = (int) ceil($count / 25);
        $all    = [];

        $this->info("Fetching top {$count} anime (filter: {$filter})…");

        for ($page = 1; $page <= $pages; $page++) {
            $results = $jikan->getTopAnime($page, $filter);
            $batch   = $results['data'] ?? [];
            $all     = array_merge($all, $batch);
            $this->info("  Page {$page}: " . count($batch) . ' results');
        }

        $all      = array_slice($all, 0, $count);
        $imported = $importer->importBulk($all);

        $this->info("Done. Imported {$imported} anime.");

        return self::SUCCESS;
    }
}
