<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'author_id', 'title', 'slug', 'excerpt', 'body', 'status',
        'published_at', 'view_count', 'is_editor_pick', 'seo_title', 'seo_description', 'is_demo',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'view_count' => 'integer', 'is_editor_pick' => 'boolean', 'is_demo' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function mediaLinks(): HasMany
    {
        return $this->hasMany(ArticleMedia::class)->orderBy('sort_order');
    }

    public function featuredMedia(): HasOne
    {
        return $this->hasOne(ArticleMedia::class)->where('role', 'featured');
    }

    public function redirects(): HasMany
    {
        return $this->hasMany(ArticleRedirect::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(ArticleRevision::class)->latest();
    }

    protected static function booted(): void
    {
        static::updating(function (Article $article): void {
            if ($article->isDirty('slug') && $article->getOriginal('slug')) {
                ArticleRedirect::query()->updateOrCreate(
                    ['old_slug' => $article->getOriginal('slug')],
                    ['article_id' => $article->getKey()],
                );
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        return parent::resolveRouteBindingQuery($query, $value, $field)->published();
    }
}
