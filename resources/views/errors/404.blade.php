<x-app-layout title="404 Not Found">
    <div class="flex flex-col items-center justify-center min-vh-minus-header-footer py-24 px-4 text-center">
        <h1 class="text-9xl font-black tracking-tight text-black dark:text-white mb-4">404</h1>
        <h2 class="text-2xl md:text-3xl font-bold text-neutral-800 dark:text-neutral-200 mb-4">Oops! Halaman tidak ditemukan</h2>
        <p class="text-neutral-500 dark:text-neutral-400 max-w-md mb-8">
            Maaf, halaman yang Anda cari mungkin telah dihapus, namanya diganti, atau untuk sementara tidak tersedia.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="javascript:history.back()" 
               class="inline-flex items-center justify-center h-11 px-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 font-medium transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <a href="{{ url('/') }}" 
               class="inline-flex items-center justify-center h-11 px-6 rounded-xl text-white bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 font-medium transition shadow-sm">
                Ke Beranda
            </a>
        </div>
    </div>
</x-app-layout>
