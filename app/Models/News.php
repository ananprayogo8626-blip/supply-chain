<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;
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
        'image',
        'sentiment',
        'sentiment_score',
        'author',
        'content',
    ];

    protected $casts = [
        'impact_score' => 'integer',
        'sentiment_score' => 'integer',
        'published_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}