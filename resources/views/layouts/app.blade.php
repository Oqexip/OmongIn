<!doctype html>
<html lang="id" class="h-full"
      x-data="{ dark: localStorage.getItem('dark') === 'true' }"
      x-init="$watch('dark', v => { localStorage.setItem('dark', v) })"
      :class="{ 'dark': dark }">
<head>
    <!-- Prevent FOUC (Flash of Unstyled Content) for Dark Mode -->
    <script>
        if (localStorage.getItem('dark') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'OmongIn' }}</title>

    {{-- Inter font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>

<body class="min-h-full bg-white text-neutral-900 antialiased font-sans
             dark:bg-neutral-950 dark:text-neutral-100">

  {{-- ===== NAVBAR ===== --}}
  <header class="sticky top-0 z-40 border-b border-neutral-200 bg-white/80 backdrop-blur-xl
                 dark:border-neutral-800 dark:bg-neutral-950/80">
    <div class="max-w-6xl mx-auto px-4">

      {{-- ===== DESKTOP (md+) ===== --}}
      <div class="h-14 hidden md:grid grid-cols-3 items-center">
        {{-- Kiri: Brand --}}
        <div class="flex items-center">
          <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
            <span class="font-black text-2xl tracking-tight text-black dark:text-white">
              OmongIn
            </span>
          </a>
        </div>

        {{-- Tengah: Menu utama (center) --}}
        <nav class="flex items-stretch justify-center gap-6">
          <a href="{{ route('home') }}"
             class="inline-flex items-center text-sm font-medium px-1 pb-2 border-b-2 transition
                    {{ request()->routeIs('home') ? 'text-black dark:text-white border-black dark:border-white' : 'text-neutral-500 dark:text-neutral-400 border-transparent hover:text-black dark:hover:text-white hover:border-neutral-300 dark:hover:border-neutral-600' }}">
            Boards
          </a>
          <a href="{{ route('popular.index') }}"
             class="inline-flex items-center text-sm font-medium px-1 pb-2 border-b-2 transition
                    {{ request()->routeIs('popular.*') ? 'text-black dark:text-white border-black dark:border-white' : 'text-neutral-500 dark:text-neutral-400 border-transparent hover:text-black dark:hover:text-white hover:border-neutral-300 dark:hover:border-neutral-600' }}">
            Popular
          </a>
          <a href="{{ route('search.index') }}"
             class="inline-flex items-center text-sm font-medium px-1 pb-2 border-b-2 transition
                    {{ request()->routeIs('search.*') ? 'text-black dark:text-white border-black dark:border-white' : 'text-neutral-500 dark:text-neutral-400 border-transparent hover:text-black dark:hover:text-white hover:border-neutral-300 dark:hover:border-neutral-600' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="7" stroke-width="1.6" />
              <path d="M20 20l-3.5-3.5" stroke-width="1.6" />
            </svg>
            Search
          </a>
        </nav>

        {{-- Kanan: Auth + Dark toggle --}}
        <nav class="flex items-center justify-end gap-2">
          {{-- Dark mode toggle --}}
          <button @click="dark = !dark"
                  class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-neutral-200 dark:border-neutral-700
                         bg-white dark:bg-neutral-800 hover:bg-neutral-100 dark:hover:bg-neutral-700 shadow-sm transition"
                  :title="dark ? 'Light mode' : 'Dark mode'">
            <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="5"/><path d="M12 1v2m0 18v2m11-11h-2M3 12H1m16.07-7.07l-1.41 1.41M7.34 16.66l-1.41 1.41m12.14 0l-1.41-1.41M7.34 7.34L5.93 5.93"/>
            </svg>
            <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-600" viewBox="0 0 24 24" fill="currentColor">
              <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
            </svg>
          </button>

          @auth
            <span class="text-sm text-neutral-500 dark:text-neutral-400 mr-1">Hi, <span class="font-medium text-neutral-900 dark:text-white">{{ auth()->user()->name }}</span></span>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button
                class="inline-flex items-center gap-2 px-3 h-9 rounded-xl text-white text-sm shadow-sm transition
                       bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
                Logout
              </button>
            </form>
          @else
            <a href="{{ route('login') }}"
               class="inline-flex items-center px-3 h-9 rounded-xl border border-neutral-200 dark:border-neutral-700
                      bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700
                      text-neutral-700 dark:text-neutral-300 text-sm shadow-sm transition">
              Login
            </a>
            <a href="{{ route('register') }}"
               class="inline-flex items-center px-3 h-9 rounded-xl text-white text-sm shadow-sm transition
                      bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
              Register
            </a>
          @endauth
        </nav>
      </div>

      {{-- ===== MOBILE (< md) ===== --}}
      <div class="md:hidden" x-data="{ open: false }" @keydown.escape.window="open=false">
        <div class="h-14 flex items-center justify-between">
          {{-- Brand (mobile only) --}}
          <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
            <span class="font-black text-xl tracking-tight text-black dark:text-white">
              OmongIn
            </span>
          </a>

          <div class="flex items-center gap-2">
            {{-- Dark mode toggle (mobile) --}}
            <button @click="dark = !dark"
                    class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 hover:bg-neutral-100 dark:hover:bg-neutral-700 shadow-sm">
              <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="5"/><path d="M12 1v2m0 18v2m11-11h-2M3 12H1m16.07-7.07l-1.41 1.41M7.34 16.66l-1.41 1.41m12.14 0l-1.41-1.41M7.34 7.34L5.93 5.93"/>
              </svg>
              <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-600" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
              </svg>
            </button>

            {{-- Hamburger --}}
            <button @click="open=!open"
                    class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700 shadow-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-700 dark:text-neutral-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
              <span class="sr-only">Menu</span>
            </button>
          </div>
        </div>

        {{-- Sheet --}}
        <div x-show="open" x-cloak @click.outside="open=false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="absolute left-0 right-0 mt-2 rounded-2xl border border-neutral-200 dark:border-neutral-800
                    bg-white dark:bg-neutral-900 shadow-lg p-3 mx-4">
          {{-- Menu utama --}}
          <a href="{{ route('home') }}"
             class="block px-3 h-10 leading-10 rounded-xl border border-neutral-200 dark:border-neutral-700
                    bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700 text-sm shadow-sm mb-2 text-center
                    {{ request()->routeIs('home') ? 'text-black dark:text-white font-semibold' : 'text-neutral-600 dark:text-neutral-400' }}">
            Boards
          </a>
          <a href="{{ route('popular.index') }}"
             class="block px-3 h-10 leading-10 rounded-xl border border-neutral-200 dark:border-neutral-700
                    bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700 text-sm shadow-sm mb-2 text-center
                    {{ request()->routeIs('popular.*') ? 'text-black dark:text-white font-semibold' : 'text-neutral-600 dark:text-neutral-400' }}">
            Popular
          </a>

          {{-- Search (mobile) --}}
          <form action="{{ route('search.index') }}" method="GET" class="mb-3">
            <div class="relative">
              <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari..."
                     class="w-full h-10 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800
                            px-3 pr-10 text-sm text-neutral-900 dark:text-neutral-100 placeholder:text-neutral-400 dark:placeholder:text-neutral-500
                            outline-none focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 shadow-sm" />
              <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2" aria-label="Cari">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-500 dark:text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <circle cx="11" cy="11" r="7" stroke-width="1.6" />
                  <path d="M20 20l-3.5-3.5" stroke-width="1.6" />
                </svg>
              </button>
            </div>
          </form>

          {{-- Auth --}}
          @auth
            <div class="px-2 py-2 text-sm text-neutral-500 dark:text-neutral-400">Hi, <span class="font-medium text-neutral-900 dark:text-white">{{ auth()->user()->name }}</span></div>
            <form method="POST" action="{{ route('logout') }}" class="px-2 pb-2">
              @csrf
              <button
                class="w-full inline-flex items-center justify-center px-3 h-10 rounded-xl border border-neutral-200 dark:border-neutral-700
                       bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 text-sm shadow-sm">
                Logout
              </button>
            </form>
          @else
            <a href="{{ route('login') }}"
               class="block px-3 h-10 leading-10 rounded-xl border border-neutral-200 dark:border-neutral-700
                      bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 text-sm shadow-sm mb-2 text-center">
              Login
            </a>
            <a href="{{ route('register') }}"
               class="block px-3 h-10 leading-10 rounded-xl text-white text-sm text-center shadow-sm
                      bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
              Register
            </a>
          @endauth
        </div>
      </div>

    </div>
  </header>

  {{-- ===== CONTENT ===== --}}
  <main class="max-w-6xl mx-auto px-4 py-6">
    @if (session('ok'))
      <div x-data="{ show: true }" x-show="show" x-cloak x-transition.opacity.duration.200ms
           class="mb-4 flex w-full items-center justify-between rounded-2xl border border-emerald-200/60 bg-emerald-50/80 px-4 py-3 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 shadow-sm">
        <div class="flex items-center gap-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2a10 10 0 1010 10A10.011 10.011 0 0012 2zm-1 15l-4-4 1.414-1.414L11 13.172l5.586-5.586L18 9z"/>
          </svg>
          <span>{{ session('ok') ?? 'Posted' }}</span>
        </div>
        <button @click="show=false"
                class="ml-4 inline-flex items-center justify-center rounded-lg px-2 py-1 hover:bg-emerald-100/70 dark:hover:bg-emerald-900/50"
                aria-label="Tutup">&times;</button>
      </div>
    @endif

    {{ $slot }}
  </main>

  {{-- ===== FOOTER ===== --}}
  <footer class="border-t border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950">
    <div class="max-w-6xl mx-auto px-4 py-6 text-sm text-neutral-500 dark:text-neutral-400 flex flex-col sm:flex-row items-center justify-between gap-2">
      <p>&copy; {{ date('Y') }} OmongIn</p>
      <p>
        <span class="text-neutral-400 dark:text-neutral-500">Made by</span>
        <span class="font-bold tracking-tight text-black dark:text-white">Oqexip</span>
      </p>
    </div>
  </footer>

</body>
</html>
