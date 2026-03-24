<?php

namespace App\Http\Controllers;

use App\Models\{Thread, Bookmark};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    /**
     * Toggle bookmark on/off for a thread.
     */
    public function toggle(Thread $thread)
    {
        $user = Auth::user();

        $existing = Bookmark::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('ok', 'Bookmark dihapus.');
        }

        Bookmark::create([
            'user_id'   => $user->id,
            'thread_id' => $thread->id,
        ]);

        return back()->with('ok', 'Thread disimpan.');
    }

    /**
     * Display bookmarked threads for the authenticated user.
     */
    public function index()
    {
        $threads = Auth::user()
            ->bookmarkedThreads()
            ->with(['user:id,name', 'board:id,slug,name', 'category:id,name,slug'])
            ->withCount('comments')
            ->orderByPivot('created_at', 'desc')
            ->paginate(20);

        return view('bookmarks.index', compact('threads'));
    }
}
