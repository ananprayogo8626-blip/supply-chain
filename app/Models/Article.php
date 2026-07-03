<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    /**
     * Nama tabel
     */
    protected $table = 'articles';

    /**
     * Mass Assignment
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'summary',
        'content',
        'thumbnail',
        'category',
        'status',
        'published_at',
        'views',
    ];

    /**
     * Casting data
     */
    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer',
    ];

    /**
     * Relasi ke User
     * Satu artikel dibuat oleh satu user (Admin)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope artikel yang sudah dipublikasikan
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'Published');
    }

    /**
     * Accessor URL thumbnail
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }

        return asset('images/default-news.png');
    }
}