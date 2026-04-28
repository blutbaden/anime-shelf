<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            ['name' => 'Action',       'description' => 'Fast-paced series with combat and physical feats.'],
            ['name' => 'Adventure',    'description' => 'Journeys, exploration and discovery.'],
            ['name' => 'Comedy',       'description' => 'Humour-driven stories and lighthearted situations.'],
            ['name' => 'Drama',        'description' => 'Emotional and character-driven narratives.'],
            ['name' => 'Fantasy',      'description' => 'Magical worlds, mythical creatures and epic quests.'],
            ['name' => 'Horror',       'description' => 'Fear, suspense and dark supernatural themes.'],
            ['name' => 'Mystery',      'description' => 'Detective work, puzzles and unsolved enigmas.'],
            ['name' => 'Romance',      'description' => 'Love stories and relationship dynamics.'],
            ['name' => 'Sci-Fi',       'description' => 'Science, technology and futuristic concepts.'],
            ['name' => 'Slice of Life','description' => 'Everyday life and realistic character moments.'],
            ['name' => 'Sports',       'description' => 'Athletic competitions and team dynamics.'],
            ['name' => 'Supernatural', 'description' => 'Ghosts, demons and otherworldly powers.'],
            ['name' => 'Thriller',     'description' => 'High-stakes suspense and psychological tension.'],
            ['name' => 'Mecha',        'description' => 'Giant robots and mechanical suits.'],
            ['name' => 'Music',        'description' => 'Bands, idols and musical performances.'],
            ['name' => 'Historical',   'description' => 'Set in historical periods or inspired by real events.'],
            ['name' => 'Military',     'description' => 'War, strategy and armed conflicts.'],
            ['name' => 'Psychological','description' => 'Mind games, identity and complex mental states.'],
            ['name' => 'Ecchi',        'description' => 'Suggestive and risqué humour.'],
            ['name' => 'Magic',        'description' => 'Spells, witches and magical academies.'],
        ];

        foreach ($genres as $genre) {
            Genre::firstOrCreate(['name' => $genre['name']], $genre);
        }
    }
}
