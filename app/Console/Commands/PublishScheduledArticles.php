<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PublishScheduledArticles extends Command
{
    protected $signature = 'articles:publish-scheduled';
    protected $description = 'Publish scheduled articles whose publication time has arrived';

    public function handle(): int
    {
        $now = now();
        $eligible = 0;
        $published = 0;
        $failed = 0;

        Article::query()->where('status', 'scheduled')->whereNotNull('published_at')->where('published_at', '<=', $now)->orderBy('id')->chunkById(100, function ($articles) use (&$eligible, &$published, &$failed): void {
            foreach ($articles as $article) {
                $eligible++;
                try {
                    $changed = DB::table('articles')->where('id', $article->id)->where('status', 'scheduled')->update(['status' => 'published', 'updated_at' => now()]);
                    if ($changed !== 1) continue;
                    $published++;
                    AuditLog::record('article.auto_published', $article, ['previous_status' => 'scheduled', 'status' => 'published']);
                } catch (\Throwable $exception) {
                    $failed++;
                    report($exception);
                }
            }
        });

        $this->info("Eligible: {$eligible}; published: {$published}; failed: {$failed}.");
        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
