<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            $userId = Auth::user()->id;
            $bannedUntil = Auth::user()->banned_until;
            $banReason = Auth::user()->ban_reason;
            $userEmail = Auth::user()->email;

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Store info in the newly regenerated session so the banned page can read it
            $request->session()->put('banned_info', [
                'user_id' => $userId,
                'until'   => $bannedUntil,
                'reason'  => $banReason,
                'email'   => $userEmail,
            ]);

            return redirect()->route('banned.show');
        }

        return $next($request);
    }
}
