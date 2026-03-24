{{-- resources/views/admin/users/index.blade.php --}}
<x-app-layout title="Users — Admin">
    <div class="mb-6">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-neutral-500 dark:text-neutral-400 hover:text-black dark:hover:text-white transition">← Admin</a>
        <h1 class="text-2xl font-bold tracking-tight text-black dark:text-white mt-1">Kelola Users</h1>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
               class="flex-1 min-w-[200px] h-10 rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800
                      text-neutral-900 dark:text-neutral-100 text-sm px-3
                      focus:border-neutral-500 dark:focus:border-neutral-400 focus:ring-neutral-200 dark:focus:ring-neutral-700">
        <select name="filter"
                class="h-10 rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800
                       text-neutral-900 dark:text-neutral-100 text-sm px-3">
            <option value="">Semua</option>
            <option value="banned" {{ request('filter') === 'banned' ? 'selected' : '' }}>Banned</option>
        </select>
        <button class="h-10 px-5 rounded-xl text-white text-sm font-medium shadow-sm bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 transition">
            Cari
        </button>
    </form>

    @if ($users->count() === 0)
        <div class="text-center py-16">
            <p class="text-neutral-500 dark:text-neutral-400">Tidak ada user ditemukan.</p>
        </div>
    @else
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-800 text-left text-xs text-neutral-500 dark:text-neutral-400 uppercase">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Daftar</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach ($users as $user)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                            <td class="px-4 py-3 font-medium text-black dark:text-white">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium"
                                      style="color: {{ $user->role === 'admin' ? '#8b5cf6' : '#6b7280' }}; border: 1px solid currentColor;">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($user->isBanned())
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold" style="color: #ef4444; border: 1px solid #ef4444;">
                                        Banned
                                        @if ($user->banned_until)
                                            <span class="font-normal">sampai {{ $user->banned_until->format('d/m/Y') }}</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-xs" style="color: #10b981;">Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-neutral-500 dark:text-neutral-400">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                @if (!$user->isAdmin())
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($user->isBanned())
                                            <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                                                @csrf
                                                <button class="px-3 h-7 rounded-lg text-xs font-medium transition" style="color: #10b981; border: 1px solid #10b981;">
                                                    Unban
                                                </button>
                                            </form>
                                        @else
                                            {{-- Ban button triggers inline form --}}
                                            <button onclick="document.getElementById('ban-form-{{ $user->id }}').classList.toggle('hidden')"
                                                    class="px-3 h-7 rounded-lg text-xs font-medium transition" style="color: #ef4444; border: 1px solid #ef4444;">
                                                Ban
                                            </button>
                                        @endif
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Hapus akun {{ $user->name }}? Ini tidak bisa dibatalkan.')">
                                            @csrf @method('DELETE')
                                            <button class="px-3 h-7 rounded-lg text-xs font-medium text-neutral-500 dark:text-neutral-400 hover:text-black dark:hover:text-white border border-neutral-300 dark:border-neutral-600 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-neutral-400">—</span>
                                @endif
                            </td>
                        </tr>
                        {{-- Ban inline form (hidden by default) --}}
                        @if (!$user->isAdmin() && !$user->isBanned())
                            <tr id="ban-form-{{ $user->id }}" class="hidden bg-neutral-50 dark:bg-neutral-800">
                                <td colspan="6" class="px-4 py-4">
                                    <form method="POST" action="{{ route('admin.users.ban', $user) }}" class="flex flex-wrap items-end gap-3">
                                        @csrf
                                        <div>
                                            <label class="block text-xs text-neutral-500 dark:text-neutral-400 mb-1">Durasi</label>
                                            <select name="duration" required
                                                    class="h-9 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm px-2 text-neutral-900 dark:text-neutral-100">
                                                <option value="1">1 hari</option>
                                                <option value="7">7 hari</option>
                                                <option value="30">30 hari</option>
                                                <option value="permanent">Permanen</option>
                                            </select>
                                        </div>
                                        <div class="flex-1 min-w-[200px]">
                                            <label class="block text-xs text-neutral-500 dark:text-neutral-400 mb-1">Alasan</label>
                                            <input type="text" name="ban_reason" required placeholder="Alasan ban..."
                                                   class="w-full h-9 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm px-3 text-neutral-900 dark:text-neutral-100">
                                        </div>
                                        <button type="submit" class="h-9 px-4 rounded-lg text-xs font-medium text-white shadow-sm transition" style="background-color: #ef4444;">
                                            Konfirmasi Ban
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $users->links() }}</div>
    @endif
</x-app-layout>
