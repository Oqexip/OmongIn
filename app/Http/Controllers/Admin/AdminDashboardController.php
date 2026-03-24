<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{BanAppeal, Board, Comment, Report, Thread, User};

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users'           => User::count(),
            'threads'         => Thread::count(),
            'comments'        => Comment::count(),
            'boards'          => Board::count(),
            'open_reports'    => Report::open()->count(),
            'banned_users'    => User::banned()->count(),
            'pending_appeals' => BanAppeal::pending()->count(),
        ];

        $recentReports = Report::open()
            ->with('reportable')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReports'));
    }
}
