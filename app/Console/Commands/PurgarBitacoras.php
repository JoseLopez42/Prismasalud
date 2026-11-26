<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/*
| DBA-09 (B2): ciclo de vida de bitácoras.
|
| No se usa EVENT de MariaDB porque en hosting compartido (Hostinger) no se
| puede activar el event_scheduler. En su lugar, este comando hace la purga
| y Laravel lo llama desde el scheduler (routes/console.php) todos los días
| a las 03:15. En producción basta un cron de Linux que ejecute cada minuto:
|     * * * * * php artisan schedule:run >> /dev/null 2>&1
|
| Usa la conexión por defecto de la aplicación (prisma_app): solo necesita
| SELECT, INSERT y DELETE, por eso NO se usa la conexión "migraciones".
|
| Prueba manual:
|     php artisan bitacoras:purgar --simular
|     php artisan bitacoras:purgar
*/
class PurgarBitacoras extends Command
{
    /** Firma del comando: --simular solo cuenta, no escribe. */
    protected $signature = 'bitacoras:purgar {--simular : Solo muestra cuántas filas se afectarían, sin cambiar nada}';

    protected $description = 'Borra sesiones viejas y mueve las bitácoras antiguas a sus tablas históricas';

    /* Plazos de retención: una sola fuente de verdad para todo el comando. */
    private const DIAS_RETENCION_SESIONES = 30;

    private const ANIOS_RETENCION_INGRESOS = 1;

    private const ANIOS_RETENCION_LICENCIAS = 2;

    public function handle(): int
    {
        $simular = (bool) $this->option('simular');

        $this->line($simular
            ? 'MODO SIMULACIÓN: no se borrará ni moverá ninguna fila.'
            : 'MODO EJECUCIÓN: se aplicará la purga de bitácoras.');
        $this->line('');

        // Cada método devuelve [tabla, acción, cantidad] para el resumen final.
        $resultados = [
            $this->purgaSesiones($simular),
            $this->purgaLogIngresos($simular),
            $this->purgaLogLicencias($simular),
        ];

        $this->table(['Tabla', 'Acción', 'Filas'], $resultados);

        return self::SUCCESS;
    }

    /**
     * Sesiones: se BORRAN las de más de 30 días.
     * last_activity guarda un timestamp UNIX (segundos), no una fecha SQL.
     */
    private function purgaSesiones(bool $simular): array
    {
        $limite = now()->subDays(self::DIAS_RETENCION_SESIONES)->getTimestamp();

        $cantidad = DB::table('sessions')->where('last_activity', '<', $limite)->count();

        // Solo en modo real se ejecuta el DELETE.
        if (! $simular && $cantidad > 0) {
            $cantidad = DB::table('sessions')->where('last_activity', '<', $limite)->delete();
        }

        return [
            'sessions',
            $simular ? 'Borraría' : 'Borradas',
            $cantidad,
        ];
    }

    /**
     * Ingresos: se mueven a log_ingresos_historico las de más de 1 año.
     * La columna fecha es DATE, así que el corte se arma como Y-m-d.
     *
     | INSERT IGNORE evita fallar si un id ya estaba en el histórico;
     | el INSERT y el DELETE van en la MISMA transacción para que nunca
     | quede una fila borrada que no se haya podido archivar.
     */
    private function purgaLogIngresos(bool $simular): array
    {
        $limite = now()->subYears(self::ANIOS_RETENCION_INGRESOS)->toDateString();

        $cantidad = DB::table('log_ingresos')->where('fecha', '<', $limite)->count();

        if (! $simular && $cantidad > 0) {
            $movidas = 0;

            DB::transaction(function () use ($limite, &$movidas) {
                $columnas = ['id', 'cedula', 'tipo_usuario', 'fecha', 'hora'];
                $consulta = DB::table('log_ingresos')
                    ->select($columnas)
                    ->where('fecha', '<', $limite);

                // INSERT ... SELECT: primero copiamos al histórico.
                DB::statement(
                    'INSERT IGNORE INTO `log_ingresos_historico` (`id`, `cedula`, `tipo_usuario`, `fecha`, `hora`) '.$consulta->toSql(),
                    $consulta->getBindings()
                );

                // Después borramos de la tabla activa; el valor devuelto es
                // el número real de filas eliminadas.
                $movidas = DB::table('log_ingresos')->where('fecha', '<', $limite)->delete();
            });

            $cantidad = $movidas;
        }

        return [
            'log_ingresos',
            $simular ? 'Movería a histórico' : 'Movidas a histórico',
            $cantidad,
        ];
    }

    /**
     * Licencias: se mueven a log_licencias_historico las de más de 2 años.
     * fecha_cambio es DATETIME, por eso el corte incluye la hora.
     */
    private function purgaLogLicencias(bool $simular): array
    {
        $limite = now()->subYears(self::ANIOS_RETENCION_LICENCIAS)->toDateTimeString();

        $cantidad = DB::table('log_licencias')->where('fecha_cambio', '<', $limite)->count();

        if (! $simular && $cantidad > 0) {
            $movidas = 0;

            DB::transaction(function () use ($limite, &$movidas) {
                $columnas = ['id', 'licencia_id', 'nombre_ips', 'plan_anterior', 'plan_nuevo', 'fecha_cambio'];
                $consulta = DB::table('log_licencias')
                    ->select($columnas)
                    ->where('fecha_cambio', '<', $limite);

                DB::statement(
                    'INSERT IGNORE INTO `log_licencias_historico` (`id`, `licencia_id`, `nombre_ips`, `plan_anterior`, `plan_nuevo`, `fecha_cambio`) '.$consulta->toSql(),
                    $consulta->getBindings()
                );

                $movidas = DB::table('log_licencias')->where('fecha_cambio', '<', $limite)->delete();
            });

            $cantidad = $movidas;
        }

        return [
            'log_licencias',
            $simular ? 'Movería a histórico' : 'Movidas a histórico',
            $cantidad,
        ];
    }
}
