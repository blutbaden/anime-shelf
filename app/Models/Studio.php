<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'name', 'description', 'photo_id',
        'founded_year', 'website', 'headquarters',
    ];

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'name']];
    }

    public function photo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    public function animes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Anime::class);
    }
}
