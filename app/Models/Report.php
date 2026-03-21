<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    protected $fillable = [
        'reportable_type',
        'reportable_id',
        'anon_session_id',
        'reason',
        'notes',
    ];

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }
}
