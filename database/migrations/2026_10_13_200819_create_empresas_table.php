<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('empresas')) {
            return;
        }

        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('usuario_cedula')->nullable()->index()
                  ->comment('Administrador (usuario) dueño de la empresa/IPS');
            $table->unsignedBigInteger('licencia_id')->nullable()->index()
                  ->comment('Licencia comprada (plan activo de la empresa)');
            $table->string('nit', 30);
            $table->string('nombre', 150);
            $table->string('tipo_entidad', 20)->nullable();
            $table->string('representante_legal', 150)->nullable();
            $table->string('correo', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('departamento', 80)->nullable();
            $table->string('ciudad', 80)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('estado', 20)->default('activa');
            $table->timestamps();

            // Relaciones lógicas (sin FK estricta para no romper con datos existentes)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
