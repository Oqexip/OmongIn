<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use MustVerifyEmailTrait;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // === Role helpers ===

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModeratorOf(Board $board): bool
    {
        return $this->moderatedBoards()->where('board_id', $board->id)->exists();
    }

    public function hasModeratorRole(): bool
    {
        return $this->moderatedBoards()->exists();
    }

    // === Relationships ===

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function bookmarkedThreads(): BelongsToMany
    {
        return $this->belongsToMany(Thread::class, 'bookmarks')->withTimestamps();
    }

    public function hasBookmarked(Thread $thread): bool
    {
        return $this->bookmarks()->where('thread_id', $thread->id)->exists();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->orderByDesc('created_at');
    }

    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function moderatedBoards(): BelongsToMany
    {
        return $this->belongsToMany(Board::class, 'board_moderators')->withTimestamps();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
}
