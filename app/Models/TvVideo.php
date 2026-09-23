<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TvVideo extends Model
{
    protected $table = 'tv_videos';
    protected $fillable = ['title', 'slug', 'excerpt', 'body', 'status', 'published_at', 'media_id', 'sort_order', 'author_id'];
    protected function casts(): array { return ['published_at' => 'datetime']; }
    public function media(): BelongsTo { return $this->belongsTo(Media::class); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
    public function scopePublished(Builder $query): Builder { return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now()); }
}