<?php

namespace App\Http\Controllers;

use App\Models\Recurso;
use App\Support\FindsByIdOrSlug;
use App\Support\GeneratesUniqueSlugs;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Recurso::all();
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

        // Validar los datos recibidos
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('recursos', 'slug')],
            'informacion' => 'required|string',
            'imagen' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'tipo' => 'nullable|string|max:255',
            'archivo_url' => 'nullable|url|max:255',
            'link' => 'nullable|url|max:255',
            'descargable' => 'nullable|boolean',
            'destacado' => 'nullable|boolean',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'nullable|boolean',
        ]);

        $validated['slug'] = GeneratesUniqueSlugs::make(
            Recurso::class,
            $validated['slug'] ?? $validated['nombre']
        );

        // Crear el recurso con los datos validados
        $recurso = Recurso::create($validated);

        // Devolver el recurso creado
        return response()->json($recurso, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($identifier)
    {
        return FindsByIdOrSlug::firstOrFail(Recurso::query(), $identifier);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recurso $recurso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $recurso = Recurso::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('recursos', 'slug')->ignore($recurso->id)],
            'informacion' => 'sometimes|required|string',
            'imagen' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'tipo' => 'nullable|string|max:255',
            'archivo_url' => 'nullable|url|max:255',
            'link' => 'nullable|url|max:255',
            'descargable' => 'nullable|boolean',
            'destacado' => 'nullable|boolean',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'nullable|boolean',
        ]);

        if (! empty($validated['slug'])) {
            $validated['slug'] = GeneratesUniqueSlugs::make(Recurso::class, $validated['slug'], $recurso->id);
        }

        $recurso->update($validated);

        return $recurso;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Recurso::destroy($id);

        return response()->json(['mensaje' => 'Recurso eliminado']);
    }
}
