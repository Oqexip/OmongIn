<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Comment;
use App\Models\Thread;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $threads    = collect();
        $comments   = collect();
        $boards     = collect();

        if ($q !== '') {
            $like = '%' . addcslashes($q, '%_') . '%';

            // Search threads
            $threads = Thread::query()
                ->with(['board', 'user:id,name', 'category:id,name,slug'])
                ->where(function ($w) use ($like) {
                    $w->where('title', 'like', $like)
                      ->orWhere('content', 'like', $like);
                })
                ->orderByDesc('score')
                ->latest()
                ->paginate(15, ['*'], 'threads_page')
                ->withQueryString();

            // Search comments
            $comments = Comment::query()
                ->with(['thread:id,title,board_id', 'thread.board:id,slug,name', 'user:id,name'])
                ->where('content', 'like', $like)
                ->latest()
                ->paginate(15, ['*'], 'comments_page')
                ->withQueryString();

            // Search boards
            $boards = Board::query()
                ->where(function ($w) use ($like) {
                    $w->where('name', 'like', $like)
                      ->orWhere('description', 'like', $like);
                })
                ->orderBy('name')
                ->get();
        }

        return view('search.index', [
            'q'        => $q,
            'threads'  => $threads,
            'comments' => $comments,
            'boards'   => $boards,
        ]);
    }
}
