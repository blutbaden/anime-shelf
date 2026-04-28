<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'is_active', 'photo_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ─── Relationships ─────────────────────────────────────

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function photo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function socialAccounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function activityLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function watchGoals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WatchGoal::class);
    }

    // ─── Favorites ──────────────────────────────────────────

    public function favoriteAnime(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Anime::class, 'anime_user');
    }

    // ─── Watch List ─────────────────────────────────────────

    public function watchList(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Anime::class, 'watch_list')
                    ->withPivot('status', 'current_episode', 'score')
                    ->withTimestamps();
    }

    public function planToWatch(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Anime::class, 'watch_list')
                    ->withPivot('status', 'current_episode', 'score')
                    ->withTimestamps()
                    ->wherePivot('status', 'plan_to_watch');
    }

    public function currentlyWatching(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Anime::class, 'watch_list')
                    ->withPivot('status', 'current_episode', 'score')
                    ->withTimestamps()
                    ->wherePivot('status', 'watching');
    }

    public function completedAnime(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Anime::class, 'watch_list')
                    ->withPivot('status', 'current_episode', 'score')
                    ->withTimestamps()
                    ->wherePivot('status', 'completed');
    }

    public function onHoldAnime(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Anime::class, 'watch_list')
                    ->withPivot('status', 'current_episode', 'score')
                    ->withTimestamps()
                    ->wherePivot('status', 'on_hold');
    }

    public function droppedAnime(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Anime::class, 'watch_list')
                    ->withPivot('status', 'current_episode', 'score')
                    ->withTimestamps()
                    ->wherePivot('status', 'dropped');
    }

    // ─── Watch History ──────────────────────────────────────

    public function watchHistory(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Anime::class, 'watch_history')
                    ->withPivot('completed_at')
                    ->withTimestamps();
    }

    // ─── Recently Viewed ────────────────────────────────────

    public function recentlyViewed(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Anime::class, 'recently_viewed')
                    ->withPivot('viewed_at')
                    ->withTimestamps()
                    ->orderByPivot('viewed_at', 'desc');
    }

    // ─── Helpers ────────────────────────────────────────────

    public function watchGoalForYear(int $year): ?WatchGoal
    {
        return $this->watchGoals()->where('year', $year)->first();
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin' && $this->is_active;
    }
}
