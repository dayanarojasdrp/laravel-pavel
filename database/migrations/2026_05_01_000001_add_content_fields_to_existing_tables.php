<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ministerios', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('nombre');
            $table->text('descripcion')->nullable()->after('slug');
            $table->string('imagen')->nullable()->after('descripcion');
            $table->string('categoria')->nullable()->index()->after('imagen');
            $table->unsignedInteger('orden')->default(0)->index()->after('categoria');
            $table->boolean('activo')->default(true)->index()->after('orden');
            $table->string('url_externa')->nullable()->after('activo');
        });

        Schema::table('noticias', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('titulo');
            $table->text('resumen')->nullable()->after('slug');
            $table->string('autor')->nullable()->after('imagen');
            $table->timestamp('publicado_en')->nullable()->index()->after('autor');
            $table->string('estado')->default('borrador')->index()->after('publicado_en');
            $table->boolean('destacada')->default(false)->index()->after('estado');
            $table->string('categoria')->nullable()->index()->after('destacada');
            $table->string('meta_title')->nullable()->after('categoria');
            $table->string('meta_description')->nullable()->after('meta_title');
        });

        Schema::table('misiones', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('nombre');
            $table->string('categoria')->nullable()->index()->after('imagen');
            $table->unsignedInteger('orden')->default(0)->index()->after('categoria');
            $table->boolean('activo')->default(true)->index()->after('orden');
            $table->string('url_externa')->nullable()->after('activo');
        });

        Schema::table('recursos', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('nombre');
            $table->string('categoria')->nullable()->index()->after('imagen');
            $table->string('tipo')->nullable()->index()->after('categoria');
            $table->string('archivo_url')->nullable()->after('tipo');
            $table->string('link')->nullable()->after('archivo_url');
            $table->boolean('descargable')->default(false)->index()->after('link');
            $table->boolean('destacado')->default(false)->index()->after('descargable');
            $table->unsignedInteger('orden')->default(0)->index()->after('destacado');
            $table->boolean('activo')->default(true)->index()->after('orden');
        });

        $this->backfillSlugs('ministerios', 'nombre');
        $this->backfillSlugs('noticias', 'titulo');
        $this->backfillSlugs('misiones', 'nombre');
        $this->backfillSlugs('recursos', 'nombre');
    }

    public function down(): void
    {
        Schema::table('recursos', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'categoria',
                'tipo',
                'archivo_url',
                'link',
                'descargable',
                'destacado',
                'orden',
                'activo',
            ]);
        });

        Schema::table('misiones', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'categoria',
                'orden',
                'activo',
                'url_externa',
            ]);
        });

        Schema::table('noticias', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'resumen',
                'autor',
                'publicado_en',
                'estado',
                'destacada',
                'categoria',
                'meta_title',
                'meta_description',
            ]);
        });

        Schema::table('ministerios', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'descripcion',
                'imagen',
                'categoria',
                'orden',
                'activo',
                'url_externa',
            ]);
        });
    }

    private function backfillSlugs(string $table, string $sourceColumn): void
    {
        $usedSlugs = [];

        DB::table($table)
            ->select(['id', $sourceColumn])
            ->orderBy('id')
            ->get()
            ->each(function ($record) use ($table, $sourceColumn, &$usedSlugs) {
                $baseSlug = Str::slug($record->{$sourceColumn}) ?: 'item';
                $slug = $baseSlug;
                $counter = 2;

                while (in_array($slug, $usedSlugs, true)) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $usedSlugs[] = $slug;

                DB::table($table)
                    ->where('id', $record->id)
                    ->update(['slug' => $slug]);
            });
    }
};
