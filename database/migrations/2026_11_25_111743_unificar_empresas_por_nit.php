<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| DBA-11 (A): una sola fila por empresa en `empresas` (NIT único).
|
| Problema: CompraController creaba una empresa NUEVA en cada compra o
| renovación, por eso un mismo NIT tenía varias filas (clinica diego: 9).
|
| Qué hace:
|   1. Respaldo: toda fila que se crea, cambia o borra queda copiada en
|      empresas_respaldo_dba11 para que down() pueda deshacer todo.
|   2. Crea la empresa de los NIT que solo existen en `licencias`.
|   3. Por cada NIT deja la fila de id más bajo, con los datos de la licencia
|      vigente (la activa; si no hay activa, la más reciente). El nombre no se toca.
|   4. Agrega UNIQUE(nit).
|
| Ejecutar SOLO esta migración, con la conexión de migraciones:
|   php artisan migrate --database=migraciones --path=database/migrations/NOMBRE_DE_ESTE_ARCHIVO.php
*/
return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla de respaldo: misma estructura que empresas + qué se le hizo a la fila.
        DB::statement('CREATE TABLE empresas_respaldo_dba11 LIKE empresas');
        DB::statement("ALTER TABLE empresas_respaldo_dba11 ADD COLUMN accion_dba11 VARCHAR(20) NOT NULL COMMENT 'creada | actualizada | borrada'");

        DB::transaction(function () {
            $ahora = now();

            // Licencia vigente por NIT: primero las activas y, entre ellas, la más nueva.
            // unique('nit') se queda con la primera que aparece de cada NIT.
            $vigentes = DB::table('licencias')
                ->orderByRaw("estado = 'activa' DESC")
                ->orderByDesc('id')
                ->get()
                ->unique('nit')
                ->keyBy('nit');

            // 2. Crear la empresa de los NIT que solo están en licencias.
            foreach ($vigentes as $nit => $lic) {
                if (DB::table('empresas')->where('nit', $nit)->exists()) {
                    continue;
                }

                $id = DB::table('empresas')->insertGetId([
                    'usuario_cedula'      => $lic->usuario_cedula,
                    'licencia_id'         => $lic->id,
                    'nit'                 => $lic->nit,
                    'nombre'              => $lic->nombre_ips,
                    'tipo_entidad'        => $lic->tipo_entidad,
                    'representante_legal' => $lic->representante_legal,
                    'correo'              => $lic->correo_ips,
                    'telefono'            => $lic->telefono_ips,
                    'departamento'        => $lic->departamento,
                    'ciudad'              => $lic->ciudad,
                    'direccion'           => $lic->direccion,
                    'plan'                => $lic->plan,
                    'fecha_compra'        => $lic->fecha_inicio,
                    'fecha_expiracion'    => $lic->fecha_fin,
                    'estado'              => $lic->estado,
                    'created_at'          => $ahora,
                    'updated_at'          => $ahora,
                ]);

                $fila = (array) DB::table('empresas')->find($id);
                DB::table('empresas_respaldo_dba11')->insert($fila + ['accion_dba11' => 'creada']);
            }

            // 3. Unificar: por cada NIT queda la fila de id más bajo.
            $nits = DB::table('empresas')->distinct()->pluck('nit');

            foreach ($nits as $nit) {
                $filas = DB::table('empresas')->where('nit', $nit)->orderBy('id')->get();
                $queda = $filas->first();
                $lic   = $vigentes[$nit] ?? null;

                // La fila que queda apunta a la licencia vigente (guardamos antes cómo estaba).
                if ($lic && (int) $queda->licencia_id !== (int) $lic->id) {
                    DB::table('empresas_respaldo_dba11')->insert((array) $queda + ['accion_dba11' => 'actualizada']);

                    DB::table('empresas')->where('id', $queda->id)->update([
                        'licencia_id'      => $lic->id,
                        'plan'             => $lic->plan,
                        'fecha_compra'     => $lic->fecha_inicio,
                        'fecha_expiracion' => $lic->fecha_fin,
                        'estado'           => $lic->estado,
                        'updated_at'       => $ahora,
                    ]);
                }

                // Las demás filas del mismo NIT van al respaldo y se borran.
                foreach ($filas->slice(1) as $sobra) {
                    DB::table('empresas_respaldo_dba11')->insert((array) $sobra + ['accion_dba11' => 'borrada']);
                    DB::table('empresas')->where('id', $sobra->id)->delete();
                }
            }
        });

        // 4. Desde ahora la base no acepta dos empresas con el mismo NIT.
        Schema::table('empresas', function (Blueprint $table) {
            $table->unique('nit', 'empresas_nit_unique');
        });
    }

    public function down(): void
    {
        // Se deshace en orden inverso: primero quitar el UNIQUE (si no, no caben los duplicados).
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropUnique('empresas_nit_unique');
        });

        DB::transaction(function () {
            foreach (DB::table('empresas_respaldo_dba11')->get() as $registro) {
                $fila   = (array) $registro;
                $accion = $fila['accion_dba11'];
                unset($fila['accion_dba11']);

                match ($accion) {
                    'creada'      => DB::table('empresas')->where('id', $fila['id'])->delete(),
                    'actualizada' => DB::table('empresas')->where('id', $fila['id'])->update($fila),
                    'borrada'     => DB::table('empresas')->insert($fila),
                };
            }
        });

        Schema::dropIfExists('empresas_respaldo_dba11');
    }
};