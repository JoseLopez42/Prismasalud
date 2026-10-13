<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const CONYUGE = 'Cónyuge o compañero(a) permanente';

    /**
     * Deja en "parentescos" solo el núcleo familiar principal:
     * Cónyuge o compañero(a) permanente (es la misma opción), Hijo(a),
     * Hijastro(a), Padre y Madre. Si la tabla ya se había creado con más
     * opciones, arregla/borra las que sobran. Se puede correr varias veces.
     */
    public function up(): void
    {
        $ahora = now();

        // 1) Cónyuge y compañero(a) permanente se unen en una sola opción
        if (!DB::table('parentescos')->where('nombre', self::CONYUGE)->exists()) {
            DB::table('parentescos')->insert([
                'nombre' => self::CONYUGE, 'created_at' => $ahora, 'updated_at' => $ahora,
            ]);
        }

        // Beneficiarios ya guardados con los nombres viejos pasan al nuevo
        DB::table('beneficiarios')
            ->whereIn('parentesco', ['Cónyuge', 'Compañero(a) permanente'])
            ->update(['parentesco' => self::CONYUGE]);

        // 2) Borra todo lo que no sea del núcleo principal
        DB::table('parentescos')->whereNotIn('nombre', [
            self::CONYUGE,
            'Hijo(a)',
            'Hijastro(a)',
            'Padre',
            'Madre',
        ])->delete();

        // 3) Si faltaba alguna opción del núcleo principal, la agrega
        foreach (['Hijo(a)', 'Hijastro(a)', 'Padre', 'Madre'] as $nombre) {
            if (!DB::table('parentescos')->where('nombre', $nombre)->exists()) {
                DB::table('parentescos')->insert([
                    'nombre' => $nombre, 'created_at' => $ahora, 'updated_at' => $ahora,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Sin reversa
    }
};