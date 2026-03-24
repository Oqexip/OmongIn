{{-- resources/views/admin/appeals/show.blade.php --}}
<x-app-layout title="Detail Banding">
    <div class="mb-6">
        <a href="{{ route('admin.appeals.index') }}"
           class="inline-flex items-center gap-1 text-sm text-neutral-500 dark:text-neutral-400 hover:text-black dark:hover:text-white transition mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" /></svg>
            Kembali ke Daftar Banding
        </a>
        <h1 class="text-3xl font-extrabold tracking-tight text-black dark:text-white">Detail Banding</h1>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Left: Appeal Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Appellant Info --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-6">
                <h2 class="text-lg font-bold text-black dark:text-white mb-4">Informasi Pengguna</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center py-2 border-b border-neutral-100 dark:border-neutral-800">
                        <span class="text-neutral-500 dark:text-neutral-400">Nama</span>
                        <span class="font-semibold text-black dark:text-white">{{ $appeal->user->name }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-neutral-100 dark:border-neutral-800">
                        <span class="text-neutral-500 dark:text-neutral-400">Email</span>
                        <span class="font-medium text-black dark:text-white">{{ $appeal->user->email }}</span>
                    </div>
                    @if ($appeal->user->ban_reason)
                        <div class="flex justify-between items-center py-2 border-b border-neutral-100 dark:border-neutral-800">
                            <span class="text-neutral-500 dark:text-neutral-400">Alasan Ban</span>
                            <span class="font-medium text-rose-500 dark:text-rose-400">{{ $appeal->user->ban_reason }}</span>
                        </div>
                    @endif
                    @if ($appeal->user->banned_until)
                        <div class="flex justify-between items-center py-2">
                            <span class="text-neutral-500 dark:text-neutral-400">Batas Ban</span>
                            <span class="font-medium text-black dark:text-white">{{ $appeal->user->banned_until->translatedFormat('d F Y, H:i') }}</span>
                        </div>
                    @else
                        <div class="flex justify-between items-center py-2">
                            <span class="text-neutral-500 dark:text-neutral-400">Batas Ban</span>
                            <span class="font-medium text-black dark:text-white">Permanen</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Appeal Reason --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-6">
                <h2 class="text-lg font-bold text-black dark:text-white mb-4">Alasan Banding</h2>
                <div class="bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl p-4 text-sm text-black dark:text-white leading-relaxed whitespace-pre-line">{{ $appeal->reason }}</div>
                <div class="mt-3 text-xs text-neutral-400">Dikirim {{ $appeal->created_at->translatedFormat('d F Y, H:i') }} ({{ $appeal->created_at->diffForHumans() }})</div>
            </div>
        </div>

        {{-- Right: Actions --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-6">
                <h2 class="text-lg font-bold text-black dark:text-white mb-4">Status</h2>
                @if ($appeal->status === 'pending')
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 uppercase tracking-wider">
                        Menunggu Keputusan
                    </span>
                @elseif ($appeal->status === 'approved')
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400 uppercase tracking-wider">
                        Disetujui
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 uppercase tracking-wider">
                        Ditolak
                    </span>
                @endif

                @if ($appeal->reviewer)
                    <div class="mt-4 text-xs text-neutral-500 dark:text-neutral-400">
                        Ditinjau oleh <span class="font-semibold text-black dark:text-white">{{ $appeal->reviewer->name }}</span>
                        <br>pada {{ $appeal->resolved_at->translatedFormat('d F Y, H:i') }}
                    </div>
                @endif

                @if ($appeal->admin_notes)
                    <div class="mt-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg p-3 text-sm text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-neutral-700">
                        <div class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-1">Catatan Admin</div>
                        {{ $appeal->admin_notes }}
                    </div>
                @endif
            </div>

            {{-- Action Buttons (only for pending) --}}
            @if ($appeal->status === 'pending')
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-6 space-y-4">
                    <h2 class="text-lg font-bold text-black dark:text-white mb-2">Ambil Keputusan</h2>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-2">Catatan Admin (opsional)</label>
                        <textarea id="admin-notes" rows="3"
                                  class="w-full text-sm rounded-xl border border-neutral-300 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-black dark:text-white px-4 py-3 focus:ring-2 focus:ring-black dark:focus:ring-white focus:border-transparent transition resize-none"
                                  placeholder="Tulis catatan untuk keputusan ini..."></textarea>
                    </div>

                    <form method="POST" action="{{ route('admin.appeals.approve', $appeal) }}"
                          onsubmit="this.querySelector('[name=admin_notes]').value = document.getElementById('admin-notes').value">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="admin_notes" value="">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 h-11 rounded-xl text-sm font-bold bg-black text-white dark:bg-white dark:text-black hover:bg-neutral-800 dark:hover:bg-neutral-200 shadow-sm transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Setujui & Unban
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.appeals.reject', $appeal) }}"
                          onsubmit="this.querySelector('[name=admin_notes]').value = document.getElementById('admin-notes').value">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="admin_notes" value="">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 h-11 rounded-xl text-sm font-bold border-2 border-rose-500 text-rose-500 dark:border-rose-400 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                            Tolak Banding
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
