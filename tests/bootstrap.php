<?php

/*
 | REC-07: la sesión nativa de PHP debe abrirse antes de que PHPUnit
 | imprima su salida; si se abre dentro de setUp() llega tarde y
 | session_start() avisa de "headers already sent".
*/

require __DIR__.'/../vendor/autoload.php';

// REC-07: con config:cache los cambios de .env no se reflejan y las pruebas
// podrían apuntar a la base real; se corta antes de iniciar la sesión.
if (file_exists(__DIR__.'/../bootstrap/cache/config.php')) {
    throw new RuntimeException('Corre php artisan config:clear antes de las pruebas: la configuración en caché puede apuntar a la base real.');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
