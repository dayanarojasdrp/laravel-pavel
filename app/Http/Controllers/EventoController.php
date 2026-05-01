<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Support\AppliesListFilters;
use App\Support\FindsByIdOrSlug;
use App\Support\GeneratesUniqueSlugs;
use App\Support\ResolvesPagination;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventoController extends Controller
{
    public function index(Request $request)
    {
        $query = Evento::query();

        AppliesListFilters::search($query, $request, ['titulo', 'slug', 'descripcion', 'resumen', 'categoria', 'lugar', 'direccion', 'ciudad']);
        AppliesListFilters::exact($query, $request, 'categoria');
        AppliesListFilters::exact($query, $request, 'ciudad');
        AppliesListFilters::exact($query, $request, 'estado');
        AppliesListFilters::boolean($query, $request, 'destacado');
        AppliesListFilters::boolean($query, $request, 'activo');

        if ($request->boolean('proximos')) {
            $query->where('fecha_inicio', '>=', now());
        }

        return $query
            ->orderBy('fecha_inicio')
            ->paginate(ResolvesPagination::perPage($request));
    }

    public function store(Request $request)
    {
        $validated = $this->validateEvento($request, new Evento);

        $validated['slug'] = GeneratesUniqueSlugs::make(
            Evento::class,
            $validated['slug'] ?? $validated['titulo']
        );

        return response()->json(Evento::create($validated), 201);
    }

    public function show($identifier)
    {
        return FindsByIdOrSlug::firstOrFail(Evento::query(), $identifier);
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);
        $validated = $this->validateEvento($request, $evento, true);

        if (! empty($validated['slug'])) {
            $validated['slug'] = GeneratesUniqueSlugs::make(Evento::class, $validated['slug'], $evento->id);
        }

        $evento->update($validated);

        return $evento;
    }

    public function destroy($id)
    {
        Evento::destroy($id);

        return response()->json(['mensaje' => 'Evento eliminado']);
    }

    private function validateEvento(Request $request, Evento $evento, bool $partial = false): array
    {
        $required = $partial ? 'sometimes|required' : 'required';

        return $request->validate([
            'titulo' => "{$required}|string|max:255",
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('eventos', 'slug')->ignore($evento->id)],
            'descripcion' => "{$required}|string",
            'resumen' => 'nullable|string',
            'imagen' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'fecha_inicio' => "{$required}|date",
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'lugar' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'estado' => ['nullable', Rule::in(['programado', 'cancelado', 'finalizado', 'borrador'])],
            'destacado' => 'nullable|boolean',
            'activo' => 'nullable|boolean',
            'registro_url' => 'nullable|url|max:255',
            'capacidad' => 'nullable|integer|min:1',
        ]);
    }
}
