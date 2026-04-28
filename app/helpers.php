<?php

/**
 * Return the percentage (0-100) for a given rating score in a distribution array.
 */
function getAverageRate($rates, int $number): float
{
    if ($rates) {
        $rate = $rates->where('rate', $number)->first();
        if ($rate) {
            return round($rate->average, 2);
        }
    }
    return 0;
}

/**
 * Return a Tailwind width class based on the rating percentage for distribution bars.
 */
function getAverageRateClassByPercentage($rates, int $number): string
{
    $rate = floor(getAverageRate($rates, $number));

    return match (true) {
        $rate <= 0  => 'w-0',
        $rate <= 10 => 'w-1/12',
        $rate <= 20 => 'w-2/12',
        $rate <= 25 => 'w-3/12',
        $rate <= 35 => 'w-4/12',
        $rate <= 42 => 'w-5/12',
        $rate <= 50 => 'w-6/12',
        $rate <= 58 => 'w-7/12',
        $rate <= 66 => 'w-8/12',
        $rate <= 75 => 'w-9/12',
        $rate <= 83 => 'w-10/12',
        $rate <= 91 => 'w-11/12',
        default     => 'w-full',
    };
}

/**
 * Format episode count display (e.g. "12 eps", "? eps" for unknown).
 */
function formatEpisodes(?int $episodes): string
{
    if ($episodes === null || $episodes === 0) {
        return '? eps';
    }
    return $episodes . ' ep' . ($episodes > 1 ? 's' : '');
}

/**
 * Format episode duration display (e.g. "24 min").
 */
function formatDuration(?int $minutes): string
{
    if (! $minutes) {
        return '–';
    }
    if ($minutes >= 60) {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return $m > 0 ? "{$h}h {$m}min" : "{$h}h";
    }
    return "{$minutes} min";
}
