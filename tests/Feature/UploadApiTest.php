<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_requires_authentication(): void
    {
        Storage::fake('public');

        $this->postJson('/api/uploads/imagenes', [
            'imagen' => UploadedFile::fake()->image('evento.jpg'),
        ])->assertUnauthorized();
    }

    public function test_configured_admin_can_upload_image(): void
    {
        Storage::fake('public');
        $token = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin'])->createToken('vue-admin')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/uploads/imagenes', [
                'imagen' => UploadedFile::fake()->image('evento.jpg', 1200, 800),
                'carpeta' => 'Eventos Especiales',
            ])
            ->assertCreated()
            ->assertJsonStructure(['path', 'url']);

        $path = $response->json('path');

        $this->assertStringStartsWith('uploads/eventos-especiales/', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_regular_user_cannot_upload_image(): void
    {
        Storage::fake('public');
        $token = User::factory()->create(['role' => 'usuario'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/uploads/imagenes', [
                'imagen' => UploadedFile::fake()->image('evento.jpg'),
            ])
            ->assertForbidden();
    }

    public function test_upload_rejects_non_images(): void
    {
        Storage::fake('public');
        $token = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/uploads/imagenes', [
                'imagen' => UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf'),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['imagen']);
    }

    public function test_upload_rejects_images_larger_than_five_mb(): void
    {
        Storage::fake('public');
        $token = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/uploads/imagenes', [
                'imagen' => UploadedFile::fake()->image('grande.jpg')->size(5121),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['imagen']);
    }
}
