<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ dark: localStorage.getItem('dark') === 'true' }"
      x-init="$watch('dark', v => { localStorage.setItem('dark', v) })"
      :class="{ 'dark': dark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-neutral-900 dark:text-neutral-100 antialiased bg-white dark:bg-neutral-950 min-h-screen">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

            {{-- Logo / Brand --}}
            <div class="flex flex-col items-center space-y-2">
                <a href="/" class="flex items-center gap-2">
                    <span class="text-2xl font-black tracking-tight text-black dark:text-white">
                        {{ config('app.name', 'OmongIn') }}
                    </span>
                </a>
                <p class="text-neutral-500 dark:text-neutral-400 text-sm">Welcome back, please sign in</p>
            </div>

            {{-- Card --}}
            <div class="w-full sm:max-w-md mt-8 px-6 py-6 bg-white dark:bg-neutral-900 rounded-2xl shadow-lg border border-neutral-200 dark:border-neutral-800">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
