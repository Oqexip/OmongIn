{{-- resources/views/admin/banned.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Diblokir — OmongIn</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-50 dark:bg-neutral-950 min-h-screen flex items-center justify-center p-4 font-sans text-neutral-900 dark:text-neutral-100">
    
    <div class="max-w-2xl w-full">
        {{-- Header Section --}}
        <div class="flex flex-col items-center text-center mb-8">
            <div class="w-16 h-16 flex items-center justify-center rounded-full border border-neutral-800 dark:border-neutral-200 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>

            <h1 class="text-4xl font-extrabold tracking-tight mb-4">Akses Ditolak</h1>
            <p class="text-lg text-neutral-600 dark:text-neutral-400">
                Akun Anda saat ini <span class="font-bold text-neutral-900 dark:text-white">ditangguhkan</span>. Anda tidak dapat mengakses fitur OmongIn<br class="hidden sm:block">
                hingga masa penalti berakhir.
            </p>
        </div>

        @php
            $bannedInfo = session('banned_info');
            $userId = $bannedInfo['user_id'] ?? null;
            $hasPendingAppeal = false;
            if ($userId) {
                $hasPendingAppeal = \App\Models\BanAppeal::where('user_id', $userId)->where('status', 'pending')->exists();
            }
        @endphp

        {{-- Ban Details Card --}}
        <div class="border border-neutral-800 dark:border-neutral-200 rounded-[1.5rem] p-6 mb-6 bg-white dark:bg-neutral-900">
            <div class="flex items-center gap-2 mb-6 text-neutral-500 dark:text-neutral-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-sm font-bold tracking-widest uppercase">Informasi Penalti</h3>
            </div>
            
            <div class="space-y-6">
                <div>
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-neutral-800 dark:border-neutral-200">
                        <span class="font-medium text-neutral-700 dark:text-neutral-300">Status</span>
                        <span class="inline-flex items-center px-4 py-1 rounded-full text-sm font-bold border border-neutral-800 dark:border-neutral-200">
                            BANNED
                        </span>
                    </div>
                </div>

                @if ($bannedInfo)
                    <div>
                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-neutral-800 dark:border-neutral-200">
                            <span class="font-medium text-neutral-700 dark:text-neutral-300">Berakhir Pada</span>
                            <span class="font-semibold text-neutral-900 dark:text-white">
                                @if ($bannedInfo['until'])
                                    {{ \Carbon\Carbon::parse($bannedInfo['until'])->translatedFormat('d F Y, H:i') }}
                                @else
                                    Permanen
                                @endif
                            </span>
                        </div>
                    </div>

                    @if ($bannedInfo['reason'])
                        <div>
                            <span class="block font-medium text-neutral-700 dark:text-neutral-300 mb-3">Alasan Pemblokiran</span>
                            <div class="p-4 rounded-xl border border-neutral-800 dark:border-neutral-200 text-neutral-900 dark:text-white">
                                {!! nl2br(e($bannedInfo['reason'])) !!}
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl border border-neutral-800 dark:border-neutral-200 font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl border border-neutral-800 dark:border-neutral-200 font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- Appeal Section --}}
        @if ($userId)
            @if ($hasPendingAppeal)
                <div class="mb-8 p-6 rounded-[1.5rem] border border-neutral-800 dark:border-neutral-200 bg-white dark:bg-neutral-900 flex items-start gap-4">
                    <div class="mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-1">Banding Sedang Ditinjau</h4>
                        <p class="text-neutral-700 dark:text-neutral-300">
                            Permohonan banding Anda telah kami terima dan sedang dalam proses verifikasi oleh tim administrator.
                        </p>
                    </div>
                </div>
            @else
                <div class="mb-8 p-6 rounded-[1.5rem] border border-neutral-800 dark:border-neutral-200 bg-white dark:bg-neutral-900">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-1">Ajukan Banding</h4>
                            <p class="text-neutral-700 dark:text-neutral-300 mb-5">
                                Jika Anda merasa pemblokiran ini tidak adil, Anda dapat mengajukan banding. Jelaskan alasan Anda secara jelas dan sopan.
                            </p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('ban-appeal.store') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $userId }}">
                        
                        <textarea name="reason" rows="4" required maxlength="2000"
                                  class="w-full rounded-xl border border-neutral-800 dark:border-neutral-200 bg-transparent px-4 py-3 focus:ring-0 focus:outline-none focus:border-neutral-900 dark:focus:border-white resize-none mb-3"
                                  placeholder="Jelaskan mengapa Anda merasa pemblokiran ini perlu ditinjau ulang..."></textarea>
                        
                        @error('reason')
                            <p class="font-bold text-red-500 mb-3">{{ $message }}</p>
                        @enderror

                        <button type="submit"
                                class="w-full flex items-center justify-center h-12 rounded-xl font-bold bg-neutral-900 text-white dark:bg-white dark:text-neutral-900 hover:opacity-90 transition-opacity">
                            Kirim Banding
                        </button>
                    </form>
                </div>
            @endif
        @endif

        {{-- Footer Links --}}
        <div class="pt-6 border-t border-neutral-800 dark:border-neutral-200 text-center flex justify-center mt-4">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 font-bold text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
