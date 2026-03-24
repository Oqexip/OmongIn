<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    /**
     * Search users by name for @mention autocomplete.
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $users = User::where('name', 'like', $q . '%')
            ->select('id', 'name')
            ->limit(10)
            ->get();

        return response()->json($users);
    }
}
