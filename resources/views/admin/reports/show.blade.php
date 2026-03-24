{{-- resources/views/admin/reports/show.blade.php --}}
<x-app-layout title="Laporan #{{ $report->id }} — Admin">
    <div class="mb-6">
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-neutral-500 dark:text-neutral-400 hover:text-black dark:hover:text-white transition">← Semua Laporan</a>
        <h1 class="text-2xl font-bold tracking-tight text-black dark:text-white mt-1">Laporan #{{ $report->id }}</h1>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Report Details --}}
        <section class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-black dark:text-white mb-4">Detail Laporan</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-neutral-500 dark:text-neutral-400">Tipe Konten</dt>
                    <dd class="font-medium text-black dark:text-white">{{ class_basename($report->reportable_type) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-neutral-500 dark:text-neutral-400">ID Konten</dt>
                    <dd class="font-mono text-black dark:text-white">#{{ $report->reportable_id }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-neutral-500 dark:text-neutral-400">Alasan</dt>
                    <dd>
                        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium"
                              style="color: {{ $report->reason === 'spam' ? '#f59e0b' : ($report->reason === 'abuse' ? '#ef4444' : ($report->reason === 'nsfw' ? '#a855f7' : '#6b7280')) }};
                                     border: 1px solid currentColor;">
                            {{ strtoupper($report->reason) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-neutral-500 dark:text-neutral-400">Status</dt>
                    <dd class="font-semibold" style="color: {{ $report->status === 'open' ? '#f59e0b' : ($report->status === 'reviewed' ? '#10b981' : '#6b7280') }}">
                        {{ ucfirst($report->status) }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-neutral-500 dark:text-neutral-400">Dilaporkan</dt>
                    <dd class="text-black dark:text-white">{{ $report->created_at->format('d M Y H:i') }}</dd>
                </div>
                @if ($report->notes)
                    <div>
                        <dt class="text-neutral-500 dark:text-neutral-400 mb-1">Catatan Pelapor</dt>
                        <dd class="text-black dark:text-white bg-neutral-50 dark:bg-neutral-800 rounded-lg p-3 text-sm">{{ $report->notes }}</dd>
                    </div>
                @endif
                @if ($report->resolvedBy)
                    <div class="flex justify-between">
                        <dt class="text-neutral-500 dark:text-neutral-400">Ditangani oleh</dt>
                        <dd class="text-black dark:text-white">{{ $report->resolvedBy->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-neutral-500 dark:text-neutral-400">Tanggal ditangani</dt>
                        <dd class="text-black dark:text-white">{{ $report->resolved_at->format('d M Y H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </section>

        {{-- Reported Content Preview --}}
        <section class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-black dark:text-white mb-4">Konten yang Dilaporkan</h2>
            @if ($report->reportable)
                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-4 text-sm text-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-neutral-700">
                    @if ($report->reportable_type === 'App\\Models\\Thread')
                        <div class="font-semibold mb-2">{{ $report->reportable->title }}</div>
                        <div class="text-neutral-600 dark:text-neutral-400">{{ Str::limit(strip_tags($report->reportable->content), 300) }}</div>
                        <a href="{{ route('threads.show', $report->reportable) }}" class="inline-block mt-2 text-xs text-neutral-500 hover:text-black dark:hover:text-white transition">Lihat thread →</a>
                    @elseif ($report->reportable_type === 'App\\Models\\Comment')
                        <div class="text-neutral-600 dark:text-neutral-400">{{ Str::limit(strip_tags($report->reportable->content), 300) }}</div>
                        @if ($report->reportable->thread_id)
                            <a href="{{ route('threads.show', $report->reportable->thread_id) }}" class="inline-block mt-2 text-xs text-neutral-500 hover:text-black dark:hover:text-white transition">Lihat thread →</a>
                        @endif
                    @else
                        <p class="text-neutral-500">Pratinjau tidak tersedia.</p>
                    @endif
                </div>
            @else
                <p class="text-neutral-500 dark:text-neutral-400">Konten sudah dihapus.</p>
            @endif
        </section>
    </div>

    {{-- Actions --}}
    @if ($report->status === 'open')
        <section class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-black dark:text-white mb-4">Aksi</h2>
            <div class="flex flex-wrap gap-3">
                {{-- Mark Reviewed --}}
                <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="reviewed">
                    <button class="h-10 px-5 rounded-xl text-sm font-medium shadow-sm transition"
                            style="background-color: #10b981; color: white;">
                        ✓ Tandai Reviewed
                    </button>
                </form>

                {{-- Reviewed + Delete Content --}}
                <form method="POST" action="{{ route('admin.reports.resolve', $report) }}"
                      onsubmit="return confirm('Yakin hapus konten yang dilaporkan?')">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="reviewed">
                    <input type="hidden" name="delete_content" value="1">
                    <button class="h-10 px-5 rounded-xl text-sm font-medium shadow-sm transition"
                            style="background-color: #ef4444; color: white;">
                        🗑 Reviewed + Hapus Konten
                    </button>
                </form>

                {{-- Dismiss --}}
                <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="dismissed">
                    <button class="h-10 px-5 rounded-xl text-sm font-medium border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 shadow-sm transition">
                        ✕ Dismiss
                    </button>
                </form>
            </div>
        </section>
    @endif
</x-app-layout>
