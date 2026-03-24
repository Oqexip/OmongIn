<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BanAppeal;
use Illuminate\Http\Request;

class AdminBanAppealController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $appeals = BanAppeal::with(['user', 'reviewer'])
            ->when($status !== 'all', fn ($q) => $q->status($status))
            ->latest()
            ->paginate(20);

        $counts = [
            'pending'  => BanAppeal::pending()->count(),
            'approved' => BanAppeal::status('approved')->count(),
            'rejected' => BanAppeal::status('rejected')->count(),
        ];

        return view('admin.appeals.index', compact('appeals', 'status', 'counts'));
    }

    public function show(BanAppeal $appeal)
    {
        $appeal->load(['user', 'reviewer']);
        return view('admin.appeals.show', compact('appeal'));
    }

    public function approve(Request $request, BanAppeal $appeal)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $appeal->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'admin_notes' => $request->admin_notes,
            'resolved_at' => now(),
        ]);

        // Unban the user
        $appeal->user->update([
            'banned_at'    => null,
            'banned_until' => null,
            'ban_reason'   => null,
        ]);

        return redirect()->route('admin.appeals.index')
            ->with('success', 'Banding disetujui. User ' . $appeal->user->name . ' telah di-unban.');
    }

    public function reject(Request $request, BanAppeal $appeal)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $appeal->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'admin_notes' => $request->admin_notes,
            'resolved_at' => now(),
        ]);

        return redirect()->route('admin.appeals.index')
            ->with('success', 'Banding ditolak.');
    }
}
