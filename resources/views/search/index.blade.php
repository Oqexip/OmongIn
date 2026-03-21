{{-- resources/views/search/index.blade.php --}}
@php use Illuminate\Support\Str; @endphp

<x-app-layout title="Pencarian">
    <div class="mx-auto max-w-4xl">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-black tracking-tight text-black dark:text-white mb-1">Pencarian</h1>
            <p class="text-neutral-500 dark:text-neutral-400 text-sm">Cari thread, komentar, atau board</p>
        </div>

        {{-- Search Form --}}
        <form action="{{ route('search.index') }}" method="GET" class="mb-8">
            <div class="relative">
                <input type="text" name="q" value="{{ $q }}" placeholder="Ketik kata kunci..."
                    autofocus
                    class="w-full h-12 shadow-sm rounded-2xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-5 pr-12 text-base
                           text-neutral-900 dark:text-neutral-100 placeholder:text-neutral-400 dark:placeholder:text-neutral-500 outline-none
                           focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 transition" />
                @if ($q)
                    <a href="{{ route('search.index') }}"
                        class="absolute right-12 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 transition">✕</a>
                @endif
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2" aria-label="Cari">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-500 dark:text-neutral-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7" stroke-width="1.6" />
                        <path d="M20 20l-3.5-3.5" stroke-width="1.6" />
                    </svg>
                </button>
            </div>
        </form>

        @if ($q === '')
            {{-- Empty state --}}
            <div class="text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-neutral-300 dark:text-neutral-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" stroke-width="1.2" />
                    <path d="M20 20l-3.5-3.5" stroke-width="1.2" />
                </svg>
                <p class="text-neutral-500 dark:text-neutral-400">Masukkan kata kunci untuk mulai mencari.</p>
            </div>
        @else
            {{-- ===== TABS (Alpine) ===== --}}
            <div x-data="{ tab: 'threads' }" class="space-y-6">
                {{-- Tab Pills --}}
                <div class="flex flex-wrap items-center gap-2">
                    <button @click="tab = 'threads'"
                        class="inline-flex items-center px-4 h-9 rounded-full text-sm font-medium transition-all"
                        :class="tab === 'threads'
                            ? 'bg-black text-white dark:bg-white dark:text-black shadow-sm'
                            : 'bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 border border-neutral-200 dark:border-neutral-700 hover:border-neutral-400 dark:hover:border-neutral-500'">
                        Threads
                        @if ($threads instanceof \Illuminate\Pagination\AbstractPaginator)
                            <span class="ml-1.5 text-xs opacity-70">({{ $threads->total() }})</span>
                        @endif
                    </button>
                    <button @click="tab = 'comments'"
                        class="inline-flex items-center px-4 h-9 rounded-full text-sm font-medium transition-all"
                        :class="tab === 'comments'
                            ? 'bg-black text-white dark:bg-white dark:text-black shadow-sm'
                            : 'bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 border border-neutral-200 dark:border-neutral-700 hover:border-neutral-400 dark:hover:border-neutral-500'">
                        Komentar
                        @if ($comments instanceof \Illuminate\Pagination\AbstractPaginator)
                            <span class="ml-1.5 text-xs opacity-70">({{ $comments->total() }})</span>
                        @endif
                    </button>
                    <button @click="tab = 'boards'"
                        class="inline-flex items-center px-4 h-9 rounded-full text-sm font-medium transition-all"
                        :class="tab === 'boards'
                            ? 'bg-black text-white dark:bg-white dark:text-black shadow-sm'
                            : 'bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 border border-neutral-200 dark:border-neutral-700 hover:border-neutral-400 dark:hover:border-neutral-500'">
                        Boards
                        @if ($boards instanceof \Illuminate\Support\Collection)
                            <span class="ml-1.5 text-xs opacity-70">({{ $boards->count() }})</span>
                        @endif
                    </button>
                </div>

                {{-- ===== THREADS TAB ===== --}}
                <div x-show="tab === 'threads'" x-cloak>
                    @if ($threads instanceof \Illuminate\Pagination\AbstractPaginator && $threads->count())
                        <div class="space-y-3">
                            @foreach ($threads as $t)
                                <a href="{{ route('threads.show', $t) }}" class="group hover-card block p-4 transition">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                                <span class="transition group-hover:underline underline-offset-4 decoration-2 decoration-neutral-400 dark:decoration-neutral-500">
                                                    {{ $t->title ?? Str::limit(strip_tags($t->content), 80) }}
                                                </span>
                                            </div>
                                            @if ($t->category)
                                                <span class="inline-block mt-1 text-[11px] px-2 py-0.5 rounded-full border border-neutral-300 dark:border-neutral-600 bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 font-medium">
                                                    {{ $t->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                        @if ($t->board)
                                            <span class="shrink-0 text-xs font-medium text-neutral-500 dark:text-neutral-400 bg-neutral-100 dark:bg-neutral-800 px-2.5 py-1 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                                /{{ $t->board->slug }}
                                            </span>
                                        @endif
                                    </div>

                                    <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400 line-clamp-2">
                                        {{ Str::limit(strip_tags($t->content), 200) }}
                                    </p>

                                    <div class="mt-2 text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-4 flex-wrap">
                                        <span class="inline-flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-400 dark:text-neutral-500" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M20 2H4a2 2 0 00-2 2v13.586L6.586 14H20a2 2 0 002-2V4a2 2 0 00-2-2z" />
                                            </svg>
                                            {{ $t->comment_count }} comments
                                        </span>
                                        <span class="inline-flex items-center gap-1">
                                            ▲ {{ $t->score }}
                                        </span>
                                        <span>{{ $t->created_at->diffForHumans() }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-5">{{ $threads->links() }}</div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-neutral-500 dark:text-neutral-400">Tidak ditemukan thread untuk "<strong>{{ $q }}</strong>"</p>
                        </div>
                    @endif
                </div>

                {{-- ===== COMMENTS TAB ===== --}}
                <div x-show="tab === 'comments'" x-cloak>
                    @if ($comments instanceof \Illuminate\Pagination\AbstractPaginator && $comments->count())
                        <div class="space-y-3">
                            @foreach ($comments as $c)
                                <a href="{{ route('threads.show', $c->thread_id) }}#c-{{ $c->id }}" class="group hover-card block p-4 transition">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-semibold text-neutral-800 dark:text-neutral-200 mb-1">
                                                @if ($c->user)
                                                    {{ $c->user->name }}
                                                @else
                                                    <span class="text-neutral-500">Anon</span>
                                                @endif
                                                <span class="text-neutral-400 dark:text-neutral-500 font-normal">commented on</span>
                                                <span class="text-neutral-900 dark:text-neutral-100 group-hover:underline underline-offset-2">
                                                    {{ $c->thread->title ?? 'Thread' }}
                                                </span>
                                            </div>

                                            <p class="text-sm text-neutral-600 dark:text-neutral-400 line-clamp-2">
                                                {{ Str::limit(strip_tags($c->content), 200) }}
                                            </p>
                                        </div>
                                        @if ($c->thread && $c->thread->board)
                                            <span class="shrink-0 text-xs font-medium text-neutral-500 dark:text-neutral-400 bg-neutral-100 dark:bg-neutral-800 px-2.5 py-1 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                                /{{ $c->thread->board->slug }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                                        {{ $c->created_at->diffForHumans() }}
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-5">{{ $comments->links() }}</div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-neutral-500 dark:text-neutral-400">Tidak ditemukan komentar untuk "<strong>{{ $q }}</strong>"</p>
                        </div>
                    @endif
                </div>

                {{-- ===== BOARDS TAB ===== --}}
                <div x-show="tab === 'boards'" x-cloak>
                    @if ($boards instanceof \Illuminate\Support\Collection && $boards->count())
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach ($boards as $b)
                                <a href="{{ route('boards.show', $b) }}" class="hover-card group block p-5 transition-all duration-300">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="text-xl font-bold tracking-tight text-black dark:text-white group-hover:underline underline-offset-4 decoration-2">
                                            /{{ $b->slug }}
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-400 dark:text-neutral-500 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">{{ $b->description }}</div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-neutral-500 dark:text-neutral-400">Tidak ditemukan board untuk "<strong>{{ $q }}</strong>"</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
