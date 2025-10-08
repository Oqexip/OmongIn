{{-- resources/views/popular/index.blade.php --}}
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    $presets = [1 => '24h', 7 => '7d', 30 => '30d', 90 => '90d'];
@endphp

<x-layouts.app title="Popular Threads">
    <div class="mx-auto max-w-[1200px] px-4 py-6">
        <div class="grid grid-cols-12 gap-6">
            {{-- ===== LEFT: Sidebar Boards ===== --}}
            <aside class="hidden lg:block col-span-3">
                <div class="sticky top-20">
                    <div class="mb-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Boards</div>

                    <nav class="space-y-2">
                        @forelse ($boards as $board)
                            <a href="{{ route('boards.show', $board) }}"
                               class="flex items-center justify-between px-3 h-10 rounded-xl border text-sm
                                      bg-white border-slate-200 hover:bg-slate-50 shadow-sm">
                                <span class="truncate">{{ $board->name }}</span>
                                <span class="text-slate-400">→</span>
                            </a>
                        @empty
                            <div class="px-3 py-2 text-sm text-slate-500 rounded-xl border border-slate-200 bg-white">
                                Belum ada board.
                            </div>
                        @endforelse
                    </nav>
                </div>
            </aside>

            {{-- ===== CENTER: Feed Popular ===== --}}
            <section class="col-span-12 lg:col-span-6 xl:col-span-7">
                <h1 class="text-2xl font-bold mb-4">🔥 Popular Threads</h1>

                {{-- (Opsional) Filter waktu --}}
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    @foreach ($presets as $d => $label)
                        <a href="{{ route('popular.index', ['t' => $d]) }}"
                           class="inline-flex items-center px-3 h-9 rounded-xl border text-sm shadow-sm transition
                                  {{ $days == $d ? 'bg-sky-600 text-white border-sky-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                @if ($threads->isEmpty())
                    <p class="text-gray-500">Belum ada thread populer untuk periode ini.</p>
                @else
                    <div class="space-y-5">
                        @foreach ($threads as $thread)
                            @php
                                $title       = $thread->title ?? '(untitled)';
                                $excerpt     = Str::limit(strip_tags($thread->content), 240);
                                $userVote    = (int) ($thread->user_vote ?? 0);   // ← datang dari scope
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
                                class="p-5 bg-white rounded-2xl shadow border border-slate-100 cursor-pointer
                                       focus:outline-none focus:ring-2 focus:ring-sky-300"
                            >
                                {{-- Judul (klik di mana pun pada kartu juga navigasi) --}}
                                <a href="{{ $threadUrl }}"
                                   @click.stop
                                   class="block text-xl font-bold text-slate-900 hover:underline">
                                    {{ $title }}
                                </a>

                                <div class="mt-1 text-slate-500 font-medium">
                                    {{ $thread->board->name ?? 'Board' }}
                                    <span class="mx-2">•</span>
                                    <span class="font-normal">{{ $thread->created_at->diffForHumans() }}</span>
                                </div>

                                @if($excerpt)
                                    <p class="mt-3 text-slate-800">{{ $excerpt }}</p>
                                @endif

                                @if ($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="Image of {{ $title }}"
                                         class="mt-3 rounded-xl w-full max-h-[520px] object-cover">
                                @endif

                                <div class="mt-4 flex items-center justify-between">
                                    <a href="{{ $threadUrl }}#comments"
                                       @click.stop
                                       class="inline-flex items-center gap-2 px-3 h-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm shadow-sm">
                                        💬 Comment
                                    </a>

                                    <div class="flex items-center gap-3">
                                        <button @click.stop="vote(1)" :disabled="busy"
                                                class="h-8 w-8 grid place-items-center rounded-md border shadow-sm transition"
                                                :class="myVote === 1 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                                            ▲
                                        </button>

                                        <span class="w-6 text-center text-slate-800 font-semibold" x-text="score"></span>

                                        <button @click.stop="vote(-1)" :disabled="busy"
                                                class="h-8 w-8 grid place-items-center rounded-md border shadow-sm transition"
                                                :class="myVote === -1 ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
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

            {{-- ===== RIGHT: placeholder kosong dulu ===== --}}
            <aside class="hidden xl:block col-span-2">
                {{-- Kosong sementara. --}}
            </aside>
        </div>
    </div>
</x-layouts.app>
