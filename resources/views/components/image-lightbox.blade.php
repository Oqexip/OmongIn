{{-- resources/views/components/image-lightbox.blade.php --}}
{{-- Global lightbox overlay — triggered via 'open-lightbox' custom event --}}
<div
    x-data="{
        open: false,
        images: [],
        index: 0,
        get current() { return this.images[this.index] ?? null; },
        get hasPrev()  { return this.index > 0; },
        get hasNext()  { return this.index < this.images.length - 1; },
        prev()  { if (this.hasPrev) this.index--; },
        next()  { if (this.hasNext) this.index++; },
        close() { this.open = false; document.body.style.overflow = ''; },
        show(imgs, idx) {
            this.images = imgs;
            this.index  = idx ?? 0;
            this.open   = true;
            document.body.style.overflow = 'hidden';
        }
    }"
    @open-lightbox.window="show($event.detail.images, $event.detail.index)"
    @keydown.escape.window="if(open) close()"
    @keydown.arrow-left.window="if(open) prev()"
    @keydown.arrow-right.window="if(open) next()"
    x-show="open"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center"
    style="z-index: 99999;"
    @click.self="close()"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/90" aria-hidden="true"></div>

    {{-- Close button --}}
    <button
        type="button"
        @click.stop="close()"
        class="fixed top-4 right-4 flex items-center justify-center w-11 h-11 rounded-full bg-white/15 hover:bg-white/30 text-white transition focus:outline-none"
        style="z-index: 100001;"
        aria-label="Tutup"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    {{-- Image counter --}}
    <div
        x-show="images.length > 1"
        class="fixed top-4 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-white/15 text-white text-sm font-medium"
        style="z-index: 100001;"
    >
        <span x-text="index + 1"></span> / <span x-text="images.length"></span>
    </div>

    {{-- Prev button --}}
    <button
        type="button"
        x-show="hasPrev"
        @click.stop="prev()"
        class="fixed left-3 sm:left-6 top-1/2 -translate-y-1/2 flex items-center justify-center w-11 h-11 rounded-full bg-white/15 hover:bg-white/30 text-white transition focus:outline-none"
        style="z-index: 100001;"
        aria-label="Sebelumnya"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </button>

    {{-- Image --}}
    <img
        x-show="current"
        :src="current"
        :key="current"
        alt="Gambar"
        @click.stop
        class="relative max-w-[90vw] max-h-[85vh] object-contain rounded-lg shadow-2xl select-none"
        style="z-index: 100000;"
        draggable="false"
    >

    {{-- Next button --}}
    <button
        type="button"
        x-show="hasNext"
        @click.stop="next()"
        class="fixed right-3 sm:right-6 top-1/2 -translate-y-1/2 flex items-center justify-center w-11 h-11 rounded-full bg-white/15 hover:bg-white/30 text-white transition focus:outline-none"
        style="z-index: 100001;"
        aria-label="Berikutnya"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    </button>
</div>
