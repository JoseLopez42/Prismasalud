<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * DBA-03 (parte 3): campo sí/no guardado CIFRADO en la base.
 *
 * Ley 1581 de 2012: discapacidad y víctima del conflicto son datos sensibles.
 * En la base se guarda el texto '1' o '0' cifrado con APP_KEY (AES-256);
 * al leerlo desde el modelo se recibe true o false.
 *
 * Se usa en vez del cast 'encrypted' de Laravel porque ese trabaja con texto
 * y no maneja bien true/false.
 */
class BooleanoCifrado implements CastsAttributes
{
    // De la base al modelo: descifra y convierte a true/false.
    public function get(Model $model, string $key, mixed $value, array $attributes): ?bool
    {
        if ($value === null) {
            return null;
        }

        return Crypt::decryptString($value) === '1';
    }

    // Del modelo a la base: cualquier valor "verdadero" se guarda como '1', lo demás como '0', y se cifra.
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return Crypt::encryptString(filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0');
    }
}