<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Support\GeneratesUniqueSlugs;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NoticiaController extends Controller
{
    public function index()
    {
        return Noticia::with('ministerio')->get();
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
    public function show($id)
    {
        $noticia = Noticia::with('ministerio')->where('id', $id)->first();

        if (! $noticia) {
            return response()->json(['error' => 'Noticia no encontrada'], 404);
        }

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

        if (array_key_exists('slug', $validated)) {
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

    public function porMinisterio($id)
    {
        return Noticia::where('ministerio_id', $id)->get();
    }
}
