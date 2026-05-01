<?php

namespace App\Http\Controllers;

use App\Models\MensajeContacto;
use App\Support\AppliesListFilters;
use App\Support\ResolvesPagination;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MensajeContactoController extends Controller
{
    public function index(Request $request)
    {
        $query = MensajeContacto::query();

        AppliesListFilters::search($query, $request, ['nombre', 'email', 'telefono', 'asunto', 'mensaje']);
        AppliesListFilters::exact($query, $request, 'estado');
        AppliesListFilters::boolean($query, $request, 'leido');

        return $query
            ->orderByDesc('created_at')
            ->paginate(ResolvesPagination::perPage($request));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'asunto' => 'required|string|max:255',
            'mensaje' => 'required|string|max:5000',
        ]);

        $mensaje = MensajeContacto::create($validated)->refresh();

        return response()->json([
            'mensaje' => 'Mensaje recibido',
            'data' => $mensaje,
        ], 201);
    }

    public function show($id)
    {
        return MensajeContacto::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $mensaje = MensajeContacto::findOrFail($id);
        $validated = $request->validate([
            'estado' => ['nullable', Rule::in(['nuevo', 'leido', 'respondido', 'archivado'])],
            'leido' => 'nullable|boolean',
            'respondido_en' => 'nullable|date',
            'notas_internas' => 'nullable|string',
        ]);

        if (($validated['estado'] ?? null) === 'respondido' && ! array_key_exists('respondido_en', $validated)) {
            $validated['respondido_en'] = now();
        }

        if (($validated['estado'] ?? null) === 'leido') {
            $validated['leido'] = true;
        }

        $mensaje->update($validated);

        return $mensaje;
    }

    public function destroy($id)
    {
        MensajeContacto::destroy($id);

        return response()->json(['mensaje' => 'Mensaje eliminado']);
    }
}
