<?php

namespace App\Http\Controllers;

use App\Models\Ministerio;
use Illuminate\Http\Request;

class MinisterioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
          return Ministerio::all();
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
        ]);

        return Ministerio::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
         return Ministerio::findOrFail($id);
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
            'nombre' => 'required|string|max:255',
        ]);

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
