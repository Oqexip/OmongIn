<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->query('filter') === 'banned') {
            $query->banned();
        }

        $users = $query->orderByDesc('created_at')->paginate(20)->appends($request->query());

        return view('admin.users.index', compact('users'));
    }

    public function ban(Request $request, User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Tidak bisa ban admin.');
        }

        $data = $request->validate([
            'duration'   => ['required', 'in:permanent,1,7,30'],
            'ban_reason' => ['required', 'string', 'max:500'],
        ]);

        $bannedUntil = match ($data['duration']) {
            '1'  => now()->addDay(),
            '7'  => now()->addDays(7),
            '30' => now()->addDays(30),
            default => null, // permanent
        };

        $user->update([
            'banned_at'    => now(),
            'banned_until' => $bannedUntil,
            'ban_reason'   => $data['ban_reason'],
        ]);

        $durLabel = $bannedUntil ? "selama {$data['duration']} hari" : 'permanen';
        return back()->with('ok', "{$user->name} telah di-ban {$durLabel}.");
    }

    public function unban(User $user)
    {
        $user->update([
            'banned_at'    => null,
            'banned_until' => null,
            'ban_reason'   => null,
        ]);

        return back()->with('ok', "{$user->name} telah di-unban.");
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Tidak bisa hapus admin.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('ok', "Akun {$name} telah dihapus.");
    }
}
