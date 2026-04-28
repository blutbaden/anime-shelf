<?php

use App\Http\Controllers\Api\V1\AnimeApiController;
use App\Http\Controllers\Api\V1\GenreApiController;
use App\Http\Controllers\Api\V1\ReviewApiController;
use App\Http\Controllers\Api\V1\StudioApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->middleware('throttle:60,1')->group(function () {

    // Anime
    Route::get('/anime', [AnimeApiController::class, 'index'])->name('anime.index');
    Route::get('/anime/{slug}', [AnimeApiController::class, 'show'])->name('anime.show');
    Route::get('/anime/{id}/related', [AnimeApiController::class, 'related'])->name('anime.related');
    Route::get('/anime/{animeId}/reviews', [ReviewApiController::class, 'index'])->name('anime.reviews');

    // Studios
    Route::get('/studios', [StudioApiController::class, 'index'])->name('studios.index');
    Route::get('/studios/{id}', [StudioApiController::class, 'show'])->name('studios.show');

    // Genres
    Route::get('/genres', [GenreApiController::class, 'index'])->name('genres.index');
    Route::get('/genres/{id}', [GenreApiController::class, 'show'])->name('genres.show');

    // Authenticated user
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', fn (Request $request) => $request->user())->name('user');
    });
});
