<?php

namespace App\Http\Controllers;

use App\Models\Ministerio;
use App\Models\Noticia;
use App\Support\AppliesListFilters;
use App\Support\FindsByIdOrSlug;
use App\Support\GeneratesUniqueSlugs;
use App\Support\ResolvesPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NoticiaController extends Controller
{
    public function index(Request $request)
    {
        $query = Noticia::with('ministerio');

        $this->applyFilters($query, $request);

        return $query
            ->orderByDesc('publicado_en')
            ->orderByDesc('created_at')
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
            'titulo' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('noticias', 'slug')],
            'resumen' => 'nullable|string',
            'contenido' => 'required|string',
            'imagen' => 'nullable|string|max:255',
            'autor' => 'nullable|string|max:255',
            'publicado_en' => 'nullable|date',
            'estado' => ['nullable', Rule::in(['borrador', 'publicado', 'archivado'])],
            'destacada' => 'nullable|boolean',
            'categoria' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'ministerio_id' => 'required|exists:ministerios,id',
        ]);

        $validated['slug'] = GeneratesUniqueSlugs::make(
            Noticia::class,
            $validated['slug'] ?? $validated['titulo']
        );

        return Noticia::create($validated);

    }

    /**
     * Display the specified resource.
     */
    public function show($identifier)
    {
        $noticia = FindsByIdOrSlug::firstOrFail(Noticia::with('ministerio'), $identifier);

        return $noticia;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Noticia $noticia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $noticia = Noticia::findOrFail($id);
        $validated = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('noticias', 'slug')->ignore($noticia->id)],
            'resumen' => 'nullable|string',
            'contenido' => 'sometimes|required|string',
            'imagen' => 'nullable|string|max:255',
            'autor' => 'nullable|string|max:255',
            'publicado_en' => 'nullable|date',
            'estado' => ['nullable', Rule::in(['borrador', 'publicado', 'archivado'])],
            'destacada' => 'nullable|boolean',
            'categoria' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'ministerio_id' => 'sometimes|required|exists:ministerios,id',
        ]);

        if (! empty($validated['slug'])) {
            $validated['slug'] = GeneratesUniqueSlugs::make(Noticia::class, $validated['slug'], $noticia->id);
        }

        $noticia->update($validated);

        return $noticia;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Noticia::destroy($id);

        return response()->json(['mensaje' => 'Noticia eliminada']);
    }

    public function porMinisterio(Request $request, $identifier)
    {
        $ministerio = FindsByIdOrSlug::firstOrFail(Ministerio::query(), $identifier);

        $query = Noticia::where('ministerio_id', $ministerio->id);

        $this->applyFilters($query, $request);

        return $query
            ->orderByDesc('publicado_en')
            ->orderByDesc('created_at')
            ->paginate(ResolvesPagination::perPage($request));
    }

    private function applyFilters(Builder $query, Request $request): void
    {
        AppliesListFilters::search($query, $request, ['titulo', 'slug', 'resumen', 'contenido', 'autor', 'categoria']);
        AppliesListFilters::exact($query, $request, 'categoria');
        AppliesListFilters::exact($query, $request, 'estado');
        AppliesListFilters::exact($query, $request, 'ministerio_id');
        AppliesListFilters::boolean($query, $request, 'destacada');
    }
}
