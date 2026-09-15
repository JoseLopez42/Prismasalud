<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "tipos_usuario": guarda los roles que puede tener un
     * usuario en el sistema (ej: Administrador, Paciente, Médico).
     * La tabla "usuarios" se relaciona con esta mediante una FK.
     */
    public function up(): void
    {
        Schema::create('tipos_usuario', function (Blueprint $table) {
            // id autoincremental normal: es la PK de esta tabla
            $table->id();

            // Nombre del tipo/rol (Administrador, Paciente, etc.)
            $table->string('nombre', 50);

            // created_at / updated_at automáticos
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_usuario');
    }
};