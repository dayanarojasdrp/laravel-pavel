<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paginas_institucionales', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('contenido');
            $table->text('resumen')->nullable();
            $table->string('imagen')->nullable();
            $table->string('seccion')->nullable()->index();
            $table->unsignedInteger('orden')->default(0)->index();
            $table->boolean('activo')->default(true)->index();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paginas_institucionales');
    }
};
