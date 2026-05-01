<?php

namespace Tests\Feature;

use App\Models\Ministerio;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use RuntimeException;
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

    public function test_login_is_rate_limited(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->postJson('/api/login', [
                'email' => 'missing@example.com',
                'password' => 'wrong-password',
            ])->assertUnprocessable();
        }

        $this->postJson('/api/login', [
            'email' => 'missing@example.com',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
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

    public function test_editor_can_manage_public_content_but_regular_user_cannot(): void
    {
        $editorToken = User::factory()->create(['role' => 'editor'])->createToken('vue-admin')->plainTextToken;
        $userToken = User::factory()->create(['role' => 'usuario'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($editorToken)
            ->postJson('/api/ministerios', ['nombre' => 'Jovenes'])
            ->assertCreated();

        $this->app['auth']->forgetGuards();

        $this->withToken($userToken)
            ->postJson('/api/ministerios', ['nombre' => 'Adultos'])
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

    public function test_authenticated_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPassword1',
            'role' => 'pastor',
        ]);
        $token = $user->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/change-password', [
                'current_password' => 'OldPassword1',
                'password' => 'NewPassword1',
                'password_confirmation' => 'NewPassword1',
            ])
            ->assertOk()
            ->assertJsonPath('mensaje', 'Contraseña actualizada');

        $this->assertDatabaseCount('personal_access_tokens', 0);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'NewPassword1',
        ])->assertOk();
    }

    public function test_password_reset_flow_updates_password(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'pastor@example.com',
            'password' => 'OldPassword1',
            'role' => 'pastor',
        ]);

        $this->postJson('/api/forgot-password', [
            'email' => $user->email,
        ])->assertOk();

        $token = Password::broker()->createToken($user);

        $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword1',
            'password_confirmation' => 'NewPassword1',
        ])->assertOk();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'NewPassword1',
        ])->assertOk();
    }

    public function test_database_seeder_requires_secure_admin_password(): void
    {
        $this->app['config']->set('app.env', 'production');

        putenv('ADMIN_EMAIL=admin@example.com');
        putenv('ADMIN_PASSWORD=password');
        $_ENV['ADMIN_EMAIL'] = 'admin@example.com';
        $_ENV['ADMIN_PASSWORD'] = 'password';
        $_SERVER['ADMIN_EMAIL'] = 'admin@example.com';
        $_SERVER['ADMIN_PASSWORD'] = 'password';

        $this->expectException(RuntimeException::class);

        (new DatabaseSeeder)->run();
    }
}
