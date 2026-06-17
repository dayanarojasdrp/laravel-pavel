<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Ministerio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraceabilityApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_changes_are_recorded_in_audit_logs(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
        $token = $admin->createToken('vue-admin')->plainTextToken;

        $createResponse = $this->withToken($token)
            ->postJson('/api/ministerios', [
                'nombre' => 'Jovenes',
                'descripcion' => 'Ministerio inicial.',
            ])
            ->assertCreated();

        $ministerioId = $createResponse->json('id');

        $this->withToken($token)
            ->patchJson("/api/ministerios/{$ministerioId}", [
                'descripcion' => 'Ministerio actualizado.',
            ])
            ->assertOk();

        $this->withToken($token)
            ->deleteJson("/api/ministerios/{$ministerioId}")
            ->assertOk();

        $this->assertSoftDeleted('ministerios', ['id' => $ministerioId]);
        $this->assertDatabaseCount('audit_logs', 3);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'created',
            'auditable_type' => Ministerio::class,
            'auditable_id' => $ministerioId,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'updated',
            'auditable_type' => Ministerio::class,
            'auditable_id' => $ministerioId,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'deleted',
            'auditable_type' => Ministerio::class,
            'auditable_id' => $ministerioId,
        ]);
    }

    public function test_public_indexes_do_not_include_soft_deleted_content(): void
    {
        Ministerio::create([
            'nombre' => 'Jovenes',
            'slug' => 'jovenes',
        ])->delete();

        $this->getJson('/api/ministerios')
            ->assertOk()
            ->assertJsonPath('total', 0);
    }

    public function test_configured_admin_can_read_traceability(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
        $token = $admin->createToken('vue-admin')->plainTextToken;

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'updated',
            'auditable_type' => Ministerio::class,
            'auditable_id' => 123,
            'auditable_label' => 'Jovenes',
            'before_values' => ['nombre' => 'Jovenes'],
            'after_values' => ['nombre' => 'Jovenes Renovados'],
        ]);

        $this->withToken($token)
            ->getJson('/api/trazabilidad?search=Jovenes')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.action', 'updated');
    }

    public function test_non_configured_admin_cannot_read_traceability(): void
    {
        $token = User::factory()->create(['role' => 'admin'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/trazabilidad')
            ->assertForbidden();
    }
}
