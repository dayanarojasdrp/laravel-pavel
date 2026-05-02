<?php

namespace Tests\Feature;

use App\Models\PaginaInstitucional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginaInstitucionalApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_read_paginas(): void
    {
        PaginaInstitucional::create($this->paginaPayload([
            'titulo' => 'Sobre nosotros',
            'slug' => 'sobre-nosotros',
        ]));

        $this->getJson('/api/paginas')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'sobre-nosotros']);
    }

    public function test_configured_admin_can_create_pagina(): void
    {
        $token = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/paginas', $this->paginaPayload([
                'titulo' => 'Nuestra vision',
                'seccion' => 'iglesia',
                'orden' => 2,
            ]))
            ->assertCreated()
            ->assertJsonFragment([
                'titulo' => 'Nuestra vision',
                'slug' => 'nuestra-vision',
                'seccion' => 'iglesia',
                'orden' => 2,
            ]);
    }

    public function test_regular_user_cannot_create_pagina(): void
    {
        $token = User::factory()->create(['role' => 'usuario'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/paginas', $this->paginaPayload())
            ->assertForbidden();
    }

    public function test_pagina_can_be_read_by_slug(): void
    {
        PaginaInstitucional::create($this->paginaPayload([
            'titulo' => 'Horarios',
            'slug' => 'horarios',
        ]));

        $this->getJson('/api/paginas/horarios')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'horarios']);
    }

    public function test_paginas_can_be_filtered_and_searched(): void
    {
        PaginaInstitucional::create($this->paginaPayload([
            'titulo' => 'Nuestra historia',
            'slug' => 'nuestra-historia',
            'contenido' => 'Historia de la iglesia y su comunidad.',
            'seccion' => 'iglesia',
            'activo' => true,
        ]));

        PaginaInstitucional::create($this->paginaPayload([
            'titulo' => 'Equipo pastoral',
            'slug' => 'equipo-pastoral',
            'contenido' => 'Liderazgo pastoral.',
            'seccion' => 'liderazgo',
            'activo' => false,
        ]));

        $this->getJson('/api/paginas?search=comunidad&seccion=iglesia&activo=true')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.slug', 'nuestra-historia');
    }

    public function test_paginas_are_paginated(): void
    {
        foreach (range(1, 3) as $number) {
            PaginaInstitucional::create($this->paginaPayload([
                'titulo' => "Pagina {$number}",
                'slug' => "pagina-{$number}",
                'orden' => $number,
            ]));
        }

        $this->getJson('/api/paginas?per_page=2&page=2')
            ->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonPath('per_page', 2)
            ->assertJsonPath('total', 3)
            ->assertJsonCount(1, 'data');
    }

    public function test_admin_can_update_pagina(): void
    {
        $token = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin'])->createToken('vue-admin')->plainTextToken;
        $pagina = PaginaInstitucional::create($this->paginaPayload([
            'titulo' => 'Mision',
            'slug' => 'mision',
        ]));

        $this->withToken($token)
            ->patchJson("/api/paginas/{$pagina->id}", [
                'titulo' => 'Mision actualizada',
                'contenido' => 'Contenido actualizado.',
                'activo' => false,
            ])
            ->assertOk()
            ->assertJsonFragment([
                'titulo' => 'Mision actualizada',
                'contenido' => 'Contenido actualizado.',
                'activo' => false,
            ]);
    }

    private function paginaPayload(array $overrides = []): array
    {
        return array_merge([
            'titulo' => 'Sobre la iglesia',
            'contenido' => 'Contenido institucional de la iglesia.',
            'resumen' => 'Resumen institucional.',
            'imagen' => 'https://example.com/iglesia.jpg',
            'seccion' => 'iglesia',
            'orden' => 1,
            'activo' => true,
            'meta_title' => 'Sobre la iglesia',
            'meta_description' => 'Informacion sobre la iglesia.',
        ], $overrides);
    }
}
