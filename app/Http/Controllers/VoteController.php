<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VoteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'votable_type' => ['required','string'],   // 'thread' | 'comment' | FQCN
            'votable_id'   => ['required','integer'],
            'value'        => ['required','in:-1,1'],
        ]);

        // Normalisasi tipe ke FQCN
        $type = strtolower(trim($data['votable_type']));
        $map  = ['thread' => Thread::class, 'comment' => Comment::class];
        $votableClass = $map[$type] ?? $data['votable_type'];

        if (!in_array($votableClass, [Thread::class, Comment::class], true)) {
            return response()->json(['message' => 'Invalid votable_type'], 422);
        }

        try {
            $model = $votableClass::query()->findOrFail((int)$data['votable_id']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Content not found'], 404);
        }

        $value  = (int) $data['value'];
        $userId = Auth::id();

        // gunakan anon_key (skema yang ada sekarang)
        $anonKey = $userId ? null : (
            $request->session()->get('anon_key')
            ?? $request->session()->get('anon_id')      // fallback lama
            ?? $request->session()->get('anon_session_id')
        );

        if (!$userId && !$anonKey) {
            $anonKey = (string) random_int(100000000, 999999999);
            $request->session()->put('anon_key', $anonKey);
        }

        return DB::transaction(function () use ($model, $value, $userId, $anonKey) {
            $existing = $model->votes()
                ->when($userId,  fn($q) => $q->where('user_id', $userId))
                ->when(!$userId, fn($q) => $q->where('anon_key', $anonKey))
                ->lockForUpdate()
                ->first();

            $hasScore = array_key_exists('score', $model->getAttributes());

            if (!$existing) {
                $model->votes()->create([
                    'user_id'  => $userId,
                    'anon_key' => $userId ? null : $anonKey,
                    'value'    => $value,
                ]);

                if ($hasScore) {
                    $model->increment('score', $value);
                    $model->refresh();
                }

                return response()->json([
                    'status' => 'created',
                    'score'  => $hasScore ? (int)$model->score : null,
                    'myVote' => $value,
                ]);
            }

            if ((int)$existing->value === $value) {
                $existing->delete();

                if ($hasScore) {
                    $model->decrement('score', $value);
                    $model->refresh();
                }

                return response()->json([
                    'status' => 'deleted',
                    'score'  => $hasScore ? (int)$model->score : null,
                    'myVote' => 0,
                ]);
            }

            // switch -1 <-> +1
            $delta = $value - (int)$existing->value; // +2 or -2
            $existing->update(['value' => $value]);

            if ($hasScore) {
                $model->increment('score', $delta);
                $model->refresh();
            }

            return response()->json([
                'status' => 'updated',
                'score'  => $hasScore ? (int)$model->score : null,
                'myVote' => $value,
            ]);
        });
    }
}
