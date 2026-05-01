<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_read_eventos(): void
    {
        Evento::create($this->eventoPayload([
            'titulo' => 'Culto familiar',
            'slug' => 'culto-familiar',
        ]));

        $this->getJson('/api/eventos')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'culto-familiar']);
    }

    public function test_pastor_can_create_evento(): void
    {
        $token = User::factory()->create(['role' => 'pastor'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/eventos', $this->eventoPayload([
                'titulo' => 'Conferencia de jovenes',
                'categoria' => 'Jovenes',
                'destacado' => true,
            ]))
            ->assertCreated()
            ->assertJsonFragment([
                'titulo' => 'Conferencia de jovenes',
                'slug' => 'conferencia-de-jovenes',
                'categoria' => 'Jovenes',
                'destacado' => true,
            ]);
    }

    public function test_regular_user_cannot_create_evento(): void
    {
        $token = User::factory()->create(['role' => 'usuario'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/eventos', $this->eventoPayload())
            ->assertForbidden();
    }

    public function test_evento_can_be_read_by_slug(): void
    {
        Evento::create($this->eventoPayload([
            'titulo' => 'Cena comunitaria',
            'slug' => 'cena-comunitaria',
        ]));

        $this->getJson('/api/eventos/cena-comunitaria')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'cena-comunitaria']);
    }

    public function test_eventos_can_be_filtered_and_searched(): void
    {
        Evento::create($this->eventoPayload([
            'titulo' => 'Conferencia de liderazgo',
            'slug' => 'conferencia-de-liderazgo',
            'descripcion' => 'Entrenamiento pastoral para lideres.',
            'categoria' => 'Liderazgo',
            'ciudad' => 'Miami',
            'estado' => 'programado',
            'destacado' => true,
            'activo' => true,
        ]));

        Evento::create($this->eventoPayload([
            'titulo' => 'Actividad infantil',
            'slug' => 'actividad-infantil',
            'descripcion' => 'Actividad para ninos.',
            'categoria' => 'Ninos',
            'ciudad' => 'Hialeah',
            'estado' => 'programado',
            'destacado' => false,
            'activo' => true,
        ]));

        $this->getJson('/api/eventos?search=pastoral&categoria=Liderazgo&ciudad=Miami&estado=programado&destacado=true&activo=true')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.slug', 'conferencia-de-liderazgo');
    }

    public function test_eventos_are_paginated(): void
    {
        foreach (range(1, 3) as $number) {
            Evento::create($this->eventoPayload([
                'titulo' => "Evento {$number}",
                'slug' => "evento-{$number}",
                'fecha_inicio' => now()->addDays($number),
            ]));
        }

        $this->getJson('/api/eventos?per_page=2&page=2')
            ->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonPath('per_page', 2)
            ->assertJsonPath('total', 3)
            ->assertJsonCount(1, 'data');
    }

    public function test_fecha_fin_must_be_after_or_equal_fecha_inicio(): void
    {
        $token = User::factory()->create(['role' => 'admin'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/eventos', $this->eventoPayload([
                'fecha_inicio' => '2026-05-02 10:00:00',
                'fecha_fin' => '2026-05-01 10:00:00',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['fecha_fin']);
    }

    private function eventoPayload(array $overrides = []): array
    {
        return array_merge([
            'titulo' => 'Culto especial',
            'descripcion' => 'Una actividad especial de la iglesia.',
            'resumen' => 'Actividad especial.',
            'imagen' => 'https://example.com/evento.jpg',
            'categoria' => 'General',
            'fecha_inicio' => '2026-05-01 10:00:00',
            'fecha_fin' => '2026-05-01 12:00:00',
            'lugar' => 'Templo principal',
            'direccion' => '123 Main St',
            'ciudad' => 'Miami',
            'estado' => 'programado',
            'destacado' => false,
            'activo' => true,
            'registro_url' => 'https://example.com/registro',
            'capacidad' => 120,
        ], $overrides);
    }
}
