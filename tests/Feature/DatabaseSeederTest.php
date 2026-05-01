<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\Ministerio;
use App\Models\Misione;
use App\Models\Noticia;
use App\Models\PaginaInstitucional;
use App\Models\Recurso;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_admin_and_initial_content(): void
    {
        putenv('ADMIN_EMAIL=admin@example.com');
        putenv('ADMIN_PASSWORD=AdminPassword1');
        $_ENV['ADMIN_EMAIL'] = 'admin@example.com';
        $_ENV['ADMIN_PASSWORD'] = 'AdminPassword1';
        $_SERVER['ADMIN_EMAIL'] = 'admin@example.com';
        $_SERVER['ADMIN_PASSWORD'] = 'AdminPassword1';

        (new DatabaseSeeder)->run();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $this->assertGreaterThanOrEqual(1, User::count());
        $this->assertGreaterThanOrEqual(2, Ministerio::count());
        $this->assertGreaterThanOrEqual(2, Noticia::count());
        $this->assertGreaterThanOrEqual(2, Evento::count());
        $this->assertGreaterThanOrEqual(2, Recurso::count());
        $this->assertGreaterThanOrEqual(1, Misione::count());
        $this->assertGreaterThanOrEqual(3, PaginaInstitucional::count());
    }
}
