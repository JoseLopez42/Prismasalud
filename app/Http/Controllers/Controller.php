<?php

namespace App\Http\Controllers;

use App\Models\Admin\CitaMedica;

abstract class Controller
{
    protected function citaAtendida($idCita): bool
    {
        if (!$idCita) {
            return false;
        }

        return CitaMedica::with('estadoCita')->find($idCita)?->atendida() ?? false;
    }

    protected function abortarSiCitaAtendida($idCita): void
    {
        if ($this->citaAtendida($idCita)) {
            abort(403, 'La cita ya fue marcada como atendida y no se puede modificar.');
        }
    }

    protected function idCitaDeRegistro($registro): ?int
    {
        if (!$registro) {
            return null;
        }

        if (!empty($registro->id_cita)) {
            return (int) $registro->id_cita;
        }

        if (method_exists($registro, 'historiaClinica') && $registro->historiaClinica) {
            return $registro->historiaClinica->id_cita ? (int) $registro->historiaClinica->id_cita : null;
        }

        if (method_exists($registro, 'procedimiento') && $registro->procedimiento) {
            return $registro->procedimiento->id_cita ? (int) $registro->procedimiento->id_cita : null;
        }

        return null;
    }

    protected function abortarSiRegistroEnCitaAtendida($registro): void
    {
        $this->abortarSiCitaAtendida($this->idCitaDeRegistro($registro));
    }
}
