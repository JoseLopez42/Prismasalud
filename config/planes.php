<?php

return [
    'whatsapp_url' => 'https://wa.me/573017880161?text=Hola%2C%20quiero%20agendar%20una%20cita',

    'faq_general' => [
        [
            'pregunta' => '¿Puedo comenzar con el plan Gratis?',
            'respuesta' => 'Sí. Puedes crear tu cuenta sin pagar durante 1 mes y registrar hasta 15 pacientes activos.',
        ],
        [
            'pregunta' => '¿Qué pasa al terminar el mes gratis?',
            'respuesta' => 'Puedes elegir un plan de 1 o 2 años, con o sin entrega directa a farmacia. El plan Gratis no se renueva automáticamente.',
        ],
        [
            'pregunta' => '¿Los datos quedan almacenados en la nube?',
            'respuesta' => 'Sí. PrismaSalud Software está diseñado para trabajar en la nube, con acceso organizado por roles y medidas de seguridad para proteger la información.',
        ],
        [
            'pregunta' => '¿Cómo solicito una demostración?',
            'respuesta' => 'Escribe desde la página de Contacto, elige el plan que te interesa y un asesor te contactará para mostrarte el producto y resolver tus preguntas.',
        ],
    ],

    'faq_por_plan' => [
        'gratis' => [
            [
                'pregunta' => '¿Puedo registrar más de 15 pacientes?',
                'respuesta' => 'No. La prueba gratuita permite hasta 15 pacientes activos. Si necesitas más capacidad, debes elegir un plan de 1 o 2 años.',
            ],
            [
                'pregunta' => '¿El plan Gratis se renueva?',
                'respuesta' => 'No. El acceso gratuito dura 1 mes y no se renueva automáticamente.',
            ],
        ],
        'un-anio' => [
            [
                'pregunta' => '¿Tiene límite de pacientes?',
                'respuesta' => 'No. Durante los 12 meses de vigencia puedes registrar pacientes activos sin límite.',
            ],
            [
                'pregunta' => '¿Cómo llega la fórmula?',
                'respuesta' => 'El doctor envía la fórmula al correo del paciente. El paciente debe reclamarla en la sede de farmacia.',
            ],
        ],
        'dos-anios' => [
            [
                'pregunta' => '¿Tiene límite de pacientes?',
                'respuesta' => 'No. Durante los 24 meses de vigencia puedes registrar pacientes activos sin límite.',
            ],
            [
                'pregunta' => '¿Cómo llega la fórmula?',
                'respuesta' => 'El doctor envía la fórmula al correo del paciente. El paciente debe reclamarla en la sede de farmacia.',
            ],
        ],
        'un-anio-farmacia' => [
            [
                'pregunta' => '¿Cómo se entrega la fórmula?',
                'respuesta' => 'La fórmula se envía directamente a la farmacia y el farmaceuta la recibe de inmediato.',
            ],
            [
                'pregunta' => '¿El paciente hace fila?',
                'respuesta' => 'No. El paciente recibe un correo con la sede, el horario y las indicaciones para reclamar sus medicamentos sin hacer fila.',
            ],
        ],
        'dos-anios-farmacia' => [
            [
                'pregunta' => '¿Cómo se entrega la fórmula?',
                'respuesta' => 'La fórmula se envía directamente a la farmacia y el farmaceuta la recibe de inmediato.',
            ],
            [
                'pregunta' => '¿El paciente hace fila?',
                'respuesta' => 'No. El paciente recibe un correo con la sede, el horario y las indicaciones para reclamar sus medicamentos sin hacer fila.',
            ],
        ],
    ],
];