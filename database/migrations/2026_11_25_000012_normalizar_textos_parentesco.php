<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const REPLACEMENTS = [
        'Conyuge' => 'Cónyuge o compañero(a) permanente',
        'Hijo/a' => 'Hijo(a)',
        'Hermano/a' => 'Hermano(a)',
    ];

    public function up(): void
    {
        foreach (['afiliacion', 'beneficiarios'] as $table) {
            foreach (self::REPLACEMENTS as $antes => $despues) {
                DB::table($table)
                    ->where('parentesco', $antes)
                    ->update(['parentesco' => $despues]);
            }
        }
    }

    public function down(): void
    {
        $inverso = array_flip(self::REPLACEMENTS);

        foreach (['afiliacion', 'beneficiarios'] as $table) {
            foreach ($inverso as $antes => $despues) {
                DB::table($table)
                    ->where('parentesco', $antes)
                    ->update(['parentesco' => $despues]);
            }
        }
    }
};
