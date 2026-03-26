<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    /**
     * Cast a vote on a poll option.
     * Accessible by both authenticated users and anonymous sessions.
     * Validates: poll must be accepting votes, option must belong to poll, no double-voting.
     */
    public function vote(Request $request, Poll $poll)
    {
        // Check poll is still active
        if (! $poll->isAcceptingVotes()) {
            return back()->with('error', 'Polling ini sudah ditutup atau sudah berakhir.');
        }

        $data = $request->validate([
            'poll_option_id' => ['required', 'integer'],
        ]);

        // Resolve voter identity
        $userId  = Auth::id();
        $anonKey = $userId ? null : (string) ($request->attributes->get('anon_id') ?? session('anon_id'));

        // Verify the option belongs to this poll
        $option = PollOption::where('id', $data['poll_option_id'])
            ->where('poll_id', $poll->id)
            ->firstOrFail();

        // Prevent double-voting
        if ($poll->hasVoted($userId, $anonKey)) {
            return back()->with('error', 'Kamu sudah memberikan suara di polling ini.');
        }

        DB::transaction(function () use ($poll, $option, $userId, $anonKey) {
            // Record the vote
            PollVote::create([
                'poll_id'        => $poll->id,
                'poll_option_id' => $option->id,
                'user_id'        => $userId,
                'anon_key'       => $anonKey,
            ]);

            // Increment the cached counter on the option
            $option->increment('votes_count');
        });

        return back()->with('ok', 'Suaramu sudah dicatat!');
    }
}
