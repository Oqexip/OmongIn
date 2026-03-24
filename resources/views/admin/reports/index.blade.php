{{-- resources/views/admin/reports/index.blade.php --}}
<x-app-layout title="Laporan — Admin">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-neutral-500 dark:text-neutral-400 hover:text-black dark:hover:text-white transition">← Admin</a>
            <h1 class="text-2xl font-bold tracking-tight text-black dark:text-white mt-1">Kelola Laporan</h1>
        </div>
    </div>

    {{-- Status Tabs --}}
    <div class="flex gap-2 mb-6">
        @foreach (['open' => 'Open', 'reviewed' => 'Reviewed', 'dismissed' => 'Dismissed', 'all' => 'Semua'] as $key => $label)
            <a href="{{ route('admin.reports.index', ['status' => $key]) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-medium transition
                      {{ $status === $key
                          ? 'bg-black text-white dark:bg-white dark:text-black'
                          : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-200 dark:hover:bg-neutral-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($reports->count() === 0)
        <div class="text-center py-16">
            <p class="text-neutral-500 dark:text-neutral-400">Tidak ada laporan{{ $status !== 'all' ? " berstatus {$status}" : '' }}.</p>
        </div>
    @else
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-800 text-left text-xs text-neutral-500 dark:text-neutral-400 uppercase">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Konten</th>
                        <th class="px-4 py-3">Alasan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach ($reports as $report)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                            <td class="px-4 py-3 font-mono text-neutral-500 dark:text-neutral-400">#{{ $report->id }}</td>
                            <td class="px-4 py-3 text-black dark:text-white">
                                {{ class_basename($report->reportable_type) }} #{{ $report->reportable_id }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium"
                                      style="color: {{ $report->reason === 'spam' ? '#f59e0b' : ($report->reason === 'abuse' ? '#ef4444' : ($report->reason === 'nsfw' ? '#a855f7' : '#6b7280')) }};
                                             border: 1px solid currentColor;">
                                    {{ strtoupper($report->reason) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = ['open' => '#f59e0b', 'reviewed' => '#10b981', 'dismissed' => '#6b7280'];
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold"
                                      style="color: {{ $statusColors[$report->status] ?? '#6b7280' }}; border: 1px solid currentColor;">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-neutral-500 dark:text-neutral-400">{{ $report->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.reports.show', $report) }}"
                                   class="text-xs text-neutral-500 dark:text-neutral-400 hover:text-black dark:hover:text-white transition">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $reports->links() }}</div>
    @endif
</x-app-layout>
