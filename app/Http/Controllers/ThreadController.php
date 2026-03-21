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
            ->orderByDesc('score')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $categories = $board->categories()->select('id', 'name', 'slug')->get();

        return view('threads.index', [
            'board'      => $board,
            'threads'    => $threads,
            'categories' => $categories,
            'q'          => $q,
            'category'   => $category,
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
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $thread = Thread::create([
            'board_id'        => $board->id,
            'category_id'     => $data['category_id'] ?? null,
            'anon_session_id' => Auth::check() ? null : (int) $request->attributes->get('anon_id'),
            'user_id'         => Auth::id(),
            'title'           => $data['title'] ?? null,
            'content'         => $data['content'],
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
