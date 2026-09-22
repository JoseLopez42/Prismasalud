<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('contrasena');
            $table->unsignedInteger('edad')->nullable()->after('apellidos');
            $table->string('telefono', 20)->nullable()->after('edad');

            // Sin FK todavía a ciudad/eps (se agregan cuando conectemos
            // esas tablas más adelante, si hace falta)
            $table->unsignedInteger('id_ciudad')->nullable()->after('telefono');
            $table->unsignedInteger('id_tipo_documento')->nullable()->after('id_ciudad');
            $table->unsignedInteger('nit_eps')->nullable()->after('id_tipo_documento');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['foto', 'edad', 'telefono', 'id_ciudad', 'id_tipo_documento', 'nit_eps']);
        });
    }
};