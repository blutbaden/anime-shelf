<?php

namespace App\Console\Commands;

use App\Models\Anime;
use App\Services\JikanService;
use Illuminate\Console\Command;

class JikanSyncSeasonsCommand extends Command
{
    protected $signature   = 'jikan:sync-seasons {--force : Update even anime that already have a seasons value}';
    protected $description = 'Auto-detect seasons count for imported anime using Jikan relations API';

    public function handle(JikanService $jikan): int
    {
        $query = Anime::whereNotNull('mal_id');

        if (! $this->option('force')) {
            $query->whereNull('seasons');
        }

        $animes = $query->get(['id', 'mal_id', 'title', 'seasons']);

        if ($animes->isEmpty()) {
            $this->info('No anime to update. Use --force to re-sync all.');
            return 0;
        }

        $this->info("Syncing seasons for {$animes->count()} anime...");
        $bar = $this->output->createProgressBar($animes->count());
        $bar->start();

        $updated = 0;

        foreach ($animes as $anime) {
            $seasons = $jikan->fetchSeasonsCount($anime->mal_id);

            if ($seasons !== null) {
                $anime->update(['seasons' => $seasons]);
                $updated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. Updated {$updated} / {$animes->count()} anime.");

        return 0;
    }
}
