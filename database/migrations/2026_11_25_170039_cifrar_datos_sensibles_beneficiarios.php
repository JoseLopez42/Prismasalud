<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| DBA-03 (parte 3): cifrado de datos sensibles en `beneficiarios`.
| Ley 1581 de 2012: origen étnico, datos de salud y víctima del conflicto
| son datos sensibles; teléfono y dirección son datos personales de contacto.
|
| 1. Las 8 columnas pasan a TEXT (el texto cifrado mide ~200 caracteres o más).
| 2. Los datos que ya existían se cifran con APP_KEY (el mismo cifrado que
|    usan los casts del modelo Beneficiario), dentro de una transacción.
|
| IMPORTANTE: los datos quedan cifrados con el APP_KEY del .env con el que
| se ejecute esta migración. Si ese APP_KEY se pierde, no se pueden recuperar.
|
| Ejecutar SOLO esta migración, con la conexión de migraciones:
|   php artisan migrate --database=migraciones --path=database/migrations/NOMBRE_DE_ESTE_ARCHIVO.php
*/
return new class extends Migration
{
    // Columnas que quedan cifradas (las mismas que tienen cast en el modelo)
    private array $columnas = [
        'grupo_etnico',
        'tipo_sangre',
        'tipo_discapacidad',
        'telefono',
        'telefono_fijo',
        'direccion',
        'tiene_discapacidad',        // sí/no: se guarda '1' o '0' cifrado
        'victima_conflicto_armado',  // sí/no: se guarda '1' o '0' cifrado
    ];

    public function up(): void
    {
        // 1. Columnas a TEXT. direccion sigue siendo obligatoria, como antes.
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->text('grupo_etnico')->nullable()->change();
            $table->text('tipo_sangre')->nullable()->change();
            $table->text('tipo_discapacidad')->nullable()->change();
            $table->text('telefono')->nullable()->change();
            $table->text('telefono_fijo')->nullable()->change();
            $table->text('direccion')->change();
            $table->text('tiene_discapacidad')->nullable()->change();
            $table->text('victima_conflicto_armado')->nullable()->change();
        });

        // 2. Cifrar lo que ya existe. Si algo falla, la transacción deshace todo:
        //    nunca quedan unas filas cifradas y otras no.
        DB::transaction(function () {
            foreach (DB::table('beneficiarios')->get() as $fila) {
                $cambios = [];

                foreach ($this->columnas as $columna) {
                    if ($fila->$columna !== null) {
                        $cambios[$columna] = Crypt::encryptString((string) $fila->$columna);
                    }
                }

                if ($cambios) {
                    DB::table('beneficiarios')->where('cc_afiliado', $fila->cc_afiliado)->update($cambios);
                }
            }
        });
    }

    public function down(): void
    {
        // 1. Descifrar (en orden inverso a up(): primero los datos, luego los tipos).
        DB::transaction(function () {
            foreach (DB::table('beneficiarios')->get() as $fila) {
                $cambios = [];

                foreach ($this->columnas as $columna) {
                    if ($fila->$columna !== null) {
                        $cambios[$columna] = Crypt::decryptString($fila->$columna);
                    }
                }

                if ($cambios) {
                    DB::table('beneficiarios')->where('cc_afiliado', $fila->cc_afiliado)->update($cambios);
                }
            }

            // Los sí/no vuelven a ser obligatorios: los vacíos se dejan en 0 (No).
            DB::table('beneficiarios')->whereNull('tiene_discapacidad')->update(['tiene_discapacidad' => '0']);
            DB::table('beneficiarios')->whereNull('victima_conflicto_armado')->update(['victima_conflicto_armado' => '0']);
        });

        // 2. Volver a los tipos originales.
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->string('grupo_etnico', 50)->nullable()->change();
            $table->string('tipo_sangre', 5)->nullable()->change();
            $table->string('tipo_discapacidad', 100)->nullable()->change();
            $table->string('telefono', 20)->nullable()->change();
            $table->string('telefono_fijo', 20)->nullable()->change();
            $table->string('direccion', 255)->change();
            $table->boolean('tiene_discapacidad')->default(false)->change();
            $table->boolean('victima_conflicto_armado')->default(false)->change();
        });
    }
};