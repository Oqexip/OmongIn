<?php

namespace App\Support;

use Illuminate\Support\Str;

class Sanitize
{
    public static function toHtml(string $input): string
    {
        $html = Str::markdown($input, [
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $html = strip_tags($html, '<p><a><strong><em><ul><ol><li><code><pre><blockquote><br><hr><span>');

        // Parse ||spoiler text|| syntax
        $html = preg_replace('/\|\|(.*?)\|\|/s', '<span class="spoiler">$1</span>', $html);

        return $html;
    }
}
