<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function values(array $keys): array
    {
        return static::query()->whereIn('key', $keys)->pluck('value', 'key')->all();
    }

    public static function put(string $key, ?string $value, string $type = 'string'): self
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type]);
    }
}
