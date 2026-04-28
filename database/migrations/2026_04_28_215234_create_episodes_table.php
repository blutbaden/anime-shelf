<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('number');
            $table->string('title')->nullable();
            $table->string('url');          // embed or direct video URL
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration')->nullable(); // minutes
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['anime_id', 'number']);
            $table->index(['anime_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
