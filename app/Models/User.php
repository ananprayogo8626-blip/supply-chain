<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'role',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke tabel articles
     * Satu user dapat membuat banyak artikel
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Relasi ke tabel watchlists
     * Satu user dapat memiliki banyak watchlist
     */
    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Cek apakah user adalah Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Cek apakah user adalah Admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah Analyst
     */
    public function isAnalyst(): bool
    {
        return $this->role === 'analyst';
    }

    /**
     * Cek apakah user adalah Viewer
     */
    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    /**
     * Cek apakah user adalah User
     */
    public function isUser(): bool
    {
        return in_array($this->role, ['user', 'viewer']);
    }

    /**
     * Cek apakah user memiliki akses admin (super_admin atau admin)
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    /**
     * Cek apakah user memiliki akses edit (super_admin, admin, atau analyst)
     */
    public function hasEditAccess(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'analyst']);
    }
}