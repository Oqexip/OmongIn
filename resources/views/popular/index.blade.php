{{-- resources/views/popular/index.blade.php --}}
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    $presets = [1 => '24h', 7 => '7d', 30 => '30d', 90 => '90d', 'all' => 'All Time'];
@endphp

<x-app-layout title="Popular Threads">
    <div class="mx-auto max-w-[1200px] px-4 py-6">
        <div class="grid grid-cols-12 gap-6">
            {{-- ===== LEFT: Sidebar Boards ===== --}}
            <aside class="hidden lg:block col-span-3">
                <div class="sticky top-20">
                    <div class="mb-3 text-xs font-bold text-neutral-400 dark:text-neutral-500 uppercase tracking-widest">Boards</div>

                    <nav class="space-y-1.5">
                        @forelse ($boards as $board)
                            <a href="{{ route('boards.show', $board) }}"
                               class="group flex items-center justify-between px-3 h-10 rounded-xl border text-sm transition-all
                                      bg-white dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800
                                      hover:border-neutral-400 dark:hover:border-neutral-600 hover:shadow-sm">
                                <span class="truncate text-neutral-700 dark:text-neutral-300 group-hover:text-black dark:group-hover:text-white font-medium">{{ $board->name }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-300 dark:text-neutral-600 group-hover:text-neutral-500 dark:group-hover:text-neutral-400 group-hover:translate-x-0.5 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @empty
                            <div class="px-3 py-2 text-sm text-neutral-500 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900">
                                Belum ada board.
                            </div>
                        @endforelse
                    </nav>
                </div>
            </aside>

            {{-- ===== CENTER: Feed Popular ===== --}}
            <section class="col-span-12 lg:col-span-6 xl:col-span-7">
                <div class="mb-6">
                    <h1 class="text-3xl font-black tracking-tight text-black dark:text-white mb-1">Popular</h1>
                    <p class="text-neutral-500 dark:text-neutral-400 text-sm">Trending threads across all boards</p>
                </div>

                {{-- Time Filter Pills --}}
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    @foreach ($presets as $d => $label)
                        <a href="{{ route('popular.index', ['t' => $d]) }}"
                           class="inline-flex items-center px-4 h-9 rounded-full text-sm font-medium transition-all
                                  {{ (string)$days === (string)$d
                                      ? 'bg-black text-white dark:bg-white dark:text-black shadow-sm'
                                      : 'bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 border border-neutral-200 dark:border-neutral-700 hover:border-neutral-400 dark:hover:border-neutral-500' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                @if ($threads->isEmpty())
                    <div class="text-center py-16">
                        <p class="text-neutral-500 dark:text-neutral-400">Belum ada thread populer untuk periode ini.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($threads as $thread)
                            @php
                                $title       = $thread->title ?? '(untitled)';
                                $excerpt     = Str::limit(strip_tags($thread->content), 240);
                                $userVote    = (int) ($thread->user_vote ?? 0);
                                $firstAttach = $thread->attachments->first();
                                $imgUrl      = $firstAttach ? Storage::url($firstAttach->path) : null;
                                $threadUrl   = route('threads.show', $thread);
                            @endphp

                            <article
                                x-data="{
                                    busy:false,
                                    score: {{ $thread->score }},
                                    myVote: {{ $userVote }},
                                    async vote(val) {
                                        if (this.busy) return;
                                        this.busy = true;
                                        try {
                                            const res = await fetch('{{ route('vote.store') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Accept': 'application/json',
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                                },
                                                body: JSON.stringify({
                                                    votable_type: 'thread',
                                                    votable_id: {{ $thread->id }},
                                                    value: val
                                                })
                                            });
                                            const data = await res.json();
                                            if (typeof data.score !== 'undefined') this.score = data.score;
                                            if (typeof data.myVote !== 'undefined') this.myVote = data.myVote;
                                        } finally { this.busy = false; }
                                    }
                                }"
                                @click="window.location='{{ $threadUrl }}'"
                                @keydown.enter.prevent="window.location='{{ $threadUrl }}'"
                                @keydown.space.prevent="window.location='{{ $threadUrl }}'"
                                tabindex="0" role="button"
                                class="group p-5 bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-neutral-200 dark:border-neutral-800 cursor-pointer
                                       hover:border-neutral-400 dark:hover:border-neutral-600 hover:shadow-md transition-all duration-200
                                       focus:outline-none focus:ring-2 focus:ring-neutral-300 dark:focus:ring-neutral-600"
                            >
                                {{-- Title --}}
                                <a href="{{ $threadUrl }}"
                                   @click.stop
                                   class="block text-xl font-bold text-neutral-900 dark:text-white hover:underline underline-offset-4 decoration-2">
                                    {{ $title }}
                                </a>

                                {{-- Meta --}}
                                <div class="mt-1.5 flex items-center gap-2 text-sm">
                                    <span class="font-medium text-neutral-700 dark:text-neutral-300">{{ $thread->board->name ?? 'Board' }}</span>
                                    <span class="text-neutral-300 dark:text-neutral-600">•</span>
                                    <span class="text-neutral-500 dark:text-neutral-400">{{ $thread->created_at->diffForHumans() }}</span>
                                </div>

                                @if($excerpt)
                                    <p class="mt-3 text-neutral-700 dark:text-neutral-300 leading-relaxed">{{ $excerpt }}</p>
                                @endif

                                @if ($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="Image of {{ $title }}"
                                         class="mt-3 rounded-xl w-full max-h-[520px] object-cover border border-neutral-100 dark:border-neutral-800">
                                @endif

                                {{-- Actions --}}
                                <div class="mt-4 flex items-center justify-between">
                                    <a href="{{ $threadUrl }}#comments"
                                       @click.stop
                                       class="inline-flex items-center gap-2 px-3 h-9 rounded-xl border border-neutral-200 dark:border-neutral-700
                                              bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700
                                              text-neutral-700 dark:text-neutral-300 text-sm shadow-sm transition">
                                        Comment
                                    </a>

                                    <div class="flex items-center gap-2" @click.stop>
                                        <button @click.stop="vote(1)" :disabled="busy"
                                                class="h-8 w-8 grid place-items-center rounded-lg border shadow-sm transition text-sm font-medium"
                                                :class="myVote === 1 ? 'bg-black text-white border-black dark:bg-white dark:text-black dark:border-white' : 'bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 border-neutral-200 dark:border-neutral-700 hover:bg-neutral-100 dark:hover:bg-neutral-700'">
                                            ▲
                                        </button>

                                        <span class="w-8 text-center text-neutral-800 dark:text-neutral-200 font-bold text-sm" x-text="score"></span>

                                        <button @click.stop="vote(-1)" :disabled="busy"
                                                class="h-8 w-8 grid place-items-center rounded-lg border shadow-sm transition text-sm font-medium"
                                                :class="myVote === -1 ? 'bg-black text-white border-black dark:bg-white dark:text-black dark:border-white' : 'bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 border-neutral-200 dark:border-neutral-700 hover:bg-neutral-100 dark:hover:bg-neutral-700'">
                                            ▼
                                        </button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $threads->links() }}
                    </div>
                @endif
            </section>

            {{-- ===== RIGHT: placeholder ===== --}}
            <aside class="hidden xl:block col-span-2">
                {{-- Kosong sementara. --}}
            </aside>
        </div>
    </div>
</x-app-layout>
