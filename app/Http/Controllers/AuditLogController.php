<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Support\AppliesListFilters;
use App\Support\ResolvesPagination;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user:id,name,email,role');

        AppliesListFilters::search($query, $request, ['auditable_type', 'auditable_label', 'action']);
        AppliesListFilters::exact($query, $request, 'action');
        AppliesListFilters::exact($query, $request, 'auditable_type');
        AppliesListFilters::exact($query, $request, 'auditable_id');
        AppliesListFilters::exact($query, $request, 'user_id');

        return $query
            ->orderByDesc('created_at')
            ->paginate(ResolvesPagination::perPage($request));
    }

    public function show($id)
    {
        return AuditLog::with('user:id,name,email,role')->findOrFail($id);
    }
}
