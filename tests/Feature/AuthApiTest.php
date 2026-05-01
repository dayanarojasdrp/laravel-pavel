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

        $token = User::factory()->create()->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonStructure(['id', 'name', 'email']);
    }

    public function test_logout_revokes_current_bearer_token(): void
    {
        $user = User::factory()->create();
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
