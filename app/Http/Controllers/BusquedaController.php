<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Ministerio;
use App\Models\Misione;
use App\Models\Noticia;
use App\Models\PaginaInstitucional;
use App\Models\Recurso;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BusquedaController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
            'search' => 'nullable|string|max:255',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        $term = trim((string) ($validated['q'] ?? $validated['search'] ?? ''));
        $limit = (int) ($validated['limit'] ?? 5);

        if ($term === '') {
            return response()->json([
                'query' => $term,
                'total' => 0,
                'results' => $this->emptyResults(),
            ]);
        }

        $results = [
            'noticias' => $this->searchNoticias($term, $limit),
            'eventos' => $this->searchEventos($term, $limit),
            'recursos' => $this->searchRecursos($term, $limit),
            'ministerios' => $this->searchMinisterios($term, $limit),
            'misiones' => $this->searchMisiones($term, $limit),
            'paginas' => $this->searchPaginas($term, $limit),
        ];

        return response()->json([
            'query' => $term,
            'total' => collect($results)->sum(fn ($items) => $items->count()),
            'results' => $results,
        ]);
    }

    private function searchNoticias(string $term, int $limit)
    {
        return Noticia::query()
            ->select(['id', 'titulo', 'slug', 'resumen', 'categoria', 'imagen', 'publicado_en'])
            ->where(function (Builder $query) use ($term) {
                $query->where('titulo', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('resumen', 'like', "%{$term}%")
                    ->orWhere('contenido', 'like', "%{$term}%")
                    ->orWhere('categoria', 'like', "%{$term}%");
            })
            ->orderByDesc('publicado_en')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn (Noticia $noticia) => [
                'type' => 'noticia',
                'id' => $noticia->id,
                'title' => $noticia->titulo,
                'slug' => $noticia->slug,
                'summary' => $noticia->resumen,
                'category' => $noticia->categoria,
                'image' => $noticia->imagen,
                'date' => $noticia->publicado_en,
            ]);
    }

    private function searchEventos(string $term, int $limit)
    {
        return Evento::query()
            ->select(['id', 'titulo', 'slug', 'resumen', 'categoria', 'imagen', 'fecha_inicio', 'lugar', 'ciudad'])
            ->where('activo', true)
            ->where(function (Builder $query) use ($term) {
                $query->where('titulo', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('descripcion', 'like', "%{$term}%")
                    ->orWhere('resumen', 'like', "%{$term}%")
                    ->orWhere('categoria', 'like', "%{$term}%")
                    ->orWhere('lugar', 'like', "%{$term}%")
                    ->orWhere('ciudad', 'like', "%{$term}%");
            })
            ->orderBy('fecha_inicio')
            ->limit($limit)
            ->get()
            ->map(fn (Evento $evento) => [
                'type' => 'evento',
                'id' => $evento->id,
                'title' => $evento->titulo,
                'slug' => $evento->slug,
                'summary' => $evento->resumen,
                'category' => $evento->categoria,
                'image' => $evento->imagen,
                'date' => $evento->fecha_inicio,
                'location' => $evento->lugar,
                'city' => $evento->ciudad,
            ]);
    }

    private function searchRecursos(string $term, int $limit)
    {
        return Recurso::query()
            ->select(['id', 'nombre', 'slug', 'informacion', 'categoria', 'tipo', 'imagen'])
            ->where('activo', true)
            ->where(function (Builder $query) use ($term) {
                $query->where('nombre', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('informacion', 'like', "%{$term}%")
                    ->orWhere('categoria', 'like', "%{$term}%")
                    ->orWhere('tipo', 'like', "%{$term}%");
            })
            ->orderBy('orden')
            ->orderBy('nombre')
            ->limit($limit)
            ->get()
            ->map(fn (Recurso $recurso) => [
                'type' => 'recurso',
                'id' => $recurso->id,
                'title' => $recurso->nombre,
                'slug' => $recurso->slug,
                'summary' => $recurso->informacion,
                'category' => $recurso->categoria,
                'resource_type' => $recurso->tipo,
                'image' => $recurso->imagen,
            ]);
    }

    private function searchMinisterios(string $term, int $limit)
    {
        return Ministerio::query()
            ->select(['id', 'nombre', 'slug', 'descripcion', 'categoria', 'imagen'])
            ->where('activo', true)
            ->where(function (Builder $query) use ($term) {
                $query->where('nombre', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('descripcion', 'like', "%{$term}%")
                    ->orWhere('categoria', 'like', "%{$term}%");
            })
            ->orderBy('orden')
            ->orderBy('nombre')
            ->limit($limit)
            ->get()
            ->map(fn (Ministerio $ministerio) => [
                'type' => 'ministerio',
                'id' => $ministerio->id,
                'title' => $ministerio->nombre,
                'slug' => $ministerio->slug,
                'summary' => $ministerio->descripcion,
                'category' => $ministerio->categoria,
                'image' => $ministerio->imagen,
            ]);
    }

    private function searchMisiones(string $term, int $limit)
    {
        return Misione::query()
            ->select(['id', 'nombre', 'slug', 'informacion', 'categoria', 'imagen'])
            ->where('activo', true)
            ->where(function (Builder $query) use ($term) {
                $query->where('nombre', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('informacion', 'like', "%{$term}%")
                    ->orWhere('categoria', 'like', "%{$term}%");
            })
            ->orderBy('orden')
            ->orderBy('nombre')
            ->limit($limit)
            ->get()
            ->map(fn (Misione $mision) => [
                'type' => 'mision',
                'id' => $mision->id,
                'title' => $mision->nombre,
                'slug' => $mision->slug,
                'summary' => $mision->informacion,
                'category' => $mision->categoria,
                'image' => $mision->imagen,
            ]);
    }

    private function searchPaginas(string $term, int $limit)
    {
        return PaginaInstitucional::query()
            ->select(['id', 'titulo', 'slug', 'resumen', 'seccion', 'imagen'])
            ->where('activo', true)
            ->where(function (Builder $query) use ($term) {
                $query->where('titulo', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('contenido', 'like', "%{$term}%")
                    ->orWhere('resumen', 'like', "%{$term}%")
                    ->orWhere('seccion', 'like', "%{$term}%");
            })
            ->orderBy('orden')
            ->orderBy('titulo')
            ->limit($limit)
            ->get()
            ->map(fn (PaginaInstitucional $pagina) => [
                'type' => 'pagina',
                'id' => $pagina->id,
                'title' => $pagina->titulo,
                'slug' => $pagina->slug,
                'summary' => $pagina->resumen,
                'section' => $pagina->seccion,
                'image' => $pagina->imagen,
            ]);
    }

    private function emptyResults(): array
    {
        return [
            'noticias' => [],
            'eventos' => [],
            'recursos' => [],
            'ministerios' => [],
            'misiones' => [],
            'paginas' => [],
        ];
    }
}
