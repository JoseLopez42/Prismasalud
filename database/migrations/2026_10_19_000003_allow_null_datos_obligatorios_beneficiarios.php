<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * El módulo de Usuarios permite crear un paciente sin edad, sin teléfono
     * y sin fecha de nacimiento (esos datos se piden en la afiliación).
     * Por eso esas columnas de "beneficiarios" no pueden ser obligatorias:
     * sin esto, guardar un paciente desde ese módulo fallaba con un error 500.
     */
    public function up(): void
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->unsignedInteger('edad')->nullable()->change();
            $table->string('telefono', 20)->nullable()->change();
            $table->date('fecha_nacimiento')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->unsignedInteger('edad')->nullable(false)->change();
            $table->string('telefono', 20)->nullable(false)->change();
            $table->date('fecha_nacimiento')->nullable(false)->change();
        });
    }
};
