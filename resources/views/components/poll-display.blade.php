{{-- resources/views/components/poll-display.blade.php --}}
{{-- Renders the interactive poll widget inside a thread show page --}}

@php
    use Illuminate\Support\Facades\Auth;

    $userId  = Auth::id();
    $anonKey = (string) (request()->attributes->get('anon_id') ?? session('anon_id'));
    $hasVoted    = $poll->hasVoted($userId, $anonKey);
    $votedId     = $poll->votedOptionId($userId, $anonKey);
    $totalVotes  = $poll->totalVotes();
    $isOpen      = $poll->isAcceptingVotes();
    $isExpired   = $poll->isExpired();
    $isClosed    = $poll->is_closed;
@endphp

<div class="mt-6 rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/60 p-5 space-y-4">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-2">
            {{-- Poll icon --}}
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-neutral-200 dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 3h4v18H3V3zm7 6h4v12h-4V9zm7-4h4v16h-4V5z"/>
                </svg>
            </span>
            <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 text-sm leading-snug">
                {{ $poll->question }}
            </h3>
        </div>

        {{-- Status badge --}}
        @if (!$isOpen)
            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                {{ $isClosed ? 'bg-neutral-200 dark:bg-neutral-700 text-neutral-600 dark:text-neutral-400' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' }}">
                {{ $isClosed ? 'Ditutup' : 'Berakhir' }}
            </span>
        @elseif($poll->expires_at)
            <span class="shrink-0 text-[11px] text-neutral-400 dark:text-neutral-500 whitespace-nowrap">
                Berakhir {{ $poll->expires_at->diffForHumans() }}
            </span>
        @endif
    </div>

    {{-- Options --}}
    @if ($hasVoted || ! $isOpen)
        {{-- Results view --}}
        <div class="space-y-2">
            @foreach ($poll->options as $option)
                @php
                    $pct = $totalVotes > 0 ? round(($option->votes_count / $totalVotes) * 100) : 0;
                    $isMyVote = $votedId === $option->id;
                @endphp
                <div class="relative">
                    {{-- Background bar --}}
                    <div class="absolute inset-0 rounded-lg overflow-hidden">
                        <div class="h-full rounded-lg transition-all duration-700 ease-out {{ $isMyVote ? 'bg-black dark:bg-white' : 'bg-neutral-200 dark:bg-neutral-700' }}"
                             style="width: {{ $pct }}%; opacity: {{ $isMyVote ? '0.12' : '0.3' }};"></div>
                    </div>

                    <div class="relative flex items-center justify-between px-3 py-2.5 rounded-lg border {{ $isMyVote ? 'border-black dark:border-white' : 'border-neutral-200 dark:border-neutral-700' }}">
                        <div class="flex items-center gap-2 min-w-0">
                            @if ($isMyVote)
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-black dark:text-white shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2a10 10 0 100 20A10 10 0 0012 2zm-1 15l-4-4 1.41-1.41L11 13.17l6.59-6.59L19 8l-8 9z"/>
                                </svg>
                            @endif
                            <span class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate">{{ $option->label }}</span>
                        </div>
                        <div class="shrink-0 flex items-center gap-2 ml-3">
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $option->votes_count }}</span>
                            <span class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 w-9 text-right">{{ $pct }}%</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer info --}}
        <p class="text-xs text-neutral-400 dark:text-neutral-600">
            {{ $totalVotes }} {{ Str::plural('suara', $totalVotes) }}
            @if ($hasVoted) • Kamu sudah memilih @endif
        </p>

    @else
        {{-- Voting form --}}
        <form method="POST" action="{{ route('polls.vote', $poll) }}">
            @csrf
            <div class="space-y-2">
                @foreach ($poll->options as $option)
                    <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 cursor-pointer hover:border-neutral-400 dark:hover:border-neutral-500 hover:bg-neutral-50 dark:hover:bg-neutral-700/60 transition group has-[:checked]:border-black dark:has-[:checked]:border-white has-[:checked]:bg-black/5 dark:has-[:checked]:bg-white/5">
                        <input type="radio" name="poll_option_id" value="{{ $option->id }}" required
                               class="accent-black dark:accent-white shrink-0">
                        <span class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate">{{ $option->label }}</span>
                    </label>
                @endforeach
            </div>

            <div class="mt-3 flex items-center justify-between">
                <span class="text-xs text-neutral-400 dark:text-neutral-600">
                    {{ $totalVotes }} {{ Str::plural('suara', $totalVotes) }}
                </span>
                <button type="submit"
                        class="h-9 px-4 rounded-xl bg-black text-white text-sm font-medium hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 transition shadow-sm">
                    Vote
                </button>
            </div>
        </form>
    @endif
</div>
