<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parentescos', function (Blueprint $table) {
            $table->id('id_parentesco');
            $table->string('nombre', 40)->unique();
            $table->timestamps();
        });

        $ahora = now();
        // Núcleo familiar principal (sin costo adicional)
        $opciones = [
            'Cónyuge o compañero(a) permanente',
            'Hijo(a)',
            'Hijastro(a)',
            'Padre',
            'Madre',
        ];

        foreach ($opciones as $nombre) {
            DB::table('parentescos')->insert([
                'nombre'     => $nombre,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('parentescos');
    }
};