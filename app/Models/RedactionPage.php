<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RedactionPage extends Model
{
    protected $fillable = ['slug', 'title', 'summary', 'sort_order', 'content', 'updated_by'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function publicRouteName(): string
    {
        return match ($this->slug) {
            'redaksi' => 'redaction.show',
            'tentang-kami' => 'institutional.about',
            'pedoman-media-siber' => 'institutional.cyber-media-guidelines',
            'kebijakan-privasi' => 'institutional.privacy',
            'hubungi-redaksi' => 'institutional.contact',
            default => 'redaction.show',
        };
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
