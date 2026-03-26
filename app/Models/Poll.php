<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    protected $fillable = [
        'thread_id',
        'question',
        'expires_at',
        'is_closed',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_closed'  => 'boolean',
    ];

    // ===== Relationships =====

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    // ===== Helpers =====

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isAcceptingVotes(): bool
    {
        return ! $this->is_closed && ! $this->isExpired();
    }

    public function totalVotes(): int
    {
        return $this->options->sum('votes_count');
    }

    /**
     * Check if a given user/anon has already voted.
     */
    public function hasVoted(?int $userId, ?string $anonKey): bool
    {
        return $this->votes()
            ->when($userId,   fn ($q) => $q->where('user_id', $userId))
            ->when(!$userId,  fn ($q) => $q->where('anon_key', $anonKey))
            ->exists();
    }

    /**
     * Return the option_id that a given user/anon has voted for, or null.
     */
    public function votedOptionId(?int $userId, ?string $anonKey): ?int
    {
        $vote = $this->votes()
            ->when($userId,   fn ($q) => $q->where('user_id', $userId))
            ->when(!$userId,  fn ($q) => $q->where('anon_key', $anonKey))
            ->first();

        return $vote?->poll_option_id;
    }
}
