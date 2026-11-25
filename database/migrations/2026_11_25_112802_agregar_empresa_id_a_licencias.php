<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| DBA-11 (B): cada licencia pertenece a una empresa (licencias.empresa_id).
|
| Antes la relación estaba al revés (empresas.licencia_id), pero una empresa
| compra muchas licencias en el tiempo: 1 empresa -> N licencias.
|
| Requiere que ya se haya ejecutado DBA-11 (A): empresas.nit es único y
| todo NIT de licencias tiene su empresa.
|
| Ejecutar SOLO esta migración, con la conexión de migraciones:
|   php artisan migrate --database=migraciones --path=database/migrations/2026_11_25_112802_agregar_empresa_id_a_licencias.php
*/
return new class extends Migration
{
    public function up(): void
    {
        // 0. Verificar ANTES de cambiar nada: toda licencia debe tener una empresa con su NIT.
        //    Si no, se detiene con un mensaje claro y la base queda intacta.
        $sinEmpresa = DB::table('licencias as l')
            ->leftJoin('empresas as e', 'e.nit', '=', 'l.nit')
            ->whereNull('e.id')
            ->pluck('l.id');

        if ($sinEmpresa->isNotEmpty()) {
            throw new RuntimeException(
                'DBA-11 (B): estas licencias no tienen empresa con su NIT: ' . $sinEmpresa->implode(', ')
                . '. Ejecute primero DBA-11 (A).'
            );
        }

        // 1. Columna nueva, vacía al principio para poder llenarla.
        Schema::table('licencias', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_id')->nullable()->after('id')
                  ->comment('Empresa (IPS/EPS) dueña de la licencia');
        });

        // 2. Llenarla cruzando por NIT (ya es único en empresas, así que el cruce es exacto).
        DB::statement('UPDATE licencias l JOIN empresas e ON e.nit = l.nit SET l.empresa_id = e.id');

        // 3. Obligatoria + llave foránea.
        //    RESTRICT: no se puede borrar una empresa que tiene licencias.
        //    CASCADE en update: si cambia el id de la empresa, las licencias lo siguen.
        Schema::table('licencias', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_id')->nullable(false)
                  ->comment('Empresa (IPS/EPS) dueña de la licencia')->change();

            $table->foreign('empresa_id', 'licencias_empresa_id_foreign')
                  ->references('id')->on('empresas')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        // Primero la llave foránea (depende de la columna), luego la columna.
        Schema::table('licencias', function (Blueprint $table) {
            $table->dropForeign('licencias_empresa_id_foreign');
            $table->dropColumn('empresa_id');
        });
    }
};