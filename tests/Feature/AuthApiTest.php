<?php

namespace Tests\Feature;

use App\Models\Ministerio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_reads_do_not_require_authentication(): void
    {
        Ministerio::create(['nombre' => 'Jovenes']);

        $this->getJson('/api/ministerios')
            ->assertOk()
            ->assertJsonFragment(['nombre' => 'Jovenes']);
    }

    public function test_writes_require_authentication(): void
    {
        $this->postJson('/api/ministerios', ['nombre' => 'Jovenes'])
            ->assertUnauthorized();
    }

    public function test_user_can_login_and_use_bearer_token(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ])
            ->assertOk()
            ->assertJsonStructure(['user', 'token']);

        $token = $loginResponse->json('token');

        $this->withToken($token)
            ->postJson('/api/ministerios', ['nombre' => 'Jovenes'])
            ->assertCreated()
            ->assertJsonFragment(['nombre' => 'Jovenes']);
    }

    public function test_user_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();

        $token = User::factory()->create(['role' => 'usuario'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonStructure(['id', 'name', 'email', 'role']);
    }

    public function test_pastor_can_manage_content(): void
    {
        $token = User::factory()->create(['role' => 'pastor'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/ministerios', ['nombre' => 'Jovenes'])
            ->assertCreated()
            ->assertJsonFragment(['nombre' => 'Jovenes']);
    }

    public function test_editor_and_regular_user_cannot_manage_content(): void
    {
        $editorToken = User::factory()->create(['role' => 'editor'])->createToken('vue-admin')->plainTextToken;
        $userToken = User::factory()->create(['role' => 'usuario'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($editorToken)
            ->postJson('/api/ministerios', ['nombre' => 'Jovenes'])
            ->assertForbidden();

        $this->withToken($userToken)
            ->postJson('/api/ministerios', ['nombre' => 'Jovenes'])
            ->assertForbidden();
    }

    public function test_public_evento_writes_require_authentication(): void
    {
        $this->postJson('/api/eventos', [
            'titulo' => 'Culto especial',
            'descripcion' => 'Una actividad especial.',
            'fecha_inicio' => '2026-05-01 10:00:00',
        ])->assertUnauthorized();
    }

    public function test_public_pagina_writes_require_authentication(): void
    {
        $this->postJson('/api/paginas', [
            'titulo' => 'Sobre nosotros',
            'contenido' => 'Contenido institucional.',
        ])->assertUnauthorized();
    }

    public function test_logout_revokes_current_bearer_token(): void
    {
        $user = User::factory()->create(['role' => 'usuario']);
        $token = $user->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->app['auth']->forgetGuards();

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertUnauthorized();
    }
}
