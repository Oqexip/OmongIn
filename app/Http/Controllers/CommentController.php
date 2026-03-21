<?php

namespace App\Http\Controllers;

use App\Models\{Thread, Comment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Support\SaveImages;

class CommentController extends Controller
{
    /**
     * Store a new comment on a thread.
     */
    public function store(Request $request, Thread $thread)
    {
        $data = $request->validate([
            'content'   => ['required', 'string', 'min:1', 'max:10000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
            'images.*'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $parentId = $data['parent_id'] ?? null;
        $parent   = null;

        if ($parentId) {
            $parent = Comment::whereKey($parentId)
                ->where('thread_id', $thread->id)
                ->firstOrFail();
        }

        $depth = $parent ? ($parent->depth + 1) : 0;

        DB::transaction(function () use ($request, $thread, $data, $parentId, $depth) {
            $comment = Comment::create([
                'thread_id'       => $thread->id,
                'parent_id'       => $parentId,
                'anon_session_id' => Auth::check() ? null : (int) $request->attributes->get('anon_id'),
                'user_id'         => Auth::id(),
                'depth'           => $depth,
                'content'         => $data['content'],
            ]);

            if ($request->hasFile('images')) {
                foreach (SaveImages::storeMany($request->file('images')) as $att) {
                    $comment->attachments()->create($att);
                }
            }

            $thread->increment('comment_count');
        });

        return back()->with('ok', 'Posted');
    }

    /**
     * Update a comment (owner only, within 15 minutes).
     */
    public function update(Request $request, Comment $comment)
    {
        if (! $comment->isOwnedByRequest($request) || ! $comment->canEditNow()) {
            abort(403, 'You can only edit your own comment within 15 minutes.');
        }

        $data = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:10000'],
        ]);

        $comment->fill(['content' => $data['content']]);

        if ($comment->isDirty('content')) {
            $comment->edited_at = now();
        }

        $comment->save();

        return back()->with('ok', 'Comment edited');
    }

    /**
     * Delete a comment.
     * Owner can delete within 15 minutes, otherwise uses policy (admin/mod).
     */
    public function destroy(Request $request, Comment $comment)
    {
        $isOwner = $comment->isOwnedByRequest($request);
        $recent  = $comment->created_at && $comment->created_at->gt(now()->subMinutes(15));

        if (! ($isOwner && $recent)) {
            $this->authorize('delete', $comment);
        }

        DB::transaction(function () use ($comment) {
            $comment->delete();
            $comment->thread()->decrement('comment_count');
        });

        return back()->with('ok', 'Comment removed');
    }
}
