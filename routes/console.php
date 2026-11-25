<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| DBA-09 (B3): ciclo de vida de bitácoras.
|
| No se crea un EVENT de MariaDB porque en el hosting compartido no se puede
| activar event_scheduler. Laravel se encarga de disparar el comando todos
| los días a las 03:15 (hora de America/Bogota, configurada en config/app.php).
|
| Para que el scheduler funcione hace falta UN cron en producción:
|     * * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
*/
Schedule::command('bitacoras:purgar')->dailyAt('03:15');
