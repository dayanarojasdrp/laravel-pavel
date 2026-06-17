<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action')->index();
            $table->string('auditable_type')->index();
            $table->unsignedBigInteger('auditable_id')->index();
            $table->string('auditable_label')->nullable()->index();
            $table->json('before_values')->nullable();
            $table->json('after_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::table('ministerios', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('noticias', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('misiones', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('recursos', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('eventos', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('mensajes_contacto', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('paginas_institucionales', fn (Blueprint $table) => $table->softDeletes());
    }

    public function down(): void
    {
        Schema::table('paginas_institucionales', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('mensajes_contacto', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('eventos', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('recursos', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('misiones', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('noticias', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('ministerios', fn (Blueprint $table) => $table->dropSoftDeletes());

        Schema::dropIfExists('audit_logs');
    }
};
