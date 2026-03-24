{{-- resources/views/notifications/index.blade.php --}}
<x-app-layout title="Notifikasi">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-black dark:text-white">Notifikasi</h1>

        @if ($notifications->count() > 0)
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button class="text-sm text-neutral-500 dark:text-neutral-400 hover:text-black dark:hover:text-white transition">
                    Tandai semua dibaca
                </button>
            </form>
        @endif
    </div>

    @if ($notifications->count() === 0)
        <div class="text-center py-16">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-neutral-300 dark:text-neutral-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="text-neutral-500 dark:text-neutral-400">Belum ada notifikasi.</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach ($notifications as $notif)
                <div class="flex items-start gap-3 p-4 rounded-2xl border transition
                    {{ $notif->read_at ? 'border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900' : 'border-neutral-300 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800' }}">

                    {{-- Icon --}}
                    <div class="flex-shrink-0 mt-0.5">
                        @if ($notif->type === 'mention')
                            <div class="h-8 w-8 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                                <span class="text-sm font-bold text-neutral-600 dark:text-neutral-300">@</span>
                            </div>
                        @elseif ($notif->type === 'moderator_invite')
                            <div class="h-8 w-8 rounded-full bg-amber-100 dark:bg-amber-900 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                        @elseif (in_array($notif->type, ['invite_accepted', 'invite_declined']))
                            <div class="h-8 w-8 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                                @if ($notif->type === 'invite_accepted')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                @endif
                            </div>
                        @else
                            <div class="h-8 w-8 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-neutral-800 dark:text-neutral-200">
                            @if ($notif->sender)
                                <span class="font-semibold">{{ $notif->sender->name }}</span>
                            @else
                                <span class="font-semibold">Seseorang</span>
                            @endif
                            {{ $notif->message ?? 'mengirim notifikasi.' }}
                        </p>

                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            <time class="text-xs text-neutral-400 dark:text-neutral-500">
                                {{ $notif->created_at->diffForHumans() }}
                            </time>

                            @if ($notif->notifiable_type === 'App\\Models\\Comment' && $notif->notifiable)
                                <a href="{{ route('threads.show', $notif->notifiable->thread_id) }}"
                                   class="text-xs text-neutral-500 dark:text-neutral-400 hover:text-black dark:hover:text-white transition">
                                    Lihat thread →
                                </a>
                            @endif
                        </div>

                        {{-- Moderator invite: Accept / Decline buttons --}}
                        @if ($notif->type === 'moderator_invite' && $notif->notifiable)
                            @php $invitation = $notif->notifiable; @endphp
                            @if ($invitation->isPending())
                                <div class="flex items-center gap-2 mt-3">
                                    <form method="POST" action="{{ route('moderator.invitation.accept', $invitation) }}">
                                        @csrf
                                        <button class="h-8 px-4 rounded-lg text-sm font-medium text-white bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 shadow-sm transition">
                                            ✓ Terima
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('moderator.invitation.decline', $invitation) }}">
                                        @csrf
                                        <button class="h-8 px-4 rounded-lg text-sm font-medium text-neutral-700 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                            ✕ Tolak
                                        </button>
                                    </form>
                                </div>
                            @elseif ($invitation->isAccepted())
                                <div class="mt-2">
                                    <span class="inline-flex items-center text-xs font-semibold px-2 py-1 rounded-md"
                                          style="color: #10b981; border: 1px solid #10b981; background-color: transparent;">
                                        ✓ Diterima
                                    </span>
                                </div>
                            @elseif ($invitation->isDeclined())
                                <div class="mt-2">
                                    <span class="inline-flex items-center text-xs font-semibold px-2 py-1 rounded-md"
                                          style="color: #ef4444; border: 1px solid #ef4444; background-color: transparent;">
                                        ✕ Ditolak
                                    </span>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Mark read button (only for non-invitation notifications or already responded) --}}
                    @if (!$notif->read_at && $notif->type !== 'moderator_invite')
                        <form method="POST" action="{{ route('notifications.read', $notif) }}" class="flex-shrink-0">
                            @csrf @method('PATCH')
                            <button class="h-8 w-8 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800 flex items-center justify-center transition"
                                    title="Tandai dibaca">
                                <div class="h-2 w-2 rounded-full bg-black dark:bg-white"></div>
                            </button>
                        </form>
                    @elseif (!$notif->read_at)
                        <div class="flex-shrink-0 mt-1">
                            <div class="h-2 w-2 rounded-full bg-amber-500" title="Menunggu respons"></div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</x-app-layout>

