<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    protected $fillable = [
        'anime_id',
        'title',
        'slug',
        'episode_number',
        'release_date',
    ];

    public function getVideoEmbedUrlAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return $this->videos()->first()?->url;
    }

    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class);
    }

    public function videos()
    {
        return $this->hasMany(EpisodeVideo::class);
    }

    public function downloads()
    {
        return $this->hasMany(EpisodeDownload::class);
    }
}
