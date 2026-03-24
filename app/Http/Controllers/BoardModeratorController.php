<?php

namespace App\Http\Controllers;

use App\Models\{Board, User, ModeratorInvitation, Notification};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoardModeratorController extends Controller
{
    /**
     * Display moderators and pending invitations for a board (admin only).
     */
    public function index(Board $board)
    {
        $moderators = $board->moderators()->orderBy('name')->get();

        $pendingInvitations = ModeratorInvitation::where('board_id', $board->id)
            ->where('status', 'pending')
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->get();

        // Exclude current mods AND users with pending invitations from available list
        $excludeIds = $moderators->pluck('id')
            ->merge($pendingInvitations->pluck('user_id'))
            ->unique();

        $availableUsers = User::whereNotIn('id', $excludeIds)
            ->where('role', '!=', 'admin')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.board-moderators', compact('board', 'moderators', 'pendingInvitations', 'availableUsers'));
    }

    /**
     * Send a moderator invitation to a user (instead of direct assign).
     */
    public function store(Request $request, Board $board)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($data['user_id']);

        if ($user->isAdmin()) {
            return back()->with('error', 'Admin tidak perlu dijadikan moderator.');
        }

        if ($board->moderators()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User sudah menjadi moderator board ini.');
        }

        // Check for existing pending invitation
        $existing = ModeratorInvitation::where('board_id', $board->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->with('error', 'Undangan untuk user ini sudah terkirim dan masih pending.');
        }

        DB::transaction(function () use ($board, $user) {
            // Create the invitation
            $invitation = ModeratorInvitation::create([
                'board_id'   => $board->id,
                'user_id'    => $user->id,
                'invited_by' => Auth::id(),
                'status'     => 'pending',
            ]);

            // Send notification to the invited user
            Notification::create([
                'user_id'         => $user->id,
                'sender_id'       => Auth::id(),
                'type'            => 'moderator_invite',
                'notifiable_type' => ModeratorInvitation::class,
                'notifiable_id'   => $invitation->id,
                'message'         => "mengundang Anda menjadi moderator di /{$board->slug}.",
            ]);
        });

        return back()->with('ok', "Undangan moderator dikirim ke {$user->name}.");
    }

    /**
     * Remove a user from board moderators.
     */
    public function destroy(Board $board, User $user)
    {
        $board->moderators()->detach($user->id);

        // Also clean up any accepted invitation records
        ModeratorInvitation::where('board_id', $board->id)
            ->where('user_id', $user->id)
            ->delete();

        return back()->with('ok', "{$user->name} dihapus dari moderator.");
    }
}
