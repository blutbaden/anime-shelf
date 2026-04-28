<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Anime indexes
        Schema::table('animes', function (Blueprint $table) {
            $table->index('views');
            $table->index('studio_id');
            $table->index('created_at');
            $table->index('type');
            $table->index('status');
            $table->index(['season', 'season_year']);
        });

        // Reviews composite indexes
        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['anime_id', 'is_active']);
            $table->index(['user_id', 'anime_id']);
        });

        // Watch list index
        Schema::table('watch_list', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
        });

        // Audit logs
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('animes', function (Blueprint $table) {
            $table->dropIndex(['views']);
            $table->dropIndex(['studio_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['season', 'season_year']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['anime_id', 'is_active']);
            $table->dropIndex(['user_id', 'anime_id']);
        });

        Schema::table('watch_list', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
