<?php

namespace App\Http\Controllers;

use App\Models\BanAppeal;
use App\Models\User;
use Illuminate\Http\Request;

class BanAppealController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason'  => 'required|string|max:2000',
        ]);

        $user = User::findOrFail($request->user_id);

        // Only banned users can submit appeals
        if (! $user->isBanned()) {
            return back()->with('error', 'Akun Anda tidak sedang diblokir.');
        }

        // Check if user already has a pending appeal
        $existingAppeal = BanAppeal::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingAppeal) {
            return back()->with('error', 'Anda sudah memiliki banding yang sedang ditinjau.');
        }

        BanAppeal::create([
            'user_id' => $user->id,
            'reason'  => $request->reason,
        ]);

        return back()->with('success', 'Banding Anda berhasil dikirim dan sedang menunggu peninjauan admin.');
    }
}
