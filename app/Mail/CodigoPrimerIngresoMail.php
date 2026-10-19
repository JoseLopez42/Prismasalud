<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CodigoPrimerIngresoMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $codigo;
    public string $nombreUsuario;

    /**
     * Recibe el código de 6 dígitos y el nombre del usuario, para
     * poder mostrarlos dentro de la plantilla del correo.
     */
    public function __construct(string $codigo, string $nombreUsuario)
    {
        $this->codigo = $codigo;
        $this->nombreUsuario = $nombreUsuario;
    }

    public function build()
    {
        return $this->subject('Tu código de primer ingreso - PrismaSalud IPS')
                     ->view('emails.codigo-primer-ingreso');
    }
}
