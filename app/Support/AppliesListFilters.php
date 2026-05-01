<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AppliesListFilters
{
    public static function search(Builder $query, Request $request, array $columns): Builder
    {
        $search = trim((string) $request->query('search', $request->query('q', '')));

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($columns, $search) {
            foreach ($columns as $column) {
                $query->orWhere($column, 'like', "%{$search}%");
            }
        });
    }

    public static function exact(Builder $query, Request $request, string $field, ?string $column = null): Builder
    {
        if (! $request->filled($field)) {
            return $query;
        }

        return $query->where($column ?? $field, $request->query($field));
    }

    public static function boolean(Builder $query, Request $request, string $field, ?string $column = null): Builder
    {
        if (! $request->has($field)) {
            return $query;
        }

        $value = filter_var($request->query($field), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($value === null) {
            return $query;
        }

        return $query->where($column ?? $field, $value);
    }
}
