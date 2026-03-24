{{-- resources/views/admin/boards/form.blade.php --}}
<x-app-layout title="{{ $board ? 'Edit' : 'Buat' }} Board — Admin">
    <div class="mb-6">
        <a href="{{ route('admin.boards.index') }}" class="text-sm text-neutral-500 dark:text-neutral-400 hover:text-black dark:hover:text-white transition">← Boards</a>
        <h1 class="text-2xl font-bold tracking-tight text-black dark:text-white mt-1">
            {{ $board ? "Edit Board: /{$board->slug}" : 'Buat Board Baru' }}
        </h1>
    </div>

    <section class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-6 max-w-xl">
        <form method="POST"
              action="{{ $board ? route('admin.boards.update', $board) : route('admin.boards.store') }}"
              class="space-y-4">
            @csrf
            @if ($board) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $board?->slug) }}" required placeholder="contoh: random"
                       class="w-full h-10 rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800
                              text-neutral-900 dark:text-neutral-100 text-sm px-3
                              focus:border-neutral-500 dark:focus:border-neutral-400">
                <p class="text-xs text-neutral-400 mt-1">Huruf kecil, angka, garis bawah, strip. Tanpa spasi.</p>
                @error('slug') <p class="text-xs mt-1" style="color: #ef4444;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', $board?->name) }}" required placeholder="contoh: Random Discussion"
                       class="w-full h-10 rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800
                              text-neutral-900 dark:text-neutral-100 text-sm px-3
                              focus:border-neutral-500 dark:focus:border-neutral-400">
                @error('name') <p class="text-xs mt-1" style="color: #ef4444;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Deskripsi singkat board ini (opsional)"
                          class="w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800
                                 text-neutral-900 dark:text-neutral-100 text-sm px-3 py-2
                                 focus:border-neutral-500 dark:focus:border-neutral-400">{{ old('description', $board?->description) }}</textarea>
                @error('description') <p class="text-xs mt-1" style="color: #ef4444;">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_nsfw" value="1" id="is_nsfw"
                       {{ old('is_nsfw', $board?->is_nsfw) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-neutral-300 dark:border-neutral-600">
                <label for="is_nsfw" class="text-sm text-neutral-700 dark:text-neutral-300">Board ini NSFW</label>
            </div>

            <button type="submit"
                    class="h-10 px-5 rounded-xl text-white text-sm font-medium shadow-sm bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 transition">
                {{ $board ? 'Simpan Perubahan' : 'Buat Board' }}
            </button>
        </form>
    </section>
</x-app-layout>
