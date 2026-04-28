<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchGoal extends Model
{
    protected $fillable = ['user_id', 'year', 'goal'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
