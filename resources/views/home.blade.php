<x-app-layout>
    <h1 class="text-3xl font-black tracking-tight mb-1 text-black dark:text-white">Boards</h1>
    <p class="text-neutral-500 dark:text-neutral-400 text-sm mb-6">Choose a board to explore</p>

    <div class="grid sm:grid-cols-2 gap-4">
        @foreach ($boards as $b)
            <a href="{{ route('boards.show', $b) }}"
               class="hover-card group block p-5 transition-all duration-300">
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
</x-app-layout>
