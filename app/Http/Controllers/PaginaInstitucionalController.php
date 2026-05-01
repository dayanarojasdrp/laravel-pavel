<?php

namespace App\Http\Controllers;

use App\Models\PaginaInstitucional;
use App\Support\AppliesListFilters;
use App\Support\FindsByIdOrSlug;
use App\Support\GeneratesUniqueSlugs;
use App\Support\ResolvesPagination;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaginaInstitucionalController extends Controller
{
    public function index(Request $request)
    {
        $query = PaginaInstitucional::query();

        AppliesListFilters::search($query, $request, ['titulo', 'slug', 'contenido', 'resumen', 'seccion']);
        AppliesListFilters::exact($query, $request, 'seccion');
        AppliesListFilters::boolean($query, $request, 'activo');

        return $query
            ->orderBy('orden')
            ->orderBy('titulo')
            ->paginate(ResolvesPagination::perPage($request));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePagina($request, new PaginaInstitucional);

        $validated['slug'] = GeneratesUniqueSlugs::make(
            PaginaInstitucional::class,
            $validated['slug'] ?? $validated['titulo']
        );

        return response()->json(PaginaInstitucional::create($validated), 201);
    }

    public function show($identifier)
    {
        return FindsByIdOrSlug::firstOrFail(PaginaInstitucional::query(), $identifier);
    }

    public function update(Request $request, $id)
    {
        $pagina = PaginaInstitucional::findOrFail($id);
        $validated = $this->validatePagina($request, $pagina, true);

        if (! empty($validated['slug'])) {
            $validated['slug'] = GeneratesUniqueSlugs::make(PaginaInstitucional::class, $validated['slug'], $pagina->id);
        }

        $pagina->update($validated);

        return $pagina;
    }

    public function destroy($id)
    {
        PaginaInstitucional::destroy($id);

        return response()->json(['mensaje' => 'Pagina eliminada']);
    }

    private function validatePagina(Request $request, PaginaInstitucional $pagina, bool $partial = false): array
    {
        $required = $partial ? 'sometimes|required' : 'required';

        return $request->validate([
            'titulo' => "{$required}|string|max:255",
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('paginas_institucionales', 'slug')->ignore($pagina->id)],
            'contenido' => "{$required}|string",
            'resumen' => 'nullable|string',
            'imagen' => 'nullable|string|max:255',
            'seccion' => 'nullable|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ]);
    }
}
