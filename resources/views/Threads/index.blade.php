@php use Illuminate\Support\Str; @endphp

<x-app-layout :title="'/' . $board->slug">
    {{-- Header Board + tombol New Thread --}}
    <div x-data="{ open: false }" class="mb-6">
        <div class="flex items-center justify-between">
            <a href="#"
                class="inline-flex items-center gap-2 text-black dark:text-white text-2xl font-black tracking-tight hover:underline underline-offset-4 decoration-2">
                /{{ $board->slug }}
            </a>

            <button @click="open = true"
                class="px-4 py-2 rounded-xl text-white bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 text-sm font-medium transition shadow-sm">
                + New Thread
            </button>
        </div>

        {{-- Teleport modal --}}
        <template x-teleport="body">
            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50 dark:bg-black/70" @click="open = false" aria-hidden="true"></div>

                <div x-show="open" x-transition @keydown.escape.window="open = false"
                    class="relative z-50 bg-white dark:bg-neutral-900 p-6 rounded-xl w-full max-w-lg shadow-2xl border border-neutral-200 dark:border-neutral-800">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-black dark:text-white">Buat Thread Baru</h2>
                        <button @click="open=false" class="p-1 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-500"
                            aria-label="Tutup">✕</button>
                    </div>

                    <form method="POST" action="{{ route('threads.store', $board) }}" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        <input name="title" placeholder="Judul" required
                            class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3.5 py-2.5 text-[15px]
                               text-neutral-900 dark:text-neutral-100 placeholder:text-neutral-400 dark:placeholder:text-neutral-500 shadow-sm
                               focus:outline-none focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700" />

                        @if (isset($categories) && $categories->count())
                            <div class="relative">
                                <select name="category_id"
                                    class="peer w-full appearance-none rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3.5 py-2.5 pr-10
                                       text-[15px] text-neutral-900 dark:text-neutral-100 shadow-sm
                                       focus:outline-none focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700">
                                    <option value="">Pilih kategori (opsional)</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id || request('category') === $cat->slug)>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <svg class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-500"
                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.17l3.71-2.94a.75.75 0 01.92 1.18l-4.25 3.38a.75.75 0 01-.92 0L5.21 8.41a.75.75 0 01.02-1.2z" />
                                </svg>
                            </div>
                        @endif

                        <textarea name="content" placeholder="Isi thread" required rows="4"
                            class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3.5 py-2.5 text-[15px]
                               text-neutral-900 dark:text-neutral-100 placeholder:text-neutral-400 dark:placeholder:text-neutral-500 shadow-sm
                               focus:outline-none focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700"></textarea>

                        <div class="space-y-2">
                            <input id="images-input-modal" type="file" name="images[]" accept="image/*" multiple
                                class="hidden" onchange="__updateModalImages(this)" />

                            <div class="flex flex-wrap items-center gap-3">
                                <label for="images-input-modal"
                                    class="inline-flex items-center gap-2 h-10 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 text-sm
                                          text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700 shadow-sm cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-500"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M20 6h-3.586l-1.707-1.707A.996.996 0 0 0 14 4h-4c-.265 0-.52.105-.707.293L7.586 6H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2zM12 19a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                                    </svg>
                                    <span id="images-label-modal">Attach images</span>
                                </label>

                                <button type="button"
                                    class="h-10 px-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm text-neutral-600 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-700 shadow-sm"
                                    onclick="__clearModalImages()">
                                    Clear
                                </button>
                            </div>

                            <div id="images-preview-modal" class="grid grid-cols-3 gap-2"></div>

                            <p class="text-xs text-neutral-400 dark:text-neutral-500">
                                Format: jpg, jpeg, png, webp, gif • Maks 4MB per gambar
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <button type="button"
                                class="h-10 px-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700 shadow-sm"
                                @click="open=false">
                                Batal
                            </button>
                            <!-- Submit & NSFW -->
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <div class="relative flex items-center">
                                            <input type="checkbox" name="is_spoiler" value="1"
                                                class="peer appearance-none w-5 h-5 border-2 border-neutral-300 dark:border-neutral-600 rounded bg-white dark:bg-neutral-800 checked:bg-black checked:border-black dark:checked:bg-white dark:checked:border-white transition-all cursor-pointer">
                                            <svg class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white dark:text-black opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400 group-hover:text-black dark:group-hover:text-white transition-colors">
                                            Spoiler
                                        </span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <div class="relative flex items-center">
                                            <input type="checkbox" name="is_nsfw" value="1"
                                                class="peer appearance-none w-5 h-5 border-2 border-neutral-300 dark:border-neutral-600 rounded bg-white dark:bg-neutral-800 checked:bg-black checked:border-black dark:checked:bg-white dark:checked:border-white transition-all cursor-pointer">
                                            <svg class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white dark:text-black opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400 group-hover:text-black dark:group-hover:text-white transition-colors">
                                            NSFW
                                        </span>
                                    </label>
                                </div>

                                <button type="submit"
                                    class="h-10 px-5 rounded-xl text-white shadow-sm font-medium
                                   bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
                                    Post Thread
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    {{-- Search + Filter --}}
    <form action="{{ route('boards.show', $board) }}" method="GET" class="mb-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari thread..."
                    class="w-full h-11 shadow-sm rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-4 pr-10 text-sm
                           text-neutral-900 dark:text-neutral-100 outline-none
                           focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 transition" />
                @if (request('q'))
                    <a href="{{ route('boards.show', $board) }}"
                        class="absolute right-10 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300">✕</a>
                @endif
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2" aria-label="Cari">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-500 dark:text-neutral-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7" stroke-width="1.6" />
                        <path d="M20 20l-3.5-3.5" stroke-width="1.6" />
                    </svg>
                </button>
            </div>

            @if (!empty($categories) && $categories->count())
                <select name="category"
                    class="h-11 rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 text-sm
                           text-neutral-900 dark:text-neutral-100 outline-none
                           focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 transition w-full sm:w-48">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <select name="sort"
                class="h-11 rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 text-sm
                       text-neutral-900 dark:text-neutral-100 outline-none
                       focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 transition w-full sm:w-48">
                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Terbaru</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
                <option value="most_liked" @selected(request('sort') === 'most_liked')>Terbanyak Disukai</option>
                <option value="most_active" @selected(request('sort') === 'most_active')>Paling Ramai</option>
            </select>

            <button
                class="h-11 px-6 rounded-xl text-white shadow-sm bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 font-medium text-sm transition">
                Terapkan
            </button>
        </div>
    </form>

    {{-- Category Pills --}}
    @if (!empty($categories) && $categories->count())
        <div class="mb-4 flex flex-wrap gap-2">
            <a href="{{ route('boards.show', $board) }}"
                class="px-3 py-1.5 rounded-full text-xs font-medium border transition
                       {{ request('category') ? 'border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 bg-white dark:bg-neutral-800' : 'border-black dark:border-white text-black dark:text-white bg-neutral-100 dark:bg-neutral-800' }}">
                All
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('boards.show', $board) }}?category={{ $cat->slug }}@if (request('q')) &q={{ urlencode(request('q')) }} @endif"
                    class="px-3 py-1.5 rounded-full text-xs font-medium border transition
                           {{ request('category') === $cat->slug ? 'border-black dark:border-white text-black dark:text-white bg-neutral-100 dark:bg-neutral-800' : 'border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 bg-white dark:bg-neutral-800 hover:border-neutral-400 dark:hover:border-neutral-500' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Threads List --}}
    <div class="space-y-3">
        @forelse ($threads as $t)
            <a href="{{ route('threads.show', $t) }}" class="group hover-card block p-4 transition">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 flex items-center flex-wrap gap-2">
                        @if($t->is_pinned)
                            <span class="text-xs font-bold px-2 py-0.5 rounded flex items-center gap-1 
                                         bg-neutral-900 text-white dark:bg-white dark:text-black">
                                PINNED 📍
                            </span>
                        @endif
                        @if($t->is_nsfw)
                            <span class="text-xs font-bold px-2 py-0.5 rounded border border-red-500 text-red-600 dark:border-red-400 dark:text-red-400">
                                NSFW
                            </span>
                        @endif
                        <span class="transition group-hover:underline underline-offset-4 decoration-2 decoration-neutral-400 dark:decoration-neutral-500">
                            {{ $t->title ?? \App\Support\Sanitize::excerpt($t->content, 80) }}
                        </span>

                        @if ($t->category)
                            <div class="mt-1">
                                <span
                                    class="inline-block text-[11px] px-2 py-0.5 rounded-full border border-neutral-300 dark:border-neutral-600 bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 font-medium">
                                    {{ $t->category->name }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-1 text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-4 flex-wrap">
                    <span class="inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-neutral-400 dark:text-neutral-500" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path d="M20 2H4a2 2 0 00-2 2v13.586L6.586 14H20a2 2 0 002-2V4a2 2 0 00-2-2z" />
                        </svg>
                        {{ $t->comment_count }} comments
                    </span>

                    <span class="inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-neutral-400 dark:text-neutral-500" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path d="M12 1a11 11 0 1011 11A11.013 11.013 0 0012 1zm1 11H7V9h4V5h2z" />
                        </svg>
                        {{ $t->created_at->diffForHumans() }}
                    </span>
                </div>
            </a>
        @empty
            <div class="text-neutral-500 dark:text-neutral-400">No threads yet</div>
        @endforelse
    </div>

    <div class="mt-5">
        {{ $threads->links() }}
    </div>

    <script>
        function __updateModalImages(input) {
            const label = document.getElementById('images-label-modal');
            const preview = document.getElementById('images-preview-modal');
            const files = Array.from(input.files || []);

            if (label) {
                if (files.length === 0) label.textContent = 'Attach images';
                else {
                    const names = files.slice(0, 2).map(f => f.name).join(', ');
                    const more = files.length > 2 ? ` +${files.length - 2} more` : '';
                    label.textContent = names + more;
                }
            }

            if (!preview) return;
            preview.innerHTML = '';
            const toShow = files.slice(0, 6);
            toShow.forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = file.name;
                    img.className = 'w-full h-24 object-cover rounded-md';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }

        function __clearModalImages() {
            const input = document.getElementById('images-input-modal');
            const label = document.getElementById('images-label-modal');
            const preview = document.getElementById('images-preview-modal');
            if (input) input.value = '';
            if (label) label.textContent = 'Attach images';
            if (preview) preview.innerHTML = '';
        }
    </script>
</x-app-layout>
