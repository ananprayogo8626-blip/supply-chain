<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Watchlist extends Model
{
    use SoftDeletes;
    /**
     * Nama tabel
     */
    protected $table = 'watchlists';

    /**
     * Mass Assignment
     */
    protected $fillable = [
        'user_id',
        'country_id',
        'company_name',
        'industry',
        'priority',
        'status',
        'is_active',
        'notes',
    ];

    /**
     * Casting data
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Country
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Status aktif
     */
    public function getStatusBadgeAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}