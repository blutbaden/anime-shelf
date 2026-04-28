<?php

namespace App\Console\Commands;

use App\Services\JikanImportService;
use App\Services\JikanService;
use Illuminate\Console\Command;

class JikanImportSeasonCommand extends Command
{
    protected $signature = 'jikan:import-season
        {--year= : Year (defaults to current year)}
        {--season= : Season: winter|spring|summer|fall (defaults to current season)}
        {--past=0 : Also import N previous seasons}';

    protected $description = 'Import anime from a specific season (or multiple past seasons) via the Jikan API';

    private const SEASONS = ['winter', 'spring', 'summer', 'fall'];

    public function handle(JikanService $jikan, JikanImportService $importer): int
    {
        $year   = (int) ($this->option('year') ?: date('Y'));
        $season = $this->option('season') ?: $this->currentSeason();
        $past   = (int) $this->option('past');

        $targets = $this->buildSeasonList($year, $season, $past);

        foreach ($targets as [$y, $s]) {
            $this->info("Importing {$s} {$y}…");

            $results = $jikan->getSeasonAnime($y, $s);
            $data    = $results['data'] ?? [];

            if (empty($data)) {
                $this->warn("  No results returned.");
                continue;
            }

            $imported = $importer->importBulk($data);
            $this->info("  Imported {$imported} anime.");
        }

        $this->info('Done.');

        return self::SUCCESS;
    }

    private function currentSeason(): string
    {
        $month = (int) date('n');

        return match (true) {
            $month <= 3  => 'winter',
            $month <= 6  => 'spring',
            $month <= 9  => 'summer',
            default      => 'fall',
        };
    }

    private function buildSeasonList(int $year, string $season, int $past): array
    {
        $list  = [[$year, $season]];
        $idx   = array_search($season, self::SEASONS);

        for ($i = 0; $i < $past; $i++) {
            $idx--;
            if ($idx < 0) {
                $idx = 3;
                $year--;
            }
            $list[] = [$year, self::SEASONS[$idx]];
        }

        return $list;
    }
}
