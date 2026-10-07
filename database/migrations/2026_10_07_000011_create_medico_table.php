<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "medico".
     * IMPORTANTE: la llave primaria (PK) NO es un id autoincremental,
     * sino la CÉDULA del médico como número entero (INT), sin autoincrementar
     * (mismo criterio que la tabla "usuarios").
     *
     * Aquí vive la CUENTA de acceso del médico (correo + contraseña),
     * que se crea desde la sección de Médicos del panel de administración,
     * no desde la compra de licencias como los clientes.
     */
    public function up(): void
    {
        Schema::create('medico', function (Blueprint $table) {
            // Cédula = PK de la tabla, entero sin autoincrementar.
            $table->unsignedInteger('cedula')
                  ->primary()
                  ->comment('Cédula del médico (PK, no autoincremental)');

            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('foto')->nullable();
            $table->string('correo', 150)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('direccion', 200)->nullable();

            // Guardar SIEMPRE encriptada, nunca en texto plano.
            $table->string('contrasena', 255);

            // La cuenta se puede deshabilitar sin borrar al médico.
            $table->string('estado', 20)->default('activo')
                  ->comment('activo | inactivo');

            // FK hacia especialidad: cada médico tiene UNA especialidad,
            // pero una especialidad puede tener MUCHOS médicos (1 a muchos).
            $table->unsignedBigInteger('id_especialidad');

            $table->timestamps();

            $table->foreign('id_especialidad')
                  ->references('id')->on('especialidad')
                  ->onDelete('restrict')   // no deja borrar una especialidad si tiene médicos asociados
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medico');
    }
};
