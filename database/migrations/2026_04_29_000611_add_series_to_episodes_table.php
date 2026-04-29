<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->unsignedSmallInteger('series')->default(1)->after('anime_id');

            // Drop old unique (anime_id, number) and replace with (anime_id, series, number)
            $table->dropUnique(['anime_id', 'number']);
            $table->unique(['anime_id', 'series', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropUnique(['anime_id', 'series', 'number']);
            $table->unique(['anime_id', 'number']);
            $table->dropColumn('series');
        });
    }
};
