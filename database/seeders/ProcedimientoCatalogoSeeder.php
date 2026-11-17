<?php

namespace Database\Seeders;

use App\Models\Admin\CatalogoProcedimiento;
use App\Models\Admin\TipoProcedimiento;
use Illuminate\Database\Seeder;

class ProcedimientoCatalogoSeeder extends Seeder
{
    /**
     * Catálogo base de procedimientos de la IPS.
     * Se puede volver a ejecutar: usa updateOrCreate, no duplica registros.
     */
    public function run(): void
    {
        $catalogo = [
            'Exámenes de laboratorio clínico' => [
                'Hemogramas',
                'Química sanguínea',
                'Uroanálisis',
                'Cultivos',
            ],
            'Imágenes diagnósticas' => [
                'Radiografías',
                'Ecografías',
                'TAC',
                'Resonancia magnética',
            ],
            'Pruebas funcionales y especializadas' => [
                'Electrocardiogramas',
                'Endoscopias',
                'Biopsias',
                'Espirometrías',
            ],
        ];

        foreach ($catalogo as $nombreTipo => $procedimientos) {
            $tipo = TipoProcedimiento::updateOrCreate(
                ['nombre' => $nombreTipo],
                [
                    'descripcion' => 'Catálogo base de procedimientos de la IPS.',
                    'activo' => true,
                ]
            );

            foreach ($procedimientos as $nombre) {
                CatalogoProcedimiento::updateOrCreate(
                    ['nombre' => $nombre],
                    [
                        'id_tipo_procedimiento' => $tipo->id,
                        'activo' => true,
                    ]
                );
            }
        }
    }
}
