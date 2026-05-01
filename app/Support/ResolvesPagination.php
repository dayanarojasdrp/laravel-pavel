<?php

namespace App\Support;

use Illuminate\Http\Request;

class ResolvesPagination
{
    public static function perPage(Request $request, int $default = 10, int $max = 50): int
    {
        $perPage = (int) $request->query('per_page', $default);

        if ($perPage < 1) {
            return $default;
        }

        return min($perPage, $max);
    }
}
