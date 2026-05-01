<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\Ministerio;
use App\Models\Misione;
use App\Models\Noticia;
use App\Models\PaginaInstitucional;
use App\Models\Recurso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusquedaGlobalApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_search_returns_grouped_public_results(): void
    {
        $ministerio = Ministerio::create([
            'nombre' => 'Ministerio de Familias',
            'slug' => 'ministerio-de-familias',
            'descripcion' => 'Apoyo pastoral para familias.',
            'activo' => true,
        ]);

        Noticia::create([
            'titulo' => 'Retiro de familias',
            'slug' => 'retiro-de-familias',
            'resumen' => 'Retiro especial.',
            'contenido' => 'Actividad pastoral para familias.',
            'ministerio_id' => $ministerio->id,
        ]);

        Evento::create([
            'titulo' => 'Conferencia de familias',
            'slug' => 'conferencia-de-familias',
            'descripcion' => 'Evento pastoral para familias.',
            'fecha_inicio' => '2026-05-01 10:00:00',
            'activo' => true,
        ]);

        Recurso::create([
            'nombre' => 'Guia para familias',
            'slug' => 'guia-para-familias',
            'informacion' => 'Recurso pastoral para familias.',
            'activo' => true,
        ]);

        Misione::create([
            'nombre' => 'Misiones familiares',
            'slug' => 'misiones-familiares',
            'informacion' => 'Servicio pastoral para familias.',
            'activo' => true,
        ]);

        PaginaInstitucional::create([
            'titulo' => 'Familias en la iglesia',
            'slug' => 'familias-en-la-iglesia',
            'contenido' => 'Pagina pastoral para familias.',
            'activo' => true,
        ]);

        $this->getJson('/api/buscar?q=familias')
            ->assertOk()
            ->assertJsonPath('query', 'familias')
            ->assertJsonPath('total', 6)
            ->assertJsonPath('results.noticias.0.type', 'noticia')
            ->assertJsonPath('results.eventos.0.type', 'evento')
            ->assertJsonPath('results.recursos.0.type', 'recurso')
            ->assertJsonPath('results.ministerios.0.type', 'ministerio')
            ->assertJsonPath('results.misiones.0.type', 'mision')
            ->assertJsonPath('results.paginas.0.type', 'pagina');
    }

    public function test_global_search_does_not_require_authentication(): void
    {
        $this->getJson('/api/buscar?q=iglesia')
            ->assertOk()
            ->assertJsonStructure(['query', 'total', 'results']);
    }

    public function test_global_search_respects_limit_per_group(): void
    {
        foreach (range(1, 3) as $number) {
            PaginaInstitucional::create([
                'titulo' => "Iglesia pagina {$number}",
                'slug' => "iglesia-pagina-{$number}",
                'contenido' => 'Contenido sobre iglesia.',
                'activo' => true,
            ]);
        }

        $this->getJson('/api/buscar?q=iglesia&limit=2')
            ->assertOk()
            ->assertJsonCount(2, 'results.paginas');
    }

    public function test_global_search_excludes_inactive_public_content(): void
    {
        Evento::create([
            'titulo' => 'Evento oculto',
            'slug' => 'evento-oculto',
            'descripcion' => 'Contenido oculto.',
            'fecha_inicio' => '2026-05-01 10:00:00',
            'activo' => false,
        ]);

        Recurso::create([
            'nombre' => 'Recurso oculto',
            'slug' => 'recurso-oculto',
            'informacion' => 'Contenido oculto.',
            'activo' => false,
        ]);

        $this->getJson('/api/buscar?q=oculto')
            ->assertOk()
            ->assertJsonPath('total', 0)
            ->assertJsonCount(0, 'results.eventos')
            ->assertJsonCount(0, 'results.recursos');
    }

    public function test_empty_query_returns_empty_groups(): void
    {
        $this->getJson('/api/buscar')
            ->assertOk()
            ->assertJsonPath('total', 0)
            ->assertJsonCount(0, 'results.noticias')
            ->assertJsonCount(0, 'results.eventos')
            ->assertJsonCount(0, 'results.recursos')
            ->assertJsonCount(0, 'results.ministerios')
            ->assertJsonCount(0, 'results.misiones')
            ->assertJsonCount(0, 'results.paginas');
    }
}
