<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| DBA-09 (C2): quitar la columna edad.
|
| La edad es un dato DERIVADO: se calcula desde fecha_nacimiento, que sí es
| un dato fuente. Guardarla en dos tablas (usuarios y beneficiarios) obligaba
| a validarla dos veces y a corregirla a mano cada cumpleaños.
|
| Importante: usuarios NO tiene fecha_nacimiento. La fecha de los pacientes
| está en beneficiarios.fecha_nacimiento, relacionada por cédula
| (usuarios.cedula = beneficiarios.cc_afiliado). Por eso el accessor de
| edad de Usuario consulta esa tabla, y el down() cruza por cédula.
|
| up() borra columnas con datos: ejecutar SOLO después del respaldo
| unoa_antes_dba09_c.sql y con la conexión de migraciones:
|   php artisan migrate --database=migraciones --path=database/migrations/2026_11_25_180045_quitar_columnas_edad.php
*/
return new class extends Migration
{
    /**
     * Elimina las columnas redundantes de edad.
     */
    public function up(): void
    {
        if (Schema::hasColumn('beneficiarios', 'edad')) {
            Schema::table('beneficiarios', function (Blueprint $table) {
                $table->dropColumn('edad');
            });
        }

        if (Schema::hasColumn('usuarios', 'edad')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('edad');
            });
        }
    }

    /**
     * Recrea las columnas y las llena calculando la edad con SQL.
     *
     | TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) da los años cumplidos
     | a hoy. En usuarios se toma la fecha del beneficiario con la misma
     | cédula; si no existe o no tiene fecha, la columna queda en NULL.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('beneficiarios', 'edad')) {
            Schema::table('beneficiarios', function (Blueprint $table) {
                $table->unsignedInteger('edad')->nullable()->after('apellidos');
            });
        }

        DB::statement('
            UPDATE `beneficiarios`
               SET `edad` = TIMESTAMPDIFF(YEAR, `fecha_nacimiento`, CURDATE())
             WHERE `fecha_nacimiento` IS NOT NULL
        ');

        if (! Schema::hasColumn('usuarios', 'edad')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->unsignedInteger('edad')->nullable()->after('apellidos');
            });
        }

        DB::statement('
            UPDATE `usuarios` AS u
              JOIN `beneficiarios` AS b
                ON b.`cc_afiliado` = u.`cedula`
               SET u.`edad` = TIMESTAMPDIFF(YEAR, b.`fecha_nacimiento`, CURDATE())
             WHERE b.`fecha_nacimiento` IS NOT NULL
        ');
    }
};
