<?php

namespace App\Support;

use App\Models\Comment;
use App\Models\Notification;
use App\Models\User;

class MentionService
{
    /**
     * Parse @mentions in comment content and create notifications.
     */
    public static function parse(Comment $comment): array
    {
        // Match @username patterns (alphanumeric, underscores, hyphens, dots)
        preg_match_all('/@([a-zA-Z0-9_.\-]+)/', $comment->content, $matches);

        if (empty($matches[1])) {
            return [];
        }

        $usernames = array_unique($matches[1]);

        $mentionedUsers = User::whereIn('name', $usernames)->get();

        $notifications = [];

        foreach ($mentionedUsers as $user) {
            // Don't notify yourself
            if ($comment->user_id && $user->id === $comment->user_id) {
                continue;
            }

            $notifications[] = Notification::create([
                'user_id'         => $user->id,
                'sender_id'       => $comment->user_id,
                'type'            => 'mention',
                'notifiable_type' => Comment::class,
                'notifiable_id'   => $comment->id,
                'message'         => ($comment->user ? $comment->user->name : 'Anonymous')
                                   . ' menyebut Anda di komentar.',
            ]);
        }

        return $notifications;
    }

    /**
     * Render @mentions in text as styled spans.
     */
    public static function renderMentions(string $text): string
    {
        return preg_replace(
            '/@([a-zA-Z0-9_.\-]+)/',
            '<span class="mention font-semibold text-black dark:text-white">@$1</span>',
            $text
        );
    }
}
