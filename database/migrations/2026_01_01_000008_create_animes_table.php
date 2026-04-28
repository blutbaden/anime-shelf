<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained('studios')->onDelete('cascade');
            $table->integer('photo_id')->unsigned()->nullable();
            $table->string('title');
            $table->string('title_japanese')->nullable();
            $table->string('slug')->nullable();
            // TV, Movie, OVA, ONA, Special
            $table->string('type', 20)->default('TV');
            // Total episodes (0 = unknown/ongoing)
            $table->integer('episodes')->default(0);
            // Duration per episode in minutes
            $table->smallInteger('episode_duration')->nullable();
            // airing, finished, upcoming
            $table->string('status', 20)->default('finished');
            // Winter, Spring, Summer, Fall
            $table->string('season', 10)->nullable();
            $table->smallInteger('season_year')->nullable();
            // Manga, Light Novel, Visual Novel, Original, Game, Web Manga, Other
            $table->string('source', 30)->nullable();
            $table->text('synopsis')->nullable();
            $table->string('trailer_url', 500)->nullable();
            // MyAnimeList ID for Jikan sync
            $table->integer('mal_id')->unsigned()->nullable()->unique();
            $table->date('aired_from')->nullable();
            $table->date('aired_to')->nullable();
            // G, PG, PG-13, R, R+
            $table->string('rating', 20)->nullable();
            $table->integer('views')->default(0);
            $table->string('language', 50)->nullable()->default('Japanese');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animes');
    }
};
