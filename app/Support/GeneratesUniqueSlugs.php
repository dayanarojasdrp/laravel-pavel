<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GeneratesUniqueSlugs
{
    public static function make(string $modelClass, string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value) ?: 'item';
        $slug = $baseSlug;
        $counter = 2;

        while (self::slugExists($modelClass, $slug, $ignoreId)) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private static function slugExists(string $modelClass, string $slug, ?int $ignoreId): bool
    {
        /** @var Model $model */
        $model = new $modelClass();

        return $modelClass::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }
}
