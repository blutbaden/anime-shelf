<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Shounen', 'Shoujo', 'Seinen', 'Josei',
            'Isekai', 'Harem', 'Reverse Harem',
            'School', 'Vampire', 'Demons', 'Gods',
            'Martial Arts', 'Samurai', 'Ninja',
            'Space', 'Cyberpunk', 'Dystopia',
            'Time Travel', 'Reincarnation',
            'Game', 'Virtual Reality',
            'Parody', 'Gag Humor',
            'Super Power', 'Mahou Shoujo',
            'Yakuza', 'Police',
        ];

        foreach ($tags as $name) {
            Tag::firstOrCreate(['name' => $name]);
        }
    }
}
