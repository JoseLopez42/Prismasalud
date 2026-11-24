<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('licencias')) {
            Schema::create('licencias', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('usuario_cedula')->nullable()->index();
                $table->string('nombre_ips', 150);
                $table->string('nit', 30);
                $table->string('tipo_entidad', 20)->nullable();
                $table->string('representante_legal', 150);
                $table->string('correo_ips', 150);
                $table->string('telefono_ips', 20);
                $table->string('departamento', 80)->nullable();
                $table->string('ciudad', 80)->nullable();
                $table->string('direccion', 200)->nullable();
                $table->string('contacto_nombre', 150)->nullable();
                $table->string('contacto_email', 150)->nullable();
                $table->string('contacto_telefono', 20)->nullable();
                $table->string('plan', 50)->nullable();
                $table->unsignedBigInteger('plan_id')->nullable();
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_fin')->nullable();
                $table->string('estado', 20)->default('activa');
                $table->text('notas')->nullable();
                $table->timestamps();
            });
            return;
        }

        // Tabla existente: garantizar columna usuario_cedula y sus índices (relación con el administrador).
        if (! Schema::hasColumn('licencias', 'usuario_cedula')) {
            Schema::table('licencias', function (Blueprint $table) {
                $table->unsignedInteger('usuario_cedula')->nullable()->index()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('licencias', 'usuario_cedula')) {
            Schema::table('licencias', function (Blueprint $table) {
                $table->dropColumn('usuario_cedula');
            });
        }
    }
};
