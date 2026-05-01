<?php

namespace Tests\Feature;

use App\Models\Ministerio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentFieldsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_ministerio_accepts_extended_fields_and_generates_slug(): void
    {
        $token = User::factory()->create()->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/ministerios', [
                'nombre' => 'Ministerio de Jovenes',
                'descripcion' => 'Trabajo con jovenes y lideres.',
                'imagen' => 'https://example.com/jovenes.jpg',
                'categoria' => 'Jovenes',
                'orden' => 3,
                'activo' => true,
                'url_externa' => 'https://example.com',
            ])
            ->assertCreated()
            ->assertJsonFragment([
                'nombre' => 'Ministerio de Jovenes',
                'slug' => 'ministerio-de-jovenes',
                'categoria' => 'Jovenes',
                'orden' => 3,
                'activo' => true,
            ]);
    }

    public function test_noticia_accepts_publication_fields(): void
    {
        $token = User::factory()->create()->createToken('vue-admin')->plainTextToken;
        $ministerio = Ministerio::create(['nombre' => 'Misiones', 'slug' => 'misiones']);

        $this->withToken($token)
            ->postJson('/api/noticias', [
                'titulo' => 'Nueva conferencia',
                'resumen' => 'Resumen corto de la noticia.',
                'contenido' => 'Contenido completo.',
                'autor' => 'Equipo editorial',
                'publicado_en' => '2026-05-01 10:00:00',
                'estado' => 'publicado',
                'destacada' => true,
                'categoria' => 'Eventos',
                'meta_title' => 'Nueva conferencia',
                'meta_description' => 'Informacion sobre la nueva conferencia.',
                'ministerio_id' => $ministerio->id,
            ])
            ->assertCreated()
            ->assertJsonFragment([
                'titulo' => 'Nueva conferencia',
                'slug' => 'nueva-conferencia',
                'estado' => 'publicado',
                'destacada' => true,
                'categoria' => 'Eventos',
            ]);
    }

    public function test_recurso_accepts_download_fields(): void
    {
        $token = User::factory()->create()->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/recursos', [
                'nombre' => 'Guia para lideres',
                'informacion' => 'Material de apoyo.',
                'categoria' => 'Para ministros',
                'tipo' => 'PDF',
                'archivo_url' => 'https://example.com/guia.pdf',
                'link' => 'https://example.com/guia',
                'descargable' => true,
                'destacado' => true,
                'orden' => 1,
                'activo' => true,
            ])
            ->assertCreated()
            ->assertJsonFragment([
                'nombre' => 'Guia para lideres',
                'slug' => 'guia-para-lideres',
                'tipo' => 'PDF',
                'descargable' => true,
                'destacado' => true,
            ]);
    }
}
