<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'carpeta' => 'nullable|string|max:50',
        ]);

        $folder = $this->resolveFolder($validated['carpeta'] ?? 'general');
        $file = $validated['imagen'];
        $filename = Str::uuid().'.'.$file->extension();
        $path = $file->storeAs("uploads/{$folder}", $filename, 'public');

        return response()->json([
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ], 201);
    }

    private function resolveFolder(string $folder): string
    {
        return Str::slug($folder) ?: 'general';
    }
}
