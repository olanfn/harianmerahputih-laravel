<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ScheduledPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_due_scheduled_article_is_published_and_audited_idempotently(): void
    {
        Carbon::setTestNow('2026-09-23 10:00:00');
        $article = Article::factory()->create(['status' => 'scheduled', 'published_at' => now()->subMinute(), 'is_demo' => false]);
        $this->artisan('articles:publish-scheduled')->assertSuccessful();
        $this->assertSame('published', $article->fresh()->status);
        $this->assertSame(1, AuditLog::where('action', 'article.auto_published')->count());
        $this->artisan('articles:publish-scheduled')->assertSuccessful();
        $this->assertSame(1, AuditLog::where('action', 'article.auto_published')->count());
    }

    public function test_future_or_invalid_status_articles_are_not_published(): void
    {
        Carbon::setTestNow('2026-09-23 10:00:00');
        $future = Article::factory()->create(['status' => 'scheduled', 'published_at' => now()->addMinute()]);
        $missing = Article::factory()->create(['status' => 'scheduled', 'published_at' => null]);
        $draft = Article::factory()->create(['status' => 'draft', 'published_at' => now()->subMinute()]);
        $review = Article::factory()->create(['status' => 'review', 'published_at' => now()->subMinute()]);
        $archived = Article::factory()->create(['status' => 'archived', 'published_at' => now()->subMinute()]);
        $this->artisan('articles:publish-scheduled')->assertSuccessful();
        $this->assertSame('scheduled', $future->fresh()->status);
        $this->assertSame('scheduled', $missing->fresh()->status);
        $this->assertSame('draft', $draft->fresh()->status);
        $this->assertSame('review', $review->fresh()->status);
        $this->assertSame('archived', $archived->fresh()->status);
    }
}
