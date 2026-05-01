<?php

namespace App\Http\Controllers;

use App\Models\Ministerio;
use App\Support\FindsByIdOrSlug;
use App\Support\GeneratesUniqueSlugs;
use App\Support\ResolvesPagination;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MinisterioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Ministerio::query()
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
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('ministerios', 'slug')],
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'nullable|boolean',
            'url_externa' => 'nullable|url|max:255',
        ]);

        $validated['slug'] = GeneratesUniqueSlugs::make(
            Ministerio::class,
            $validated['slug'] ?? $validated['nombre']
        );

        return Ministerio::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show($identifier)
    {
        return FindsByIdOrSlug::firstOrFail(Ministerio::query(), $identifier);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ministerio $ministerio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ministerio = Ministerio::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('ministerios', 'slug')->ignore($ministerio->id)],
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'nullable|boolean',
            'url_externa' => 'nullable|url|max:255',
        ]);

        if (! empty($validated['slug'])) {
            $validated['slug'] = GeneratesUniqueSlugs::make(Ministerio::class, $validated['slug'], $ministerio->id);
        }

        $ministerio->update($validated);

        return $ministerio;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Ministerio::destroy($id);

        return response()->json(['mensaje' => 'Ministerio eliminado']);
    }
}
