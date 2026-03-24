<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    protected $fillable = [
        'reportable_type',
        'reportable_id',
        'anon_session_id',
        'reason',
        'notes',
        'status',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // === Scopes ===

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
