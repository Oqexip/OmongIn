<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Board;
use Illuminate\Http\Request;

class AdminBoardController extends Controller
{
    public function index()
    {
        $boards = Board::withCount('threads')->orderBy('name')->get();
        return view('admin.boards.index', compact('boards'));
    }

    public function create()
    {
        return view('admin.boards.form', ['board' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug'        => ['required', 'string', 'max:50', 'unique:boards,slug', 'regex:/^[a-z0-9_\-]+$/'],
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_nsfw'     => ['nullable', 'boolean'],
        ]);

        $data['is_nsfw'] = $data['is_nsfw'] ?? false;

        Board::create($data);

        return redirect()->route('admin.boards.index')->with('ok', "Board /{$data['slug']} berhasil dibuat.");
    }

    public function edit(Board $board)
    {
        return view('admin.boards.form', compact('board'));
    }

    public function update(Request $request, Board $board)
    {
        $data = $request->validate([
            'slug'        => ['required', 'string', 'max:50', 'regex:/^[a-z0-9_\-]+$/', "unique:boards,slug,{$board->id}"],
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_nsfw'     => ['nullable', 'boolean'],
        ]);

        $data['is_nsfw'] = $data['is_nsfw'] ?? false;

        $board->update($data);

        return redirect()->route('admin.boards.index')->with('ok', "Board /{$board->slug} berhasil diperbarui.");
    }

    public function destroy(Board $board)
    {
        $slug = $board->slug;
        $board->delete();

        return redirect()->route('admin.boards.index')->with('ok', "Board /{$slug} berhasil dihapus.");
    }
}
