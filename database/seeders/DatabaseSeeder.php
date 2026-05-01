<?php

namespace Database\Seeders;

use App\Models\Evento;
use App\Models\Ministerio;
use App\Models\Misione;
use App\Models\Noticia;
use App\Models\PaginaInstitucional;
use App\Models\Recurso;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');

        if (! $adminEmail || ! $adminPassword || $adminPassword === 'password') {
            throw new RuntimeException('Configura ADMIN_EMAIL y ADMIN_PASSWORD con una contraseña segura antes de ejecutar el seeder.');
        }

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
            ]
        );

        $this->seedPaginas();
        $this->seedMinisteriosYNoticias();
        $this->seedEventos();
        $this->seedRecursos();
        $this->seedMisiones();
    }

    private function seedPaginas(): void
    {
        collect([
            [
                'titulo' => 'Sobre nosotros',
                'slug' => 'sobre-nosotros',
                'contenido' => 'Somos una iglesia local dedicada a servir a Dios, acompañar familias y compartir esperanza con nuestra comunidad.',
                'resumen' => 'Conoce la historia y el corazón de nuestra iglesia.',
                'seccion' => 'iglesia',
                'orden' => 1,
                'meta_title' => 'Sobre nosotros',
                'meta_description' => 'Conoce nuestra iglesia, historia y comunidad.',
            ],
            [
                'titulo' => 'Nuestra misión',
                'slug' => 'nuestra-mision',
                'contenido' => 'Nuestra misión es amar a Dios, servir a las personas y formar discípulos comprometidos con la fe y la comunidad.',
                'resumen' => 'Amar, servir y formar discípulos.',
                'seccion' => 'iglesia',
                'orden' => 2,
                'meta_title' => 'Nuestra misión',
                'meta_description' => 'La misión que guía nuestra iglesia.',
            ],
            [
                'titulo' => 'Horarios',
                'slug' => 'horarios',
                'contenido' => 'Domingos: culto principal a las 10:00 AM. Miércoles: oración y estudio bíblico a las 7:30 PM.',
                'resumen' => 'Nuestros horarios semanales de reunión.',
                'seccion' => 'visita',
                'orden' => 3,
                'meta_title' => 'Horarios',
                'meta_description' => 'Horarios de cultos y reuniones.',
            ],
        ])->each(fn (array $pagina) => PaginaInstitucional::updateOrCreate(
            ['slug' => $pagina['slug']],
            $pagina + ['activo' => true]
        ));
    }

    private function seedMinisteriosYNoticias(): void
    {
        $jovenes = Ministerio::updateOrCreate(
            ['slug' => 'jovenes'],
            [
                'nombre' => 'Jóvenes',
                'descripcion' => 'Un espacio para que jóvenes crezcan en fe, amistad y propósito.',
                'categoria' => 'Jóvenes',
                'orden' => 1,
                'activo' => true,
            ]
        );

        $familias = Ministerio::updateOrCreate(
            ['slug' => 'familias'],
            [
                'nombre' => 'Familias',
                'descripcion' => 'Acompañamos a matrimonios, padres e hijos en su caminar con Dios.',
                'categoria' => 'Familias',
                'orden' => 2,
                'activo' => true,
            ]
        );

        Noticia::updateOrCreate(
            ['slug' => 'bienvenida-a-nuestra-comunidad'],
            [
                'titulo' => 'Bienvenida a nuestra comunidad',
                'resumen' => 'Una invitación para conocer nuestra iglesia y participar en la vida comunitaria.',
                'contenido' => 'Te invitamos a ser parte de nuestros cultos, grupos y actividades de servicio.',
                'estado' => 'publicado',
                'destacada' => true,
                'categoria' => 'Comunidad',
                'ministerio_id' => $familias->id,
                'publicado_en' => now(),
            ]
        );

        Noticia::updateOrCreate(
            ['slug' => 'noche-de-jovenes'],
            [
                'titulo' => 'Noche de jóvenes',
                'resumen' => 'Una noche especial con adoración, palabra y compañerismo.',
                'contenido' => 'El ministerio de jóvenes tendrá una reunión especial este mes. Todos son bienvenidos.',
                'estado' => 'publicado',
                'destacada' => false,
                'categoria' => 'Jóvenes',
                'ministerio_id' => $jovenes->id,
                'publicado_en' => now()->subDay(),
            ]
        );
    }

    private function seedEventos(): void
    {
        collect([
            [
                'titulo' => 'Culto familiar',
                'slug' => 'culto-familiar',
                'descripcion' => 'Un servicio especial para adorar juntos como familia.',
                'resumen' => 'Culto especial para toda la familia.',
                'categoria' => 'Culto',
                'fecha_inicio' => now()->next('Sunday')->setTime(10, 0),
                'fecha_fin' => now()->next('Sunday')->setTime(12, 0),
                'lugar' => 'Templo principal',
                'ciudad' => 'Miami',
                'estado' => 'programado',
                'destacado' => true,
            ],
            [
                'titulo' => 'Estudio bíblico',
                'slug' => 'estudio-biblico',
                'descripcion' => 'Una noche de enseñanza, oración y conversación bíblica.',
                'resumen' => 'Estudio bíblico semanal.',
                'categoria' => 'Enseñanza',
                'fecha_inicio' => now()->next('Wednesday')->setTime(19, 30),
                'fecha_fin' => now()->next('Wednesday')->setTime(21, 0),
                'lugar' => 'Salón de reuniones',
                'ciudad' => 'Miami',
                'estado' => 'programado',
                'destacado' => false,
            ],
        ])->each(fn (array $evento) => Evento::updateOrCreate(
            ['slug' => $evento['slug']],
            $evento + ['activo' => true]
        ));
    }

    private function seedRecursos(): void
    {
        collect([
            [
                'nombre' => 'Guía para nuevos visitantes',
                'slug' => 'guia-para-nuevos-visitantes',
                'informacion' => 'Información básica para quienes visitan la iglesia por primera vez.',
                'categoria' => 'Visitantes',
                'tipo' => 'Guía',
                'destacado' => true,
                'orden' => 1,
            ],
            [
                'nombre' => 'Devocional semanal',
                'slug' => 'devocional-semanal',
                'informacion' => 'Lecturas y reflexiones para acompañar la semana.',
                'categoria' => 'Devocionales',
                'tipo' => 'PDF',
                'destacado' => false,
                'orden' => 2,
            ],
        ])->each(fn (array $recurso) => Recurso::updateOrCreate(
            ['slug' => $recurso['slug']],
            $recurso + ['activo' => true, 'descargable' => false]
        ));
    }

    private function seedMisiones(): void
    {
        Misione::updateOrCreate(
            ['slug' => 'servicio-comunitario'],
            [
                'nombre' => 'Servicio comunitario',
                'informacion' => 'Iniciativas de ayuda práctica y acompañamiento para nuestra comunidad.',
                'categoria' => 'Local',
                'orden' => 1,
                'activo' => true,
            ]
        );
    }
}
