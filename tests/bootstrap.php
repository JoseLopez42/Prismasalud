<?php

/*
 | REC-07: la sesión nativa de PHP debe abrirse antes de que PHPUnit
 | imprima su salida; si se abre dentro de setUp() llega tarde y
 | session_start() avisa de "headers already sent".
*/

require __DIR__.'/../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
