<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Concerns\Votable;

class Thread extends Model
{
    use HasFactory, SoftDeletes;
    use Votable;

    protected $fillable = [
        'board_id',
        'category_id',
        'anon_session_id',
        'user_id',
        'title',
        'content',
        'edited_at',
        'score',
        'is_pinned',
        'is_nsfw',
        'is_spoiler',
    ];

    protected $casts = [
        'board_id'        => 'integer',
        'user_id'         => 'integer',
        'anon_session_id' => 'integer',
        'score'           => 'integer',
        'comment_count'   => 'integer',
        'is_pinned'       => 'boolean',
        'is_nsfw'         => 'boolean',
        'is_spoiler'      => 'boolean',
        'edited_at'       => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    // ===== Relationships =====

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function anon(): BelongsTo
    {
        return $this->belongsTo(AnonSession::class, 'anon_session_id');
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    // ===== Ownership & edit permissions =====

    public function isOwnedByRequest(Request $request): bool
    {
        if ($this->user_id) {
            return optional($request->user())->id === $this->user_id;
        }

        $anonId = (int) ($request->attributes->get('anon_id') ?? session('anon_id'));

        return $anonId === (int) $this->anon_session_id;
    }

    public function canEditNow(): bool
    {
        return $this->created_at && $this->created_at->gt(now()->subMinutes(15));
    }

    // ===== Voting & score =====

    public function recalcScore(): void
    {
        $this->score = (int) $this->votes()->sum('value');
        $this->saveQuietly();
    }

    // ===== Search =====

    public static function supportsFullText(): bool
    {
        $connection = config('database.default');
        $driver     = config("database.connections.$connection.driver");

        return $driver === 'mysql';
    }

    public function scopeSearch($query, string $q)
    {
        $q = trim($q);
        if ($q === '') return $query;

        if (self::supportsFullText()) {
            $term = preg_replace('/[^\p{L}\p{N}\s\+\-\*\~\"\(\)]/u', ' ', $q);

            return $query
                ->whereRaw("MATCH(title, content) AGAINST (? IN BOOLEAN MODE)", [$term . '*'])
                ->orderByRaw("MATCH(title, content) AGAINST (? IN BOOLEAN MODE) DESC", [$term . '*']);
        }

        $like = '%' . addcslashes($q, '%_') . '%';

        return $query->where(function ($w) use ($like) {
            $w->where('title', 'like', $like)
              ->orWhere('content', 'like', $like);
        });
    }

    public function getExcerptAttribute(): string
    {
        $plain = trim(strip_tags((string) $this->content));

        return str($plain)->limit(160)->toString();
    }

    /**
     * Add a virtual 'user_vote' column to query results.
     * Uses user_id if logged in, anon_key if anonymous.
     */
    public function scopeWithUserVote($query, ?int $userId, ?string $anonKey)
    {
        $type = self::class;

        return $query->select('threads.*')->selectSub(function ($q) use ($userId, $anonKey, $type) {
            $q->from('votes')
              ->select('value')
              ->whereColumn('votes.votable_id', 'threads.id')
              ->where('votes.votable_type', $type)
              ->when($userId,  fn ($qq) => $qq->where('user_id', $userId))
              ->when(!$userId, fn ($qq) => $qq->where('anon_key', $anonKey))
              ->limit(1);
        }, 'user_vote');
    }
}
