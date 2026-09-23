<?php

namespace App\Support;

use App\Models\Article;
use Illuminate\Support\Collection;

class ArticleContentRenderer
{
    public function segments(Article $article): array
    {
        $inline = $article->mediaLinks
            ->where('role', 'inline')
            ->sortBy('sort_order')
            ->keyBy('media_id');
        $parts = preg_split('/(\[\[media:\d+\]\])/', (string) $article->body, -1, PREG_SPLIT_DELIM_CAPTURE);
        $segments = [];

        foreach ($parts as $part) {
            if (preg_match('/^\[\[media:(\d+)\]\]$/', $part, $matches)) {
                $link = $inline->get((int) $matches[1]);
                if ($link && $link->media) {
                    $segments[] = ['type' => 'media', 'media' => $link->media, 'caption' => $link->caption_override ?: ($link->media->caption ?: 'Visual artikel Harian Merah Putih.')];
                }
                continue;
            }

            if ($part !== '') {
                $segments[] = ['type' => 'text', 'text' => $part];
            }
        }

        return $segments;
    }
}
