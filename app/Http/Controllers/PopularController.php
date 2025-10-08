<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;
use App\Models\Board;

class PopularController extends Controller
{
    public function index(Request $request)
    {
        $days = (int) $request->query('t', 30);
        $days = max(1, min(365, $days));

        $userId         = auth()->id(); // jika login
        $anonSessionId  = (int) ($request->attributes->get('anon_id') ?? session('anon_id')); // kalau anonim

        // formula ranking
        $popularityExpr = '(threads.score * 2 + threads.comment_count)';

        $threads = Thread::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->with(['board', 'attachments'])
            ->withUserVote($userId, $anonSessionId, 'thread') // ← 'thread' harus sama seperti saat menyimpan votes
            ->orderByRaw("$popularityExpr DESC")
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $boards = Board::query()->orderBy('name')->get();

        return view('popular.index', [
            'threads' => $threads,
            'boards'  => $boards,
            'days'    => $days,
        ]);
    }
}
