<?php

namespace Tests\Feature;

use App\Models\Ministerio;
use App\Models\Misione;
use App\Models\Noticia;
use App\Models\Recurso;
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

    public function test_content_can_be_read_by_slug(): void
    {
        $ministerio = Ministerio::create([
            'nombre' => 'Ministerio de Jovenes',
            'slug' => 'ministerio-de-jovenes',
        ]);

        $noticia = Noticia::create([
            'titulo' => 'Nueva conferencia',
            'slug' => 'nueva-conferencia',
            'contenido' => 'Contenido completo.',
            'ministerio_id' => $ministerio->id,
        ]);

        Misione::create([
            'nombre' => 'Misiones Globales',
            'slug' => 'misiones-globales',
            'informacion' => 'Informacion de misiones.',
        ]);

        Recurso::create([
            'nombre' => 'Guia para lideres',
            'slug' => 'guia-para-lideres',
            'informacion' => 'Material de apoyo.',
        ]);

        $this->getJson('/api/ministerios/ministerio-de-jovenes')
            ->assertOk()
            ->assertJsonFragment(['id' => $ministerio->id]);

        $this->getJson('/api/noticias/nueva-conferencia')
            ->assertOk()
            ->assertJsonFragment(['id' => $noticia->id]);

        $this->getJson('/api/misiones/misiones-globales')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'misiones-globales']);

        $this->getJson('/api/recursos/guia-para-lideres')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'guia-para-lideres']);
    }

    public function test_ministerio_news_can_be_read_by_ministerio_slug(): void
    {
        $ministerio = Ministerio::create([
            'nombre' => 'Misiones',
            'slug' => 'misiones',
        ]);

        Noticia::create([
            'titulo' => 'Reporte de misiones',
            'slug' => 'reporte-de-misiones',
            'contenido' => 'Contenido completo.',
            'ministerio_id' => $ministerio->id,
        ]);

        $this->getJson('/api/ministerios/misiones/noticias')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'reporte-de-misiones']);
    }

    public function test_index_endpoints_are_paginated(): void
    {
        Ministerio::create(['nombre' => 'Uno', 'slug' => 'uno']);
        Ministerio::create(['nombre' => 'Dos', 'slug' => 'dos']);
        Ministerio::create(['nombre' => 'Tres', 'slug' => 'tres']);

        $this->getJson('/api/ministerios?per_page=2&page=2')
            ->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('per_page', 2)
            ->assertJsonPath('total', 3);
    }

    public function test_pagination_per_page_is_capped(): void
    {
        foreach (range(1, 55) as $number) {
            Ministerio::create([
                'nombre' => "Ministerio {$number}",
                'slug' => "ministerio-{$number}",
            ]);
        }

        $this->getJson('/api/ministerios?per_page=200')
            ->assertOk()
            ->assertJsonPath('per_page', 50)
            ->assertJsonCount(50, 'data');
    }
}
