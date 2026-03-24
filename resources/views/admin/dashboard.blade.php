{{-- resources/views/admin/dashboard.blade.php --}}
<x-app-layout title="Admin Dashboard">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-black dark:text-white">Admin Dashboard</h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-2">Ringkasan metrik utama dan aktivitas moderasi platform.</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
        @php
            $cards = [
                [
                    'label' => 'Total Users',
                    'value' => $stats['users'],
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />'
                ],
                [
                    'label' => 'Total Threads',
                    'value' => $stats['threads'],
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />'
                ],
                [
                    'label' => 'Total Komentar',
                    'value' => $stats['comments'],
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />'
                ],
                [
                    'label' => 'Papan Aktif',
                    'value' => $stats['boards'],
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />'
                ],
                [
                    'label' => 'Laporan Open',
                    'value' => $stats['open_reports'],
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a14.451 14.451 0 014.66 0l2.767.693a9.042 9.042 0 006.873-1.15l2.436-1.565" />',
                    'highlight' => $stats['open_reports'] > 0
                ],
                [
                    'label' => 'User Banned',
                    'value' => $stats['banned_users'],
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />'
                ],
                [
                    'label' => 'Banding Pending',
                    'value' => $stats['pending_appeals'],
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />',
                    'highlight' => $stats['pending_appeals'] > 0
                ],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5 shadow-sm hover:shadow-md transition duration-200 group relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-3xl font-bold tracking-tight text-black dark:text-white {{ !empty($card['highlight']) ? 'text-rose-500 dark:text-rose-400' : '' }}">
                            {{ $card['value'] }}
                        </div>
                        <div class="text-xs font-medium text-neutral-500 dark:text-neutral-400 mt-1 uppercase tracking-wider">
                            {{ $card['label'] }}
                        </div>
                    </div>
                    <div class="p-2 rounded-xl {{ !empty($card['highlight']) ? 'bg-rose-50 text-rose-500 dark:bg-rose-500/10 dark:text-rose-400' : 'bg-neutral-50 text-neutral-400 dark:bg-neutral-800 dark:text-neutral-500' }} group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            {!! $card['icon'] !!}
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-8">
        {{-- Quick Links (2 columns wide) --}}
        <div class="lg:col-span-2 space-y-4">
            <h2 class="text-xl font-bold text-black dark:text-white mb-4">Akses Cepat</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <a href="{{ route('admin.reports.index') }}"
                   class="flex flex-col p-5 rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-black dark:hover:border-white shadow-sm transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2.5 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-neutral-300 dark:text-neutral-700 group-hover:text-black dark:group-hover:text-white transition transform group-hover:translate-x-1"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="font-bold text-lg text-black dark:text-white mb-1">Manajemen Laporan</div>
                    <div class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Tinjau {{ $stats['open_reports'] }} laporan konten menunggu.</div>
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="flex flex-col p-5 rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-black dark:hover:border-white shadow-sm transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2.5 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-neutral-300 dark:text-neutral-700 group-hover:text-black dark:group-hover:text-white transition transform group-hover:translate-x-1"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="font-bold text-lg text-black dark:text-white mb-1">Manajemen Users</div>
                    <div class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Kelola {{ $stats['users'] }} pengguna aktif.</div>
                </a>

                <a href="{{ route('admin.boards.index') }}"
                   class="flex flex-col p-5 rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-black dark:hover:border-white shadow-sm transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2.5 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-neutral-300 dark:text-neutral-700 group-hover:text-black dark:group-hover:text-white transition transform group-hover:translate-x-1"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="font-bold text-lg text-black dark:text-white mb-1">Manajemen Boards</div>
                    <div class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Buat, modifikasi, dan atur moderator pada {{ $stats['boards'] }} papan.</div>
                </a>

                <a href="{{ route('admin.appeals.index') }}"
                   class="flex flex-col p-5 rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-black dark:hover:border-white shadow-sm transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2.5 rounded-xl {{ $stats['pending_appeals'] > 0 ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-neutral-300 dark:text-neutral-700 group-hover:text-black dark:group-hover:text-white transition transform group-hover:translate-x-1"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="font-bold text-lg text-black dark:text-white mb-1">Kelola Banding</div>
                    <div class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">{{ $stats['pending_appeals'] }} banding menunggu peninjauan.</div>
                </a>
            </div>
        </div>

        {{-- Recent Reports (1 column wide) --}}
        <div>
            <h2 class="text-xl font-bold text-black dark:text-white mb-4">Laporan Terbaru</h2>
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-2">
                @if ($recentReports->count() > 0)
                    <div class="divide-y divide-neutral-100 dark:divide-neutral-800/50">
                        @foreach ($recentReports as $report)
                            <a href="{{ route('admin.reports.show', $report) }}"
                               class="flex items-center justify-between p-4 bg-transparent hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition duration-150 rounded-xl">
                                <div>
                                    <div class="font-semibold text-sm text-black dark:text-white mb-0.5">
                                        {{ class_basename($report->reportable_type) }} #{{ $report->reportable_id }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold uppercase tracking-wider text-rose-500 dark:text-rose-400">
                                            {{ $report->reason }}
                                        </span>
                                        <span class="text-[10px] text-neutral-400 dark:text-neutral-500">
                                            {{ $report->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-neutral-400 dark:text-neutral-600"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mx-auto text-neutral-300 dark:text-neutral-700 mb-3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Semua laporan telah ditangani.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
