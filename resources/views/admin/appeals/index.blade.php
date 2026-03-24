{{-- resources/views/admin/appeals/index.blade.php --}}
<x-app-layout title="Kelola Banding">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-black dark:text-white">Kelola Banding</h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-2">Tinjau dan tanggapi pengajuan banding dari pengguna yang diblokir.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Status Tabs --}}
    <div class="flex items-center gap-2 mb-6 border-b border-neutral-200 dark:border-neutral-800 pb-3">
        @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $key => $label)
            <a href="{{ route('admin.appeals.index', ['status' => $key]) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition
                      {{ $status === $key
                         ? 'bg-black text-white dark:bg-white dark:text-black'
                         : 'text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800' }}">
                {{ $label }}
                <span class="ml-1 text-xs font-bold opacity-70">({{ $counts[$key] }})</span>
            </a>
        @endforeach
    </div>

    {{-- Appeals Table --}}
    @if ($appeals->count() > 0)
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 dark:text-neutral-400 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Pengguna</th>
                        <th class="px-5 py-3 font-semibold">Alasan Banding</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Tanggal</th>
                        <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach ($appeals as $appeal)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/30 transition">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-black dark:text-white">{{ $appeal->user->name }}</div>
                                <div class="text-xs text-neutral-400">{{ $appeal->user->email }}</div>
                            </td>
                            <td class="px-5 py-4 text-neutral-700 dark:text-neutral-300 max-w-xs truncate">
                                {{ Str::limit($appeal->reason, 80) }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($appeal->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 uppercase tracking-wider">
                                        Menunggu
                                    </span>
                                @elseif ($appeal->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400 uppercase tracking-wider">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 uppercase tracking-wider">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs text-neutral-400">
                                {{ $appeal->created_at->diffForHumans() }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.appeals.show', $appeal) }}"
                                   class="text-sm font-medium text-black dark:text-white hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $appeals->appends(['status' => $status])->links() }}</div>
    @else
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-neutral-300 dark:text-neutral-700 mb-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <p class="text-neutral-500 dark:text-neutral-400 font-medium">Tidak ada banding dengan status ini.</p>
        </div>
    @endif
</x-app-layout>
