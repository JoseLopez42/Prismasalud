<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "usuarios".
     * IMPORTANTE: la llave primaria (PK) NO es un id autoincremental
     * normal, sino la CÉDULA del usuario, como número entero (INT),
     * SIN autoincrementar (por eso no usamos $table->id() aquí).
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            // Cédula = PK de la tabla, entero sin autoincrementar.
            // unsignedInteger -> INT UNSIGNED en MySQL (equivalente a INT(11) sin signo).
            $table->unsignedInteger('cedula')
                  ->primary()
                  ->comment('Cédula del usuario (PK, no autoincremental)');

            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('correo', 150)->unique();
            $table->string('contrasena', 255); // guardar siempre encriptada, nunca en texto plano

            // FK hacia tipos_usuario: cada usuario tiene UN tipo,
            // pero un tipo puede tener MUCHOS usuarios (1 a muchos).
            $table->unsignedBigInteger('tipo_usuario_id');

            $table->timestamps();

            // Relación (FK) con la tabla tipos_usuario
            $table->foreign('tipo_usuario_id')
                  ->references('id')->on('tipos_usuario')
                  ->onDelete('restrict')   // no deja borrar un tipo si tiene usuarios asociados
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};