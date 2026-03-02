<?php

namespace App\Http\Controllers;

use App\Models\Misione;
use Illuminate\Http\Request;

class MisioneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return Misione::all();
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
            'informacion' => 'required|string',
    'imagen' => 'nullable|string', // puede ser URL o nombre de archivo
        ]);

        return Misione::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return Misione::findOrFail($id);
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
            'nombre' => 'required|string|max:255',
            'informacion' => 'required|string',
        ]);

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
