<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;
use App\Models\Board;

class PopularController extends Controller
{
    public function index(Request $request)
    {
        $t = $request->query('t', '30');

        $userId  = auth()->id();
        $anonKey = $userId
            ? null
            : (string) (session('anon_key') ?? session('anon_id') ?? session('anon_session_id'));

        $query = Thread::query()
            ->with(['board', 'attachments'])
            ->withUserVote($userId, $anonKey)
            ->orderByRaw('(threads.score * 2 + threads.comment_count) DESC')
            ->orderByDesc('created_at');

        if ($t !== 'all') {
            $days = max(1, min(365, (int) $t));
            $query->where('created_at', '>=', now()->subDays($days));
        } else {
            $days = 'all';
        }

        $threads = $query->paginate(20)->withQueryString();

        $boards = Board::query()->orderBy('name')->get();

        return view('popular.index', [
            'threads' => $threads,
            'boards'  => $boards,
            'days'    => $days,
        ]);
    }
}
