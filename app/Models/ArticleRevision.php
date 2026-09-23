<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleRevision extends Model
{
    protected $fillable = ['article_id', 'editor_id', 'title', 'body', 'status', 'excerpt', 'snapshot'];
    protected function casts(): array { return ['snapshot' => 'array']; }
    public function article(): BelongsTo { return $this->belongsTo(Article::class); }
    public function editor(): BelongsTo { return $this->belongsTo(User::class, 'editor_id'); }
}