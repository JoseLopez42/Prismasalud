<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $mapa = [
            'gratis' => 'por 30 dias',
            'un-anio' => 'por 365 dias',
            'dos-anios' => 'por 730 dias',
            'un-anio-farmacia' => 'por 365 dias',
            'dos-anios-farmacia' => 'por 730 dias',
        ];

        foreach ($mapa as $slug => $periodo) {
            DB::table('planes')->where('slug', $slug)->update(['periodo' => $periodo]);
        }
    }

    public function down(): void
    {
        // No action required; keep current values.
    }
};
