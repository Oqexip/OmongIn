{{-- resources/views/admin/boards/index.blade.php --}}
<x-app-layout title="Boards — Admin">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-neutral-500 dark:text-neutral-400 hover:text-black dark:hover:text-white transition">← Admin</a>
            <h1 class="text-2xl font-bold tracking-tight text-black dark:text-white mt-1">Kelola Boards</h1>
        </div>
        <a href="{{ route('admin.boards.create') }}"
           class="h-10 px-5 rounded-xl text-white text-sm font-medium shadow-sm inline-flex items-center bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 transition">
            + Board Baru
        </a>
    </div>

    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-200 dark:border-neutral-800 text-left text-xs text-neutral-500 dark:text-neutral-400 uppercase">
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Threads</th>
                    <th class="px-4 py-3">NSFW</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @foreach ($boards as $board)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                        <td class="px-4 py-3 font-mono text-black dark:text-white">/{{ $board->slug }}</td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-300">{{ $board->name }}</td>
                        <td class="px-4 py-3 text-neutral-500 dark:text-neutral-400">{{ $board->threads_count }}</td>
                        <td class="px-4 py-3">
                            @if ($board->is_nsfw)
                                <span class="text-xs font-medium" style="color: #ef4444;">NSFW</span>
                            @else
                                <span class="text-xs text-neutral-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.boards.edit', $board) }}"
                                   class="px-3 h-7 rounded-lg text-xs font-medium text-neutral-600 dark:text-neutral-400 border border-neutral-300 dark:border-neutral-600 hover:text-black dark:hover:text-white transition inline-flex items-center">
                                    Edit
                                </a>
                                <a href="{{ route('admin.board.moderators.index', $board->slug) }}"
                                   class="px-3 h-7 rounded-lg text-xs font-medium text-neutral-600 dark:text-neutral-400 border border-neutral-300 dark:border-neutral-600 hover:text-black dark:hover:text-white transition inline-flex items-center">
                                    Moderator
                                </a>
                                <form method="POST" action="{{ route('admin.boards.destroy', $board) }}"
                                      onsubmit="return confirm('Hapus board /{{ $board->slug }}? Semua thread di dalamnya akan ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 h-7 rounded-lg text-xs font-medium transition" style="color: #ef4444; border: 1px solid #ef4444;">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
