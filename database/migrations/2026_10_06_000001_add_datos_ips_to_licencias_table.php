<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licencias', function (Blueprint $table) {
            $table->string('tipo_entidad', 30)->nullable()->after('nit');
            $table->string('representante_legal', 150)->nullable()->after('tipo_entidad');
            $table->string('correo_ips', 150)->nullable()->after('representante_legal');
            $table->string('telefono_ips', 20)->nullable()->after('correo_ips');
            $table->string('departamento', 80)->nullable()->after('telefono_ips');
            $table->string('ciudad', 80)->nullable()->after('departamento');
            $table->string('direccion', 200)->nullable()->after('ciudad');
        });
    }

    public function down(): void
    {
        Schema::table('licencias', function (Blueprint $table) {
            $table->dropColumn(['tipo_entidad', 'representante_legal', 'correo_ips', 'telefono_ips', 'departamento', 'ciudad', 'direccion']);
        });
    }
};