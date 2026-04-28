<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\GenresController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JikanImportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\StudiosController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WatchHistoryController;
use App\Http\Controllers\WatchListController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

// ── Language switcher ────────────────────────────────────────────────────────
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// ── Sitemap ───────────────────────────────────────────────────────────────────
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');

// ── Home ─────────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ── Anime catalog ─────────────────────────────────────────────────────────────
Route::middleware(['throttle:browse'])->group(function () {
    Route::get('/anime', [AnimeController::class, 'animes'])->name('animes');
    Route::post('/anime', [AnimeController::class, 'animes'])->name('animes-filtered');
    Route::get('/anime/{id}', [AnimeController::class, 'anime'])->name('anime');
    Route::get('/genres', [GenresController::class, 'publicIndex'])->name('genres.public');
    Route::get('/studios', [StudiosController::class, 'studios'])->name('studios.public');
    Route::get('/studio/{id}', [StudiosController::class, 'studio'])->name('studio.public');
});

Route::get('/anime/search', [AnimeController::class, 'animes'])->name('search')->middleware('throttle:60,1');
Route::get('/anime/autocomplete', [AnimeController::class, 'autocomplete'])->name('anime.autocomplete')->middleware('throttle:60,1');

// ── Contact & subscribe ──────────────────────────────────────────────────────
Route::get('/contact', fn () => view('contact'))->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:10,1');
Route::post('/subscribe', [SubscriberController::class, 'store'])->name('subscribe')->middleware('throttle:10,1');
Route::get('/unsubscribe', [SubscriberController::class, 'unsubscribe'])->name('unsubscribe')->middleware('signed');

// ── OAuth ─────────────────────────────────────────────────────────────────────
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('oauth.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('oauth.callback');

// ── Auth-required ────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Favorites
    Route::get('/favorites', [FavoritesController::class, 'index'])->name('favorites');
    Route::post('/favorites', [FavoritesController::class, 'store'])->name('favorites.store');

    // Watch list
    Route::get('/watch-list', [WatchListController::class, 'index'])->name('watch-list');
    Route::post('/watch-list', [WatchListController::class, 'store'])->name('watch-list.store');
    Route::patch('/watch-list/progress', [WatchListController::class, 'updateProgress'])->name('watch-list.progress');

    // Watch history
    Route::get('/watch-history', [WatchHistoryController::class, 'index'])->name('watch-history');
    Route::delete('/watch-history/{animeId}', [WatchHistoryController::class, 'destroy'])->name('watch-history.destroy');

    // Reviews
    Route::post('/review', [ReviewsController::class, 'store'])->name('review')->middleware('throttle:20,1');
    Route::patch('/review/{id}', [ReviewsController::class, 'update'])->name('review.update');
    Route::delete('/review/{id}', [ReviewsController::class, 'destroy'])->name('review.destroy');
    Route::post('/review/reply', [ReviewsController::class, 'storeReply'])->name('review.reply')->middleware('throttle:30,1');
    Route::post('/review/vote/{id}', [ReviewsController::class, 'voteReview'])->name('vote-review')->middleware('throttle:30,1');

    // Profile
    Route::get('/account/profile', [UsersController::class, 'userProfile'])->name('profile');
    Route::patch('/account/profile/update', [UsersController::class, 'updateUser'])->name('update-user-profile')->middleware('throttle:10,1');
    Route::post('/account/profile/change-password', [UsersController::class, 'changePassword'])->name('update-password')->middleware('throttle:5,1');
    Route::delete('/account/profile', [UsersController::class, 'deleteAccount'])->name('account.delete')->middleware('throttle:5,1');
    Route::get('/account/stats', [UsersController::class, 'stats'])->name('stats');
    Route::post('/account/goal', [UsersController::class, 'setGoal'])->name('goal.set');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});

// ── Public user profiles ──────────────────────────────────────────────────────
Route::get('/user/{id}', [UsersController::class, 'publicProfile'])->name('user.profile');

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::middleware('admin')->group(function () {
    // Dashboard
    Route::resource('admin/dashboard', DashboardController::class)
        ->missing(fn () => Redirect::route('dashboard.index'));

    // Users
    Route::resource('admin/users', UsersController::class)
        ->missing(fn () => Redirect::route('users.index'));
    Route::patch('admin/users/{id}/toggle-status', [UsersController::class, 'toggleStatus'])->name('users.toggle-status');

    // Anime
    Route::resource('admin/animes', AnimeController::class)
        ->missing(fn () => Redirect::route('animes.index'));
    Route::post('admin/animes/bulk-destroy', [AnimeController::class, 'bulkDestroy'])->name('animes.bulk-destroy');
    Route::post('admin/animes/import-csv', [AnimeController::class, 'importCsv'])->name('animes.import-csv');

    // Studios
    Route::resource('admin/studios', StudiosController::class)
        ->missing(fn () => Redirect::route('studios.index'));
    Route::get('admin/studios/autocomplete', [StudiosController::class, 'autocomplete'])->name('studios.autocomplete');

    // Genres
    Route::resource('admin/genres', GenresController::class)
        ->missing(fn () => Redirect::route('genres.index'));

    // Tags
    Route::resource('admin/tags', TagsController::class)
        ->missing(fn () => Redirect::route('tags.index'));

    // Reviews
    Route::resource('admin/reviews', ReviewsController::class)
        ->except(['create', 'show'])
        ->missing(fn () => Redirect::route('reviews.index'));

    // Quotes
    Route::resource('admin/quotes', QuoteController::class)
        ->missing(fn () => Redirect::route('quotes.index'));

    // Contacts
    Route::resource('admin/contacts', ContactController::class)
        ->missing(fn () => Redirect::route('contacts.index'));

    // Subscribers
    Route::resource('admin/subscribers', SubscriberController::class)
        ->missing(fn () => Redirect::route('subscribers.index'));
    Route::post('admin/subscribers/newsletter', [SubscriberController::class, 'sendNewsletter'])->name('subscribers.newsletter');

    // Audit Logs
    Route::get('admin/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('admin/audit-logs/export/csv', [AuditLogController::class, 'export'])->name('audit-logs.export.csv');
    Route::get('admin/audit-logs/export/pdf', [AuditLogController::class, 'exportPdf'])->name('audit-logs.export.pdf');

    // Jikan import
    Route::get('admin/jikan', [JikanImportController::class, 'index'])->name('jikan.index');
    Route::post('admin/jikan/search', [JikanImportController::class, 'search'])->name('jikan.search');
    Route::post('admin/jikan/import', [JikanImportController::class, 'import'])->name('jikan.import');
    Route::post('admin/jikan/import-top', [JikanImportController::class, 'importTop'])->name('jikan.import-top');
});
