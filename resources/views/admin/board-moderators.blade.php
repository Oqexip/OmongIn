{{-- resources/views/admin/board-moderators.blade.php --}}
<x-app-layout title="Moderator — /{{ $board->slug }}">
    <div class="mb-6">
        <a href="{{ route('boards.show', $board) }}" class="text-sm text-neutral-500 dark:text-neutral-400 hover:text-black dark:hover:text-white transition">
            ← /{{ $board->slug }}
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-black dark:text-white mt-2">
            Kelola Moderator: /{{ $board->slug }}
        </h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ $board->name }}</p>
    </div>

    {{-- Current Moderators --}}
    <section class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-black dark:text-white mb-4">Moderator Aktif</h2>

        @if ($moderators->count() === 0)
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Belum ada moderator untuk board ini.</p>
        @else
            <div class="space-y-2">
                @foreach ($moderators as $mod)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700">
                        <div>
                            <span class="font-medium text-neutral-900 dark:text-white">{{ $mod->name }}</span>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400 ml-2">{{ $mod->email }}</span>
                        </div>
                        <form method="POST" action="{{ route('admin.board.moderators.destroy', [$board, $mod]) }}"
                              onsubmit="return confirm('Hapus {{ $mod->name }} dari moderator?')">
                            @csrf @method('DELETE')
                            <button class="px-3 h-8 rounded-lg text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Pending Invitations --}}
    @if ($pendingInvitations->count() > 0)
        <section class="bg-white dark:bg-neutral-900 rounded-2xl border border-amber-200 dark:border-amber-800 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-black dark:text-white mb-4 flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                Undangan Pending
            </h2>
            <div class="space-y-2">
                @foreach ($pendingInvitations as $inv)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 dark:bg-amber-900 border border-amber-200 dark:border-amber-800">
                        <div>
                            <span class="font-medium text-neutral-900 dark:text-white">{{ $inv->user->name }}</span>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400 ml-2">{{ $inv->user->email }}</span>
                            <span class="text-xs text-amber-600 dark:text-amber-400 ml-2">• Menunggu respons</span>
                        </div>
                        <time class="text-xs text-neutral-400 dark:text-neutral-500">
                            {{ $inv->created_at->diffForHumans() }}
                        </time>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Send Invitation --}}
    <section class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-black dark:text-white mb-4">Undang Moderator</h2>

        @if ($availableUsers->count() === 0)
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Tidak ada user yang bisa diundang.</p>
        @else
            <form method="POST" action="{{ route('admin.board.moderators.store', $board) }}" class="flex gap-3 flex-wrap">
                @csrf
                <select name="user_id" required
                        class="flex-1 min-w-[200px] h-10 rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800
                               text-neutral-900 dark:text-neutral-100 text-sm px-3
                               focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-neutral-200 dark:focus:ring-neutral-700">
                    <option value="">Pilih user...</option>
                    @foreach ($availableUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="h-10 px-5 rounded-xl text-white text-sm font-medium shadow-sm
                               bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 transition">
                    Kirim Undangan
                </button>
            </form>
        @endif
    </section>
</x-app-layout>

