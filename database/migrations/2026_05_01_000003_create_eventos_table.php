<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('descripcion');
            $table->text('resumen')->nullable();
            $table->string('imagen')->nullable();
            $table->string('categoria')->nullable()->index();
            $table->timestamp('fecha_inicio')->index();
            $table->timestamp('fecha_fin')->nullable()->index();
            $table->string('lugar')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable()->index();
            $table->string('estado')->default('programado')->index();
            $table->boolean('destacado')->default(false)->index();
            $table->boolean('activo')->default(true)->index();
            $table->string('registro_url')->nullable();
            $table->unsignedInteger('capacidad')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
