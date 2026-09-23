<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'disk', 'path', 'original_name', 'mime_type', 'size', 'width', 'height',
        'alt_text', 'caption', 'uploaded_by', 'is_temporary',
    ];

    protected function casts(): array
    {
        return ['is_temporary' => 'boolean'];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function articleLinks(): HasMany
    {
        return $this->hasMany(ArticleMedia::class);
    }

    public function url(): string
    {
        if ($this->disk === 'public') {
            return '/storage/'.ltrim(str_replace('\\', '/', $this->path), '/');
        }

        return Storage::disk($this->disk)->url($this->path);
    }

    public function absoluteUrl(): string
    {
        return url($this->url());
    }
}
