<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModeratorInvitation extends Model
{
    protected $fillable = [
        'board_id',
        'user_id',
        'invited_by',
        'status',
    ];

    // === Relationships ===

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // === Status helpers ===

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    // === Scopes ===

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForBoard($query, int $boardId)
    {
        return $query->where('board_id', $boardId);
    }
}
