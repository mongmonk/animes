<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpisodeVideo extends Model
{
    protected $fillable = [
        'episode_id',
        'source',
        'url',
    ];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}
