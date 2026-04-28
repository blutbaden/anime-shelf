<?php

namespace App\Console\Commands;

use App\Models\Anime;
use App\Services\JikanImportService;
use App\Services\JikanService;
use Illuminate\Console\Command;

class JikanSyncCommand extends Command
{
    protected $signature = 'jikan:sync {--limit=100 : Max anime to sync}';
    protected $description = 'Sync existing anime status/episodes from MyAnimeList via Jikan';

    public function handle(JikanService $jikan, JikanImportService $importer): int
    {
        $limit  = (int) $this->option('limit');
        $animes = Anime::whereNotNull('mal_id')->limit($limit)->get();

        $this->info("Syncing {$animes->count()} anime…");
        $bar = $this->output->createProgressBar($animes->count());

        foreach ($animes as $anime) {
            $data = $jikan->getAnime($anime->mal_id);
            if (! empty($data['data'])) {
                $importer->importAnime($data['data']);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sync complete.');

        return self::SUCCESS;
    }
}
