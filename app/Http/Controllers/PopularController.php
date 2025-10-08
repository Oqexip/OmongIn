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

        $userId   = auth()->id();
$anonKey  = $userId ? null : (string)(session('anon_key') ?? session('anon_id') ?? session('anon_session_id'));

$threads = Thread::query()
    ->where('created_at', '>=', now()->subDays($days))
    ->with(['board','attachments'])
    ->withUserVote($userId, $anonKey)
    ->orderByRaw('(threads.score * 2 + threads.comment_count) DESC')
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
