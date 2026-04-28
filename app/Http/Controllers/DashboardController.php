<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\AuditLog;
use App\Models\Genre;
use App\Models\Review;
use App\Models\Studio;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'anime'          => Anime::count(),
                'genres'         => Genre::count(),
                'studios'        => Studio::count(),
                'users'          => User::count(),
                'subscribers'    => Subscriber::count(),
                'reviews'        => Review::count(),
                'pending_reviews'=> Review::where('is_active', false)->whereNull('parent_id')->count(),
                'total_views'    => Anime::sum('views'),
            ];
        });

        $topAnime = Cache::remember('top_anime_views', 300, fn () =>
            Anime::with('photo')->orderBy('views', 'desc')->limit(5)->get()
        );

        // Views per month (last 6 months) — approximate via created_at of views
        // User registrations per month
        $registrations = Cache::remember('registrations_chart', 300, fn () =>
            DB::table('users')
                ->selectRaw("TO_CHAR(created_at, 'Mon') as month, TO_CHAR(created_at, 'YYYY-MM') as ym, count(*) as total")
                ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy('ym', 'month')
                ->orderBy('ym')
                ->get()
        );

        $recentAuditLogs = AuditLog::with('user')->latest()->limit(10)->get();

        $pendingReviews = Review::where('is_active', false)->whereNull('parent_id')->count();

        return view('admin.dashboard.index', compact(
            'stats', 'topAnime', 'registrations', 'recentAuditLogs', 'pendingReviews'
        ));
    }
}
