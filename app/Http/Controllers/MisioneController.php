<?php

namespace App\Http\Controllers;

use App\Models\Misione;
use App\Support\AppliesListFilters;
use App\Support\FindsByIdOrSlug;
use App\Support\GeneratesUniqueSlugs;
use App\Support\ResolvesPagination;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MisioneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Misione::query();

        AppliesListFilters::search($query, $request, ['nombre', 'slug', 'informacion', 'categoria']);
        AppliesListFilters::exact($query, $request, 'categoria');
        AppliesListFilters::boolean($query, $request, 'activo');

        return $query
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(ResolvesPagination::perPage($request));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('misiones', 'slug')],
            'informacion' => 'required|string',
            'imagen' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'nullable|boolean',
            'url_externa' => 'nullable|url|max:255',
        ]);

        $validated['slug'] = GeneratesUniqueSlugs::make(
            Misione::class,
            $validated['slug'] ?? $validated['nombre']
        );

        return Misione::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show($identifier)
    {
        return FindsByIdOrSlug::firstOrFail(Misione::query(), $identifier);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Misione $misione)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $misione = Misione::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('misiones', 'slug')->ignore($misione->id)],
            'informacion' => 'sometimes|required|string',
            'imagen' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'nullable|boolean',
            'url_externa' => 'nullable|url|max:255',
        ]);

        if (! empty($validated['slug'])) {
            $validated['slug'] = GeneratesUniqueSlugs::make(Misione::class, $validated['slug'], $misione->id);
        }

        $misione->update($validated);

        return $misione;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Misione::destroy($id);

        return response()->json(['mensaje' => 'Misión eliminada']);
    }
}
