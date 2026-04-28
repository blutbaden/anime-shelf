<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = [
        'anime_id', 'number', 'title', 'url', 'description', 'duration', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'number'    => 'integer',
        'duration'  => 'integer',
    ];

    public function anime(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Anime::class);
    }

    /**
     * Resolve the best embed URL from any video link.
     * Supports YouTube, Dailymotion, and plain video/iframe URLs.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        $url = $this->url;

        // YouTube
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m) ||
            preg_match('/[?&]v=([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0';
        }
        if (preg_match('/youtube(?:-nocookie)?\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0';
        }

        // Dailymotion
        if (preg_match('/dailymotion\.com\/video\/([a-zA-Z0-9]+)/', $url, $m)) {
            return 'https://www.dailymotion.com/embed/video/' . $m[1];
        }

        // Already an embed/iframe or direct video — use as-is
        return $url;
    }

    /**
     * Detect if the URL is a direct video file (mp4, webm, mkv …)
     */
    public function getIsDirectVideoAttribute(): bool
    {
        return (bool) preg_match('/\.(mp4|webm|mkv|ogg|ogv)(\?|$)/i', $this->url);
    }
}
