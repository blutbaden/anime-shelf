<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_list', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('anime_id')->constrained('animes')->cascadeOnDelete();
            // plan_to_watch, watching, completed, on_hold, dropped
            $table->string('status', 20)->default('plan_to_watch');
            $table->smallInteger('current_episode')->unsigned()->default(0);
            // User's personal score 1-10 (optional)
            $table->tinyInteger('score')->unsigned()->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'anime_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_list');
    }
};
