<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $ahora = now();

        $parentescos = [
            'Hijastro(a)',
            'Hermano(a)',
            'Hermanastro(a)',
            'Abuelo(a)',
            'Nieto(a)',
            'Tío(a)',
            'Sobrino(a)',
            'Primo(a)',

        ];

        foreach ($parentescos as $nombre) {
            if (!DB::table('parentescos')->where('nombre', $nombre)->exists()) {
                DB::table('parentescos')->insert([
                    'nombre'      => $nombre,
                    'created_at'  => $ahora,
                    'updated_at'  => $ahora,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('parentescos')->whereIn('nombre', [
            'Hijastro(a)',
            'Hermano(a)',
            'Hermanastro(a)',
            'Abuelo(a)',
            'Nieto(a)',
            'Tío(a)',
            'Sobrino(a)',
            'Primo(a)',

        ])->delete();
    }
};
