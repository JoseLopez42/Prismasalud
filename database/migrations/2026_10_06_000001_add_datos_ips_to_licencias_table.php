<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('licencias')) {
            return;
        }

        // La tabla ya se crea con estas columnas en 2026_09_27_000002.
        // Solo agregamos las que falten, para no romper una BD existente.
        $columnas = [
            'tipo_entidad' => ['string', 30],
            'representante_legal' => ['string', 150],
            'correo_ips' => ['string', 150],
            'telefono_ips' => ['string', 20],
            'departamento' => ['string', 80],
            'ciudad' => ['string', 80],
            'direccion' => ['string', 200],
        ];

        $faltan = array_filter($columnas, fn ($tipo, $nombre) => ! Schema::hasColumn('licencias', $nombre), ARRAY_FILTER_USE_BOTH);

        if (! $faltan) {
            return;
        }

        Schema::table('licencias', function (Blueprint $table) use ($faltan) {
            foreach ($faltan as $nombre => $tipo) {
                $table->{$tipo[0]}($nombre, $tipo[1])->nullable();
            }
        });
    }

    public function down(): void
    {
        // Las columnas ya forman parte de la creación de la tabla.
    }
};
