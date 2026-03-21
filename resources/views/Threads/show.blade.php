{{-- resources/views/threads/show.blade.php --}}
<x-app-layout :title="$thread->title ?? 'Thread'">
    @php
        $isAuthUser = (bool) $thread->user;
        $h = !$isAuthUser ? \App\Support\Anon::handleForThread($thread->id, $thread->anon_session_id) : null;
        $palette = ['rose','orange','amber','emerald','teal','sky','violet','pink','yellow','lime','cyan','blue','indigo','purple','fuchsia'];
        $color = isset($h['color'], $palette[$h['color']]) ? $palette[$h['color']] : 'gray';
        $canEditThread = $thread->canEditNow() && $thread->isOwnedByRequest(request());
    @endphp

    {{-- Header Board --}}
    <div class="mb-6">
        <a href="{{ route('boards.show', $thread->board) }}"
           class="inline-flex items-center gap-2 text-black dark:text-white text-2xl font-black tracking-tight hover:underline underline-offset-4 decoration-2">
            /{{ $thread->board->slug }}
        </a>
    </div>

    {{-- Flash errors --}}
    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-neutral-300 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900 px-4 py-3 text-neutral-700 dark:text-neutral-300 shadow-sm">
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- THREAD CARD --}}
    <article x-data="{ openEditThread: false, menuOpen: false }"
             :class="menuOpen ? 'relative z-50' : 'relative z-10'"
             class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-neutral-200 dark:border-neutral-800 p-6">

        {{-- HEADER --}}
        <header class="mb-4 flex items-start justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-black dark:text-white">
                    {{ $thread->title ?? 'Thread' }}
                </h1>
                <div class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-2">
                    @if ($isAuthUser)
                        <span class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $thread->user->name }}</span>
                    @else
                        <span class="font-semibold {{ 'text-' . $color . '-600' }}">{{ $h['name'] ?? 'Anon' }}</span>
                    @endif

                    <span aria-hidden="true" class="text-neutral-300 dark:text-neutral-600">•</span>
                    <time datetime="{{ $thread->created_at->toIso8601String() }}">
                        {{ $thread->created_at->diffForHumans() }}
                    </time>

                    @if ($thread->edited_at)
                        <span class="text-neutral-400 dark:text-neutral-500 text-xs" title="Last edited {{ $thread->edited_at->diffForHumans() }}">
                            (edited)
                        </span>
                    @endif
                </div>
            </div>

            {{-- Action menu --}}
            <div class="relative" @keydown.escape.window="menuOpen=false" @click.outside="menuOpen=false">
                <button @click="menuOpen = !menuOpen"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 focus:outline-none transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-neutral-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 14a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
                    </svg>
                </button>

                <div x-show="menuOpen" x-cloak
                     class="absolute right-0 top-full mt-2 w-44 rounded-xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 shadow-xl py-1 z-50 overflow-hidden">
                    @if ($canEditThread)
                        <button type="button"
                                @click="menuOpen=false; openEditThread = !openEditThread"
                                class="w-full flex items-center gap-2 px-3 py-2 text-sm hover:bg-neutral-50 dark:hover:bg-neutral-800 text-neutral-700 dark:text-neutral-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-neutral-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" />
                            </svg>
                            <span x-show="!openEditThread">Edit</span>
                            <span x-show="openEditThread" x-cloak>Cancel edit</span>
                        </button>
                    @endif

                    <button type="button"
                            @click="menuOpen=false; navigator.clipboard.writeText('{{ route('threads.show', $thread) }}')"
                            class="w-full flex items-center gap-2 px-3 py-2 text-sm hover:bg-neutral-50 dark:hover:bg-neutral-800 text-neutral-700 dark:text-neutral-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-neutral-500" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8 13h8v-2H8v2z" />
                            <path d="M3.9 12a3.9 3.9 0 013.9-3.9h2v1.8h-2a2.1 2.1 0 000 4.2h2v1.8h-2A3.9 3.9 0 013.9 12zM16.2 8.1h-2v1.8h2a2.1 2.1 0 010 4.2h-2v1.8h2a3.9 3.9 0 000-7.8z" />
                        </svg>
                        Save
                    </button>

                    @can('delete', $thread)
                        <form method="POST" action="{{ route('threads.destroy', $thread) }}"
                              onsubmit="return confirm('Delete this thread?')">
                            @csrf @method('DELETE')
                            <button class="w-full flex items-center gap-2 px-3 py-2 text-sm hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 7h12v13a2 2 0 01-2 2H8a2 2 0 01-2-2V7zm3-4h6l1 1h4v2H4V4h4l1-1z" />
                                </svg>
                                Delete
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </header>

        {{-- VIEW MODE --}}
        <div x-show="!openEditThread" x-cloak>
            <div class="prose dark:prose-invert max-w-none">
                {!! \App\Support\Sanitize::toHtml($thread->content) !!}
            </div>

            @if ($thread->attachments->isNotEmpty())
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach ($thread->attachments as $a)
                        <a href="{{ Storage::url($a->path) }}" target="_blank" class="block">
                            <img src="{{ Storage::url($a->path) }}"
                                 class="rounded-xl object-cover w-full max-h-64 shadow border border-neutral-100 dark:border-neutral-800" loading="lazy" alt="">
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="mt-4 flex justify-end">
                <x-vote :model="$thread" type="thread" />
            </div>
        </div>

        {{-- EDIT MODE --}}
        @if ($canEditThread)
            <form x-show="openEditThread" x-cloak method="POST" action="{{ route('threads.update', $thread) }}"
                  class="mt-4 space-y-3">
                @csrf @method('PATCH')
                <input type="text" name="title" value="{{ old('title', $thread->title) }}"
                       class="w-full rounded-xl border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800
                              text-neutral-900 dark:text-neutral-100
                              focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-neutral-200 dark:focus:ring-neutral-700"
                       placeholder="Title" required>
                <textarea name="content" rows="4"
                          class="w-full rounded-xl border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800
                                 text-neutral-900 dark:text-neutral-100
                                 focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-neutral-200 dark:focus:ring-neutral-700"
                          required>{{ old('content', $thread->content) }}</textarea>
                <div class="flex gap-2">
                    <button
                        class="px-4 py-2 rounded-xl text-white bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 shadow-sm font-medium">
                        Save
                    </button>
                    <button type="button" class="px-4 py-2 rounded-xl bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300"
                            @click="openEditThread=false">
                        Cancel
                    </button>
                </div>
            </form>
        @endif
    </article>

    {{-- FORM KOMENTAR BARU --}}
    @if (!$thread->is_locked)
        <section class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-6 mt-6">
            <form action="{{ route('comments.store', $thread) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <textarea name="content" rows="4"
                          class="w-full rounded-xl border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800
                                 text-neutral-900 dark:text-neutral-100 placeholder:text-neutral-400 dark:placeholder:text-neutral-500
                                 focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-neutral-200 dark:focus:ring-neutral-700"
                          placeholder="Write a comment" required></textarea>

                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-2">
                        <input id="images-input" type="file" name="images[]" accept="image/*" multiple class="hidden"
                               onchange="__updateFileLabel(this)">
                        <label for="images-input"
                               class="inline-flex items-center gap-2 px-3 h-10 rounded-xl bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 cursor-pointer text-sm text-neutral-700 dark:text-neutral-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 6h-3.586l-1.707-1.707A.996.996 0 0 0 14 4h-4L7.586 6H4c-1.103 0-2 .897-2 2v10a2 2 0 0 0 2 2h16c1.103 0 2-.897 2-2V8c0-1.103-.897-2-2-2zM12 19a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                            </svg>
                            <span id="images-label">Attach images</span>
                        </label>
                        <button type="button" onclick="__clearFiles()"
                                class="px-2 h-10 text-sm text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200">
                            Clear
                        </button>
                    </div>
                    <button type="submit"
                            class="px-5 h-10 rounded-xl text-white bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 shadow-sm font-medium">
                        Comment
                    </button>
                </div>
            </form>
        </section>
    @endif

    {{-- LIST KOMENTAR --}}
    @php($grouped = $grouped ?? $thread->comments->groupBy('parent_id'))
    <section class="space-y-4 mt-6">
        @foreach ($grouped[null] ?? [] as $c)
            @include('threads.partials.comment', ['c' => $c, 'grouped' => $grouped, 'thread' => $thread])
        @endforeach
    </section>

    <script>
        function __updateFileLabel(input) {
            const label = document.getElementById('images-label');
            if (!label) return;
            const files = Array.from(input.files || []);
            label.textContent = files.length === 0
                ? 'Attach images'
                : files.slice(0, 2).map(f => f.name).join(', ') + (files.length > 2 ? ` +${files.length - 2} more` : '');
        }
        function __clearFiles() {
            const input = document.getElementById('images-input');
            if (!input) return;
            input.value = '';
            __updateFileLabel(input);
        }
    </script>
</x-app-layout>
