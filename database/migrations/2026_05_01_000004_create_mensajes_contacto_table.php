<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes_contacto', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->index();
            $table->string('telefono')->nullable();
            $table->string('asunto');
            $table->text('mensaje');
            $table->string('estado')->default('nuevo')->index();
            $table->boolean('leido')->default(false)->index();
            $table->timestamp('respondido_en')->nullable()->index();
            $table->text('notas_internas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes_contacto');
    }
};
