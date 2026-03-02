<?php

namespace App\Http\Controllers;

use App\Models\Recurso;
use App\Models\Ministerio;
use Illuminate\Http\Request;

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
        'informacion' => 'required|string',
        'imagen' => 'nullable|string', // puede ser URL o nombre de archivo
    ]);

    // Crear el recurso con los datos validados
    $recurso = Recurso::create($validated);

    // Devolver el recurso creado
    return response()->json($recurso, 201);
}

    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return Recurso::findOrFail($id);
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
            'nombre' => 'required|string|max:255',
            'informacion' => 'required|string',
            'imagen' => 'nullable|string',
        ]);

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
