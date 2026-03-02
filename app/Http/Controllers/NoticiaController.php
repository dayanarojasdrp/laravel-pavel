<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Models\Ministerio;
use Illuminate\Http\Request;

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
            'contenido' => 'required|string',
            'imagen' => 'nullable|string',
            'ministerio_id' => 'required|exists:ministerios,id',
        ]);

        return Noticia::create($validated);
    
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {$noticia = Noticia::with('ministerio')->where('id', $id)->first();

    if (!$noticia) {
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
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'imagen' => 'nullable|string',
            'ministerio_id' => 'required|exists:ministerios,id',
             ]);

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
