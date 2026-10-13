<?php

namespace App\Mail;

use App\Models\Licencia;
use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LicenciaActivadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public Licencia $licencia;
    public string $nombreAdmin;

    public function __construct(Licencia $licencia, string $nombreAdmin = '')
    {
        $this->licencia = $licencia;
        $this->nombreAdmin = $nombreAdmin;
    }

    public function build()
    {
        return $this->subject('Tu licencia de PrismaSalud está activa')
            ->view('emails.licencia-activada');
    }
}
