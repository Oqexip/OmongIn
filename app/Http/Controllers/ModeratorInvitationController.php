<?php

namespace App\Http\Controllers;

use App\Models\{ModeratorInvitation, Notification};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModeratorInvitationController extends Controller
{
    /**
     * Accept a moderator invitation.
     */
    public function accept(ModeratorInvitation $invitation)
    {
        $user = Auth::user();

        // Only the invited user can accept
        if ($invitation->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak merespons undangan ini.');
        }

        if (!$invitation->isPending()) {
            return back()->with('error', 'Undangan sudah direspons sebelumnya.');
        }

        DB::transaction(function () use ($invitation, $user) {
            // Update invitation status
            $invitation->update(['status' => 'accepted']);

            // Attach user as board moderator
            $invitation->board->moderators()->syncWithoutDetaching([$user->id]);

            // Mark the related notification as read
            Notification::where('user_id', $user->id)
                ->where('type', 'moderator_invite')
                ->where('notifiable_type', ModeratorInvitation::class)
                ->where('notifiable_id', $invitation->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Send notification to admin who invited
            Notification::create([
                'user_id'         => $invitation->invited_by,
                'sender_id'       => $user->id,
                'type'            => 'invite_accepted',
                'notifiable_type' => ModeratorInvitation::class,
                'notifiable_id'   => $invitation->id,
                'message'         => "menerima undangan moderator untuk /{$invitation->board->slug}.",
            ]);
        });

        return back()->with('ok', "Anda sekarang moderator di /{$invitation->board->slug}!");
    }

    /**
     * Decline a moderator invitation.
     */
    public function decline(ModeratorInvitation $invitation)
    {
        $user = Auth::user();

        if ($invitation->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak merespons undangan ini.');
        }

        if (!$invitation->isPending()) {
            return back()->with('error', 'Undangan sudah direspons sebelumnya.');
        }

        DB::transaction(function () use ($invitation, $user) {
            $invitation->update(['status' => 'declined']);

            // Mark the related notification as read
            Notification::where('user_id', $user->id)
                ->where('type', 'moderator_invite')
                ->where('notifiable_type', ModeratorInvitation::class)
                ->where('notifiable_id', $invitation->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Notify the admin
            Notification::create([
                'user_id'         => $invitation->invited_by,
                'sender_id'       => $user->id,
                'type'            => 'invite_declined',
                'notifiable_type' => ModeratorInvitation::class,
                'notifiable_id'   => $invitation->id,
                'message'         => "menolak undangan moderator untuk /{$invitation->board->slug}.",
            ]);
        });

        return back()->with('ok', 'Undangan moderator ditolak.');
    }
}
