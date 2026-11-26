<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| DBA-09 (B1): tablas históricas de las bitácoras.
|
| El comando bitacoras:purgar mueve las filas viejas a estas tablas en vez
| de borrarlas definitivamente. Así se conserva el historial para auditoría
| sin recargar las tablas activas que usa la aplicación todos los días.
|
| CREATE TABLE ... LIKE copia las columnas, los índices y el AUTO_INCREMENT
| exactamente igual que la tabla original. Por eso el INSERT ... SELECT del
| comando puede usar "SELECT *" sin enumerar columnas.
|
| Esta migración SOLO crea tablas vacías: no borra ni mueve datos.
| Se ejecuta con la conexión de migraciones (root):
|   php artisan migrate --database=migraciones --path=database/migrations/2026_11_25_173855_crear_tablas_historicas_bitacoras.php
*/
return new class extends Migration
{
    /**
     * Crea las dos tablas históricas solo si no existen.
     * El IF NOT EXISTS permite volver a ejecutarla sin errores.
     */
    public function up(): void
    {
        if (! Schema::hasTable('log_ingresos_historico')) {
            DB::statement('CREATE TABLE IF NOT EXISTS `log_ingresos_historico` LIKE `log_ingresos`');
        }

        if (! Schema::hasTable('log_licencias_historico')) {
            DB::statement('CREATE TABLE IF NOT EXISTS `log_licencias_historico` LIKE `log_licencias`');
        }
    }

    /**
     * Revierte la migración.
     *
     | Antes de borrar las tablas históricas se devuelven sus filas a las
     | tablas activas. INSERT IGNORE evita duplicar si el mismo id ya existe
     | en la tabla activa (por ejemplo, después de una purga simulada o de
     | una restauración parcial).
     */
    public function down(): void
    {
        if (Schema::hasTable('log_ingresos_historico')) {
            DB::statement('INSERT IGNORE INTO `log_ingresos` SELECT * FROM `log_ingresos_historico`');
            DB::statement('DROP TABLE `log_ingresos_historico`');
        }

        if (Schema::hasTable('log_licencias_historico')) {
            DB::statement('INSERT IGNORE INTO `log_licencias` SELECT * FROM `log_licencias_historico`');
            DB::statement('DROP TABLE `log_licencias_historico`');
        }
    }
};
