<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained('animes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('reviews')->cascadeOnDelete();
            $table->boolean('is_active')->default(false);
            // 1-10 scale (anime community standard)
            $table->tinyInteger('rate')->nullable();
            $table->text('body');
            $table->integer('upvote')->default(0);
            $table->integer('downvote')->default(0);
            // Auto-set if user has anime in watch_list with status watching/completed
            $table->boolean('is_verified_watcher')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
