<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ArticleMedia extends Model { protected $fillable=['article_id','media_id','role','sort_order','caption_override']; public function article(): BelongsTo { return $this->belongsTo(Article::class); } public function media(): BelongsTo { return $this->belongsTo(Media::class); } }