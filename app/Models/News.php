<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'country_id',
        'title',
        'source',
        'category',
        'url',
        'impact_score',
        'summary',
        'published_at',
    ];

    protected $casts = [
        'impact_score' => 'integer',
        'published_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}