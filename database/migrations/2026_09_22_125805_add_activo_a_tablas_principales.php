<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega la columna "activo" a las tablas que antes se podían
     * "eliminar". Ahora, en vez de borrar el registro, se marca
     * como inactivo (activo = 0), y sigue existiendo en la BD.
     * Por defecto todo nace activo (true).
     */
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('nit_eps');
        });

        Schema::table('departamentos', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('nombre_departamento');
        });

        Schema::table('ciudades', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('id_departamento');
        });

        Schema::table('sedes', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('id_ciudad');
        });

        Schema::table('tipo_documento', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('tipo_documento');
        });

        Schema::table('tipos_usuario', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', fn (Blueprint $table) => $table->dropColumn('activo'));
        Schema::table('departamentos', fn (Blueprint $table) => $table->dropColumn('activo'));
        Schema::table('ciudades', fn (Blueprint $table) => $table->dropColumn('activo'));
        Schema::table('sedes', fn (Blueprint $table) => $table->dropColumn('activo'));
        Schema::table('tipo_documento', fn (Blueprint $table) => $table->dropColumn('activo'));
        Schema::table('tipos_usuario', fn (Blueprint $table) => $table->dropColumn('activo'));
    }
};