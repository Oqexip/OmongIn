{{-- resources/views/bookmarks/index.blade.php --}}
<x-app-layout title="Tersimpan">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-black dark:text-white">Tersimpan</h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Thread yang Anda simpan.</p>
    </div>

    @if ($threads->count() === 0)
        <div class="text-center py-16">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-neutral-300 dark:text-neutral-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
            <p class="text-neutral-500 dark:text-neutral-400">Belum ada thread yang disimpan.</p>
            <a href="{{ route('home') }}" class="inline-block mt-4 text-sm text-neutral-500 hover:text-black dark:hover:text-white transition">
                ← Jelajahi Boards
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($threads as $thread)
                @php
                    $isAuthUser = (bool) $thread->user;
                    $h = !$isAuthUser ? \App\Support\Anon::handleForThread($thread->id, $thread->anon_session_id) : null;
                    $palette = ['rose','orange','amber','emerald','teal','sky','violet','pink','yellow','lime','cyan','blue','indigo','purple','fuchsia'];
                    $color = isset($h['color'], $palette[$h['color']]) ? $palette[$h['color']] : 'gray';
                @endphp
                <a href="{{ route('threads.show', $thread) }}"
                   class="block bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-neutral-200 dark:border-neutral-800 p-5 hover:border-neutral-300 dark:hover:border-neutral-700 transition group">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-base font-semibold text-black dark:text-white group-hover:underline underline-offset-2 truncate flex items-center gap-2">
                                {{ $thread->title }}
                                @if($thread->is_nsfw)
                                    <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900/30 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800">NSFW</span>
                                @endif
                            </h2>
                            <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1 flex items-center gap-2">
                                <span class="font-medium text-neutral-700 dark:text-neutral-300">
                                    /{{ $thread->board->slug ?? '?' }}
                                </span>
                                <span>•</span>
                                @if ($isAuthUser)
                                    <span>{{ $thread->user->name }}</span>
                                @else
                                    <span class="{{ 'text-' . $color . '-600' }}">{{ $h['name'] ?? 'Anon' }}</span>
                                @endif
                                <span>•</span>
                                <time>{{ $thread->created_at->diffForHumans() }}</time>
                            </div>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2 line-clamp-2">
                                {{ \App\Support\Sanitize::excerpt($thread->content, 180) }}
                            </p>
                        </div>

                        {{-- Unbookmark button --}}
                        <form method="POST" action="{{ route('bookmarks.toggle', $thread) }}" onclick="event.stopPropagation(); event.preventDefault(); this.submit();">
                            @csrf
                            <button class="h-9 w-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 flex items-center justify-center transition"
                                    title="Hapus dari tersimpan">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-black dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <div class="flex items-center gap-4 mt-3 text-xs text-neutral-400 dark:text-neutral-500">
                        <span>{{ $thread->score ?? 0 }} votes</span>
                        <span>{{ $thread->comments_count ?? 0 }} komentar</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $threads->links() }}
        </div>
    @endif
</x-app-layout>
