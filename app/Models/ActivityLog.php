<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'type', 'description',
        'subject_type', 'subject_id', 'properties',
    ];

    protected $casts = ['properties' => 'array'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public static function log(string $type, string $description, $subject = null, array $properties = []): self
    {
        return static::create([
            'user_id'      => auth()->id(),
            'type'         => $type,
            'description'  => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->id,
            'properties'   => $properties ?: null,
        ]);
    }
}
