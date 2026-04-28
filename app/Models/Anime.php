<?php

namespace App\Models;

use App\Http\Traits\AnimeTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{
    use HasFactory, Sluggable, AnimeTrait;

    protected $fillable = [
        'title', 'title_japanese', 'type', 'episodes', 'seasons', 'episode_duration',
        'status', 'season', 'season_year', 'source', 'synopsis',
        'trailer_url', 'mal_id', 'aired_from', 'aired_to', 'rating',
        'language', 'meta_title', 'meta_description',
        'studio_id', 'photo_id',
    ];

    protected $guarded = ['views'];

    protected $casts = [
        'aired_from'   => 'date',
        'aired_to'     => 'date',
        'season_year'  => 'integer',
        'episodes'     => 'integer',
    ];

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'title']];
    }

    // ─── Relationships ─────────────────────────────────────

    public function photo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    public function studio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function genres(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function tags(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function quotes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function episodes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Episode::class)->orderBy('number');
    }

    /** Favorites pivot */
    public function favoritedByUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'anime_user');
    }

    public function watchListUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'watch_list')
                    ->withPivot('status', 'current_episode', 'score')
                    ->withTimestamps();
    }

    public function watchHistoryUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'watch_history')
                    ->withPivot('completed_at');
    }

    public function recentlyViewedUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'recently_viewed')
                    ->withPivot('viewed_at');
    }

    // ─── Computed Attributes ────────────────────────────────

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->where('is_active', true)->avg('rate') ?? 0, 1);
    }

    // ─── Scopes ─────────────────────────────────────────────

    public function scopeSortBy($query, string $sort)
    {
        return match ($sort) {
            'title'   => $query->orderBy('title'),
            'newest'  => $query->orderBy('created_at', 'desc'),
            'oldest'  => $query->orderBy('created_at'),
            'popular' => $query->orderBy('views', 'desc'),
            'rating'  => $query->orderByRaw('(SELECT AVG(rate) FROM reviews WHERE reviews.anime_id = animes.id AND reviews.is_active = true) DESC NULLS LAST'),
            default   => $query->orderBy('views', 'desc'),
        };
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOfSeason($query, string $season, int $year)
    {
        return $query->where('season', $season)->where('season_year', $year);
    }
}
