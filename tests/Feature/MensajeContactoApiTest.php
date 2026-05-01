<?php

namespace Tests\Feature;

use App\Models\MensajeContacto;
use App\Models\User;
use App\Notifications\NuevoMensajeContactoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MensajeContactoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_submit_contact_message(): void
    {
        $this->postJson('/api/contactos', $this->contactPayload())
            ->assertCreated()
            ->assertJsonPath('mensaje', 'Mensaje recibido')
            ->assertJsonPath('data.estado', 'nuevo')
            ->assertJsonPath('data.leido', false);

        $this->assertDatabaseHas('mensajes_contacto', [
            'email' => 'persona@example.com',
            'asunto' => 'Necesito informacion',
        ]);
    }

    public function test_contact_form_is_rate_limited(): void
    {
        foreach (range(1, 3) as $attempt) {
            $this->postJson('/api/contactos', $this->contactPayload([
                'email' => "persona{$attempt}@example.com",
            ]))->assertCreated();
        }

        $this->postJson('/api/contactos', $this->contactPayload([
            'email' => 'persona4@example.com',
        ]))->assertTooManyRequests();
    }

    public function test_contact_message_sends_optional_notification(): void
    {
        Notification::fake();
        config(['mail.contact_notification_email' => 'pastor@example.com']);

        $this->postJson('/api/contactos', $this->contactPayload())
            ->assertCreated();

        Notification::assertSentOnDemand(NuevoMensajeContactoNotification::class);
    }

    public function test_contact_index_requires_authentication(): void
    {
        $this->getJson('/api/contactos')->assertUnauthorized();
    }

    public function test_pastor_can_list_and_view_contact_messages(): void
    {
        $token = User::factory()->create(['role' => 'pastor'])->createToken('vue-admin')->plainTextToken;
        $mensaje = MensajeContacto::create($this->contactPayload([
            'asunto' => 'Quiero visitar la iglesia',
        ]));

        $this->withToken($token)
            ->getJson('/api/contactos')
            ->assertOk()
            ->assertJsonFragment(['asunto' => 'Quiero visitar la iglesia']);

        $this->withToken($token)
            ->getJson("/api/contactos/{$mensaje->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $mensaje->id]);
    }

    public function test_regular_user_cannot_manage_contact_messages(): void
    {
        $editorToken = User::factory()->create(['role' => 'editor'])->createToken('vue-admin')->plainTextToken;
        $token = User::factory()->create(['role' => 'usuario'])->createToken('vue-admin')->plainTextToken;
        $mensaje = MensajeContacto::create($this->contactPayload());

        $this->withToken($editorToken)
            ->getJson('/api/contactos')
            ->assertForbidden();

        $this->withToken($token)
            ->getJson('/api/contactos')
            ->assertForbidden();

        $this->withToken($token)
            ->patchJson("/api/contactos/{$mensaje->id}", ['estado' => 'leido'])
            ->assertForbidden();
    }

    public function test_admin_can_mark_message_as_read_and_responded(): void
    {
        $token = User::factory()->create(['role' => 'admin'])->createToken('vue-admin')->plainTextToken;
        $mensaje = MensajeContacto::create($this->contactPayload());

        $this->withToken($token)
            ->patchJson("/api/contactos/{$mensaje->id}", [
                'estado' => 'respondido',
                'notas_internas' => 'Se llamo a la persona.',
            ])
            ->assertOk()
            ->assertJsonPath('estado', 'respondido')
            ->assertJsonPath('notas_internas', 'Se llamo a la persona.')
            ->assertJsonPath('respondido_en', fn ($value) => $value !== null);
    }

    public function test_contact_messages_can_be_filtered_and_searched(): void
    {
        MensajeContacto::create($this->contactPayload([
            'nombre' => 'Maria Lopez',
            'email' => 'maria@example.com',
            'asunto' => 'Oracion familiar',
            'mensaje' => 'Necesito oracion por mi familia.',
            'estado' => 'nuevo',
            'leido' => false,
        ]));

        MensajeContacto::create($this->contactPayload([
            'nombre' => 'Carlos Perez',
            'email' => 'carlos@example.com',
            'asunto' => 'Pregunta general',
            'mensaje' => 'Pregunta sobre horarios.',
            'estado' => 'respondido',
            'leido' => true,
        ]));

        $token = User::factory()->create(['role' => 'pastor'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/contactos?search=familia&estado=nuevo&leido=false')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.email', 'maria@example.com');
    }

    public function test_contact_messages_are_paginated(): void
    {
        foreach (range(1, 3) as $number) {
            MensajeContacto::create($this->contactPayload([
                'email' => "persona{$number}@example.com",
                'asunto' => "Mensaje {$number}",
            ]));
        }

        $token = User::factory()->create(['role' => 'admin'])->createToken('vue-admin')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/contactos?per_page=2&page=2')
            ->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonPath('per_page', 2)
            ->assertJsonPath('total', 3)
            ->assertJsonCount(1, 'data');
    }

    private function contactPayload(array $overrides = []): array
    {
        return array_merge([
            'nombre' => 'Persona Visitante',
            'email' => 'persona@example.com',
            'telefono' => '555-123-4567',
            'asunto' => 'Necesito informacion',
            'mensaje' => 'Me gustaria recibir mas informacion sobre la iglesia.',
        ], $overrides);
    }
}
