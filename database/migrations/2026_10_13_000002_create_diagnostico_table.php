<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "diagnostico": diagnósticos emitidos por el médico para
     * un paciente. Pueden colgar de una historia clínica concreta.
     */
    public function up(): void
    {
        Schema::create('diagnostico', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('cedula_paciente');
            $table->unsignedInteger('cedula_medico');

            // Historia clínica a la que pertenece (opcional)
            $table->unsignedBigInteger('id_historia_clinica')->nullable();

            $table->string('descripcion', 255);

            // Presuntivo / Confirmado / Definitivo
            $table->string('tipo', 50)->nullable();

            $table->date('fecha');

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->foreign('cedula_paciente')
                  ->references('cedula')->on('usuarios')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('cedula_medico')
                  ->references('cedula')->on('medico')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('id_historia_clinica')
                  ->references('id')->on('historia_clinica')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostico');
    }
};
