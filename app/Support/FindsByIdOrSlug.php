<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FindsByIdOrSlug
{
    public static function firstOrFail(Builder $query, string|int $identifier): Model
    {
        $identifier = (string) $identifier;

        return $query
            ->where(function (Builder $query) use ($identifier) {
                if (ctype_digit($identifier)) {
                    $query->whereKey($identifier)
                        ->orWhere('slug', $identifier);

                    return;
                }

                $query->where('slug', $identifier);
            })
            ->firstOrFail();
    }
}
