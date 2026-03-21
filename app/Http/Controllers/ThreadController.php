<?php

namespace App\Http\Controllers;

use App\Models\{Board, Thread};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\SaveImages;
use Illuminate\Validation\Rule;

class ThreadController extends Controller
{
    /**
     * Index threads per board + search ?q=
     */
    public function index(Board $board, Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $category = $request->query('category');
        $sort     = $request->query('sort', 'newest');

        $threads = Thread::query()
            ->where('board_id', $board->id)
            ->with(['user:id,name', 'category:id,name,slug'])
            ->when($category, function ($query, $category) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $category));
            })
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('title', 'like', $like)
                      ->orWhere('content', 'like', $like);
                });
            })
            ->orderByDesc('is_pinned')
            ->when($sort === 'most_liked', function ($query) {
                $query->orderByDesc('score')->latest();
            })
            ->when($sort === 'most_active', function ($query) {
                $query->orderByDesc('comment_count')->latest();
            })
            ->when($sort === 'oldest', function ($query) {
                $query->oldest();
            })
            ->when($sort === 'newest', function ($query) {
                $query->latest();
            })
            ->when(!in_array($sort, ['most_liked', 'most_active', 'oldest', 'newest']), function ($query) {
                $query->orderByDesc('score')->latest();
            })
            ->paginate(20)
            ->withQueryString();

        $categories = $board->categories()->select('id', 'name', 'slug')->get();

        return view('threads.index', [
            'board'      => $board,
            'threads'    => $threads,
            'categories' => $categories,
            'q'          => $q,
            'category'   => $category,
            'sort'       => $sort,
            'title'      => $q ? "Hasil untuk \u201c{$q}\u201d di {$board->name}" : $board->name,
        ]);
    }

    /**
     * Show thread + structured comments.
     */
    public function show(Thread $thread)
    {
        $thread->load(['user', 'comments.user']);

        $comments = $thread->comments()->orderBy('created_at')->get();
        $grouped  = $comments->groupBy('parent_id');

        return view('threads.show', compact('thread', 'grouped'));
    }

    /**
     * Store a new thread in a board.
     */
    public function store(Request $request, Board $board)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:140'],
            'content'     => ['required', 'string', 'min:3', 'max:10000'],
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where('board_id', $board->id),
            ],
            'images.*'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'is_nsfw'    => ['nullable', 'boolean'],
            'is_spoiler' => ['nullable', 'boolean'],
        ]);

        $thread = Thread::create([
            'board_id'        => $board->id,
            'category_id'     => $data['category_id'] ?? null,
            'anon_session_id' => Auth::check() ? null : (int) $request->attributes->get('anon_id'),
            'user_id'         => Auth::id(),
            'title'           => $data['title'] ?? null,
            'content'         => $data['content'],
            'is_nsfw'         => $request->boolean('is_nsfw'),
            'is_spoiler'      => $request->boolean('is_spoiler'),
        ]);

        if ($request->hasFile('images')) {
            foreach (SaveImages::storeMany($request->file('images')) as $att) {
                $thread->attachments()->create($att);
            }
        }

        return redirect()->route('threads.show', $thread)->with('ok', 'Posted');
    }

    /**
     * Update a thread (owner only, within 15 minutes).
     */
    public function update(Request $request, Thread $thread)
    {
        if (! $thread->isOwnedByRequest($request) || ! $thread->canEditNow()) {
            abort(403, 'You can only edit your own thread within 15 minutes.');
        }

        $data = $request->validate([
            'title'   => ['required', 'string', 'max:140'],
            'content' => ['required', 'string', 'min:3', 'max:10000'],
        ]);

        $thread->fill([
            'title'   => $data['title'] ?? null,
            'content' => $data['content'],
        ]);

        if ($thread->isDirty(['title', 'content'])) {
            $thread->edited_at = now();
        }

        $thread->save();

        return back()->with('ok', 'Thread updated');
    }

    /**
     * Delete a thread.
     * Anon owner can delete within 15 minutes, otherwise uses policy (admin/mod).
     */
    public function destroy(Request $request, Thread $thread)
    {
        $board = $thread->board;

        $isOwner = $thread->isOwnedByRequest($request);
        $recent  = $thread->created_at->gt(now()->subMinutes(15));

        if (! ($isOwner && $recent)) {
            $this->authorize('delete', $thread);
        }

        $thread->delete();

        return redirect()
            ->route('boards.show', $board)
            ->with('ok', 'Thread removed');
    }
}
