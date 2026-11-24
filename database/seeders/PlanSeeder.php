<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $planes = [
            [
                'slug' => 'gratis',
                'nombre' => 'Gratis',
                'etiqueta' => 'Prueba por 1 mes',
                'descripcion' => 'La opción inicial para organizar tus citas y hasta 15 pacientes activos durante un mes.',
                'precio_total' => 0,
                'periodo' => 'por 30 dias',
                'ideal' => 'EPS pequeñas y consultorios que quieren probar la plataforma durante un mes.',
                'beneficios' => [
                    'Acceso durante 1 mes',
                    'Hasta 15 pacientes activos',
                    'Hasta 3 usuarios y 1 sede',
                    'Agenda de citas y ficha de pacientes',
                    'Historial clínico básico',
                    'Reportes operativos básicos',
                    '1 GB de almacenamiento',
                ],
                'no_incluye' => [
                    'Más de 1 mes de uso',
                    'Más de 15 pacientes activos',
                    'Facturación, inventario y farmacia',
                    'Roles y permisos avanzados',
                    'Integraciones con otros sistemas',
                ],
                'cta' => 'Probar gratis',
                'cta_url' => '/comprar/gratis',
                'icono' => 'sparkles',
                'clase_icono' => 'plan-icon--teal',
                'destacada' => false,
                'orden' => 1,
            ],
            [
                'slug' => 'un-anio',
                'nombre' => '1 año',
                'etiqueta' => 'Vigencia: 12 meses',
                'descripcion' => 'Para EPS que necesitan crecer con más capacidad durante un año.',
                'precio_total' => 854400,
                'periodo' => 'por 365 dias',
                'ideal' => 'EPS que necesitan organizar su operación sin límite de pacientes durante 12 meses.',
                'beneficios' => [
                    'Pacientes activos sin límite',
                    'Vigencia de 12 meses',
                    'La fórmula se envía al correo del paciente',
                    'El paciente la reclama en la sede de farmacia',
                    'Hasta 5 usuarios y 2 sedes',
                    'Todo lo del plan Gratis',
                    'Recordatorios automáticos de citas',
                    'Prescripciones e inventario básico',
                    'Reportes de gestión y ocupación',
                    '20 GB de almacenamiento',
                    'Soporte prioritario por correo',
                ],
                'no_incluye' => [
                    'Envío directo de la fórmula a la farmacia',
                    'Integraciones y API personalizadas',
                    'Facturación avanzada y control de compras',
                    'Dashboard ejecutivo multi-sede',
                ],
                'cta' => 'Elegir 1 año',
                'cta_url' => '/comprar/un-anio',
                'icono' => 'users',
                'clase_icono' => 'plan-icon--blue',
                'destacada' => false,
                'orden' => 2,
            ],
            [
                'slug' => 'dos-anios',
                'nombre' => '2 años',
                'etiqueta' => 'Vigencia: 24 meses',
                'descripcion' => 'Una opción estable para EPS que necesitan continuidad y crecimiento durante dos años.',
                'precio_total' => 3436800,
                'periodo' => 'por 730 dias',
                'ideal' => 'IPS, clínicas y redes de atención que necesitan operar sin límite de pacientes durante 24 meses.',
                'beneficios' => [
                    'Pacientes activos sin límite',
                    'Vigencia de 24 meses',
                    'La fórmula se envía al correo del paciente',
                    'El paciente la reclama en la sede de farmacia',
                    'Hasta 20 usuarios y 10 sedes',
                    'Todo lo del plan de 1 año',
                    'Roles, permisos y bitácora de auditoría',
                    'Inventario, compras y medicamentos',
                    'Facturación, cartera y reportes avanzados',
                    'Dashboard ejecutivo y API',
                    '100 GB de almacenamiento',
                ],
                'no_incluye' => [
                    'Envío directo de la fórmula a la farmacia',
                    'Infraestructura dedicada y SLA personalizado',
                    'Integraciones y residencia de datos',
                ],
                'cta' => 'Elegir 2 años',
                'cta_url' => '/comprar/dos-anios',
                'icono' => 'chart',
                'clase_icono' => 'plan-icon--purple',
                'destacada' => false,
                'orden' => 3,
            ],
            [
                'slug' => 'un-anio-farmacia',
                'nombre' => '1 año + Farmacia',
                'etiqueta' => 'Entrega directa',
                'descripcion' => 'Además de las funciones del plan de 1 año, envía la fórmula directamente a la farmacia y evita que el paciente haga fila.',
                'precio_total' => 1068000,
                'periodo' => 'por 365 dias',
                'ideal' => 'EPS que buscan una gestión de medicamentos más ágil durante un año.',
                'beneficios' => [
                    'Todo lo del plan de 1 año',
                    'Pacientes activos sin límite',
                    'La fórmula se envía directamente a la farmacia',
                    'El farmaceuta recibe la fórmula de inmediato',
                    'Correo con la sede, el horario y las indicaciones de reclamo',
                    'El paciente reclama sin hacer fila',
                    'Historial de envíos y estado de la fórmula',
                ],
                'no_incluye' => [
                    'Entrega a domicilio, si se requiere',
                ],
                'cta' => 'Elegir 1 año + Farmacia',
                'cta_url' => '/comprar/un-anio-farmacia',
                'icono' => 'pills',
                'clase_icono' => 'plan-icon--pink',
                'destacada' => false,
                'orden' => 4,
            ],
            [
                'slug' => 'dos-anios-farmacia',
                'nombre' => '2 años + Farmacia',
                'etiqueta' => 'Entrega directa',
                'descripcion' => 'La continuidad de dos años con envío directo de fórmulas a la farmacia y aviso al paciente para reclamarlas sin hacer fila.',
                'precio_total' => 4296000,
                'periodo' => 'por 730 dias',
                'ideal' => 'EPS que necesitan escalar su operación y administrar medicamentos sin filas.',
                'beneficios' => [
                    'Todo lo del plan de 2 años',
                    'Pacientes activos sin límite',
                    'La fórmula se envía directamente a la farmacia',
                    'El farmaceuta recibe la fórmula de inmediato',
                    'Correo con la sede, el horario y las indicaciones de reclamo',
                    'El paciente reclama sin hacer fila',
                    'Historial de envíos y estado de la fórmula',
                    '100 GB de almacenamiento',
                ],
                'no_incluye' => [
                    'Entrega a domicilio, si se requiere',
                ],
                'cta' => 'Elegir 2 años + Farmacia',
                'cta_url' => '/comprar/dos-anios-farmacia',
                'icono' => 'pills',
                'clase_icono' => 'plan-icon--green',
                'destacada' => true,
                'orden' => 5,
            ],
        ];

        foreach ($planes as $datos) {
            Plan::updateOrCreate(['slug' => $datos['slug']], $datos);
        }
    }
}