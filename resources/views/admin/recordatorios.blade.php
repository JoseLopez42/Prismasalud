@extends('admin.layout')

@section('titulo', 'Recordatorios de citas')
@section('encabezado', 'Recordatorios de citas')

@push('estilos')
<style>
    /* Aspecto del chat de WhatsApp para el mensaje del paciente. */
    .wa-cabecera { background: #075e54; }
    .wa-barra    { background: #128c7e; }
    .wa-chat {
        background-color: #ece5dd;
        background-image: radial-gradient(rgba(255,255,255,.45) 1px, transparent 1px);
        background-size: 18px 18px;
    }
    .wa-burbuja {
        background: #fff;
        border-radius: .75rem .75rem .75rem .2rem;
    }
    .wa-burbuja-propia {
        background: #dcf8c6;
        border-radius: .75rem .75rem .2rem .75rem;
    }
    .btn-enviar { transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease; }
    .btn-enviar:not(:disabled):hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,.10); }
    .item-seguimiento { transition: background-color .2s ease; }
</style>
@endpush

@section('contenido')
@php
    $recordatorios = [
        ['id' => 1, 'paciente' => 'María Pérez',     'telefono' => '300 123 4567', 'correo' => 'maria.perez@correo.com',   'cita' => 'Consulta general',       'fecha' => '25 nov 2026', 'hora' => '08:30 a. m.', 'momento' => '24 h antes', 'estado' => 'Programado'],
        ['id' => 2, 'paciente' => 'Juan Rodríguez',  'telefono' => '310 456 7890', 'correo' => 'juan.rodriguez@correo.com','cita' => 'Control de presión',     'fecha' => '25 nov 2026', 'hora' => '10:15 a. m.', 'momento' => '2 h antes',  'estado' => 'Enviado'],
        ['id' => 3, 'paciente' => 'Ana María López', 'telefono' => '315 789 1234', 'correo' => 'ana.lopez@correo.com',     'cita' => 'Laboratorio clínico',    'fecha' => '26 nov 2026', 'hora' => '07:00 a. m.', 'momento' => '24 h antes', 'estado' => 'Confirmado'],
        ['id' => 4, 'paciente' => 'Carlos Mejía',    'telefono' => '301 234 5678', 'correo' => 'carlos.mejia@correo.com',   'cita' => 'Consulta dermatología',  'fecha' => '26 nov 2026', 'hora' => '02:40 p. m.', 'momento' => '2 h antes',  'estado' => 'Programado'],
        ['id' => 5, 'paciente' => 'Lucía Fernández', 'telefono' => '320 987 6543', 'correo' => 'lucia.fernandez@correo.com','cita' => 'Consulta ginecología',   'fecha' => '27 nov 2026', 'hora' => '09:20 a. m.', 'momento' => '24 h antes', 'estado' => 'Fallido'],
    ];

    $coloresEstado = [
        'Programado' => 'bg-sky-50 text-sky-700',
        'Enviado'    => 'bg-emerald-50 text-emerald-700',
        'Confirmado' => 'bg-teal-50 text-teal-700',
        'Fallido'    => 'bg-red-50 text-red-700',
    ];

    $grupos = [
        'enviados'   => array_filter($recordatorios, fn ($r) => in_array($r['estado'], ['Enviado', 'Confirmado'], true)),
        'fallidos'   => array_filter($recordatorios, fn ($r) => $r['estado'] === 'Fallido'),
        'pendientes' => array_filter($recordatorios, fn ($r) => $r['estado'] === 'Programado'),
    ];
@endphp

    {{-- Encabezado y selector de canal --}}
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <h2 class="text-2xl font-extrabold text-gray-900">Recordatorios automáticos de citas</h2>

        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-extrabold text-white shadow-sm"
                  style="background: linear-gradient(120deg, #0C7B93, #12A594);">
                <span class="h-2 w-2 rounded-full bg-white/90"></span>
                WhatsApp Cloud API (Meta)
            </span>
            <span class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-xs font-bold text-gray-400">
                SMS (Twilio) · No configurado
            </span>
        </div>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Pendientes</p>
            <p id="contador-pendientes" class="mt-3 text-3xl font-extrabold text-gray-900">{{ count($grupos['pendientes']) }}</p>
            <p class="mt-1 text-xs text-gray-400">Sin enviar todavía</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Enviados</p>
            <p id="contador-enviados" class="mt-3 text-3xl font-extrabold text-gray-900">{{ count($grupos['enviados']) }}</p>
            <p class="mt-1 text-xs text-gray-400">Correo o WhatsApp</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Fallidos</p>
            <p id="contador-fallidos" class="mt-3 text-3xl font-extrabold text-gray-900">{{ count($grupos['fallidos']) }}</p>
            <p class="mt-1 text-xs text-gray-400">Requieren reintento</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Confirmados</p>
            <p class="mt-3 text-3xl font-extrabold text-gray-900">{{ count(array_filter($recordatorios, fn ($r) => $r['estado'] === 'Confirmado')) }}</p>
            <p class="mt-1 text-xs text-gray-400">Respondieron al mensaje</p>
        </div>
    </div>

    {{-- Cola de recordatorios con botones de envío --}}
    <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-col gap-1 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-base font-extrabold text-gray-900">Próximas citas</h3>
                <p class="mt-0.5 text-xs text-gray-500">Elige un canal para enviar el recordatorio de cada cita.</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-teal-50 px-3 py-1.5 text-xs font-bold text-teal-700">
                <span class="h-2 w-2 rounded-full bg-teal-500"></span>
                {{ count($recordatorios) }} citas en la cola
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] divide-y divide-gray-200 text-left">
                <thead class="bg-gray-50 text-xs font-bold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-2.5">Paciente</th>
                        <th class="px-4 py-2.5">Cita</th>
                        <th class="px-4 py-2.5">Fecha y hora</th>
                        <th class="px-4 py-2.5">Envío</th>
                        <th class="px-4 py-2.5">Estado</th>
                        <th class="px-4 py-2.5 text-right">Enviar por</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($recordatorios as $recordatorio)
                        <tr class="hover:bg-gray-50/70" data-id="{{ $recordatorio['id'] }}"
                            data-paciente="{{ $recordatorio['paciente'] }}" data-estado="{{ $recordatorio['estado'] }}">
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 flex-shrink-0 place-items-center rounded-full bg-teal-50 text-xs font-extrabold text-teal-700">
                                        {{ collect(explode(' ', $recordatorio['paciente']))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('') }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $recordatorio['paciente'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $recordatorio['telefono'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 align-middle">{{ $recordatorio['cita'] }}</td>
                            <td class="px-4 py-3 align-middle">
                                <p class="text-sm font-medium text-gray-800">{{ $recordatorio['fecha'] }}</p>
                                <p class="text-xs text-gray-500">{{ $recordatorio['hora'] }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs font-bold text-teal-700 align-middle">{{ $recordatorio['momento'] }}</td>
                            <td class="px-4 py-3 align-middle">
                                <span class="estado-pill inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $coloresEstado[$recordatorio['estado']] }}">
                                    {{ $recordatorio['estado'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button"
                                            class="btn-enviar inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-extrabold text-indigo-700 hover:bg-indigo-100"
                                            data-canal="correo" title="Enviar a {{ $recordatorio['correo'] }}">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        Correo
                                    </button>
                                    <button type="button"
                                            class="btn-enviar inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-extrabold text-white shadow-sm hover:bg-emerald-700"
                                            data-canal="whatsapp" title="Enviar al {{ $recordatorio['telefono'] }}">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2v10z"/>
                                        </svg>
                                        WhatsApp
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-6 grid gap-6 lg:grid-cols-2">

        {{-- Mensaje de recordatorio en WhatsApp --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="mb-4">
                <h3 class="text-base font-extrabold text-gray-900">Mensaje de recordatorio</h3>
                <p class="mt-0.5 text-xs text-gray-500">Así se ve el recordatorio en el celular del paciente.</p>
            </div>

            <div class="mx-auto w-full max-w-[300px] overflow-hidden rounded-[26px] border-4 border-gray-900 bg-white shadow-lg">
                <div class="wa-cabecera flex items-center gap-2 px-3 py-3 text-white">
                    <div class="grid h-9 w-9 place-items-center rounded-full bg-white/20">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2v10z"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-bold">PrismaSalud IPS</p>
                        <p class="text-[10px] text-white/80">En línea · WhatsApp Business</p>
                    </div>
                </div>

                <div class="wa-chat min-h-[350px] space-y-3 p-3">
                    <p class="text-center">
                        <span class="rounded-md bg-white/80 px-2 py-0.5 text-[10px] font-bold text-gray-500">HOY</span>
                    </p>

                    <div class="wa-burbuja px-3 py-2.5 text-[12px] leading-relaxed text-gray-800 shadow-sm">
                        <p>¡Hola, María! 👋</p>
                        <p class="mt-2">Te recordamos tu cita médica:</p>
                        <p class="mt-2">📅 <strong>Miércoles 25 de noviembre de 2026</strong></p>
                        <p>🕗 <strong>08:30 a. m.</strong></p>
                        <p>🩺 Consulta general</p>
                        <p>📍 Sede Centro · PrismaSalud</p>
                        <p class="mt-2 text-gray-600">Responde <strong>CONFIRMAR</strong> o <strong>REPROGRAMAR</strong>.</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            <span class="rounded-full border border-teal-200 bg-teal-50 px-2 py-0.5 text-[10px] font-bold text-teal-700">Confirmar</span>
                            <span class="rounded-full border border-gray-200 bg-white px-2 py-0.5 text-[10px] font-bold text-gray-600">Reprogramar</span>
                        </div>
                        <p class="mt-1.5 text-right text-[10px] text-gray-400">08:30 ✓✓</p>
                    </div>

                    <div class="wa-burbuja-propia ml-auto max-w-[85%] px-3 py-2 text-[12px] text-gray-800 shadow-sm">
                        Sí, confirmo mi cita. ¡Gracias!
                        <span class="mt-1 block text-right text-[10px] text-gray-500">08:32 ✓✓</span>
                    </div>
                </div>

                <div class="wa-barra flex items-center gap-2 px-3 py-2 text-white/85">
                    <div class="flex-1 rounded-full bg-white/15 px-3 py-1.5 text-[11px]">Mensaje</div>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Plantillas --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <h3 class="text-base font-extrabold text-gray-900">Plantillas de mensaje</h3>
            <p class="mt-0.5 text-xs text-gray-500">Textos preparados para cada momento del recordatorio.</p>

            <div class="mt-4 space-y-3">
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-sm font-extrabold text-gray-800">24 horas antes</p>
                        <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[10px] font-bold text-sky-700">Programado</span>
                    </div>
                    <pre class="whitespace-pre-wrap font-sans text-xs leading-relaxed text-gray-600">¡Hola, {paciente}! Te recordamos tu cita el {fecha} a las {hora} con {medico}. Responde CONFIRMAR para asegurar tu cupo.</pre>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-sm font-extrabold text-gray-800">2 horas antes</p>
                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Programado</span>
                    </div>
                    <pre class="whitespace-pre-wrap font-sans text-xs leading-relaxed text-gray-600">Tu cita con {medico} es hoy a las {hora} en {sede}. Te esperamos puntual 😊.</pre>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-sm font-extrabold text-gray-800">Confirmación de cita</p>
                        <span class="rounded-full bg-teal-50 px-2 py-0.5 text-[10px] font-bold text-teal-700">Al agendar</span>
                    </div>
                    <pre class="whitespace-pre-wrap font-sans text-xs leading-relaxed text-gray-600">Tu cita ha sido agendada: {fecha} a las {hora}, {tipo_cita}. Sede {sede}. PrismaSalud IPS.</pre>
                </div>
            </div>
        </div>
    </div>

    {{-- Seguimiento de envíos --}}
    <div>
        <div class="mb-4 flex items-center gap-3">
            <h3 class="text-base font-extrabold text-gray-900">Seguimiento de envíos</h3>
            <span class="h-px flex-1 bg-gray-200"></span>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            @foreach ([
                'enviados'   => ['titulo' => 'Enviados',   'color' => 'border-emerald-400', 'pill' => 'bg-emerald-50 text-emerald-700'],
                'fallidos'   => ['titulo' => 'Fallidos',   'color' => 'border-red-400',     'pill' => 'bg-red-50 text-red-700'],
                'pendientes' => ['titulo' => 'Pendientes', 'color' => 'border-amber-400',   'pill' => 'bg-amber-50 text-amber-700'],
            ] as $clave => $config)
                <div class="rounded-xl border border-gray-200 border-t-4 {{ $config['color'] }} bg-white p-5" data-grupo="{{ $clave }}">
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-sm font-extrabold text-gray-900">{{ $config['titulo'] }}</h4>
                        <span data-conteo class="rounded-full px-2.5 py-0.5 text-xs font-extrabold {{ $config['pill'] }}">
                            {{ count($grupos[$clave]) }}
                        </span>
                    </div>

                    <div class="lista-seguimiento space-y-2.5">
                        @forelse ($grupos[$clave] as $recordatorio)
                            <div class="item-seguimiento rounded-lg border border-gray-100 bg-gray-50/70 p-3" data-id="{{ $recordatorio['id'] }}">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-gray-900">{{ $recordatorio['paciente'] }}</p>
                                        <p class="mt-0.5 truncate text-xs text-gray-500">{{ $recordatorio['cita'] }}</p>
                                        <p class="mt-1 text-[11px] font-semibold text-teal-700">{{ $recordatorio['fecha'] }} · {{ $recordatorio['hora'] }}</p>
                                    </div>
                                    @if ($clave === 'fallidos')
                                        <button type="button"
                                                class="btn-reintentar flex-shrink-0 rounded-md bg-red-600 px-2.5 py-1 text-[11px] font-extrabold text-white hover:bg-red-700"
                                                data-id="{{ $recordatorio['id'] }}">
                                            Reintentar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="estado-vacio py-6 text-center text-xs text-gray-400">Sin registros en este estado.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div id="aviso-envio" class="fixed bottom-6 right-6 z-50 hidden max-w-sm rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white shadow-2xl">
        <span id="aviso-envio-texto"></span>
    </div>

@endsection

@push('scripts')
<script>
    (function () {
        const basePill = 'estado-pill inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold ';
        let temporizadorAviso;

        function actualizarContadores() {
            const cantidades = { enviados: 0, fallidos: 0, pendientes: 0 };

            document.querySelectorAll('[data-grupo]').forEach(function (grupo) {
                const clave = grupo.dataset.grupo;
                const total = grupo.querySelectorAll('.item-seguimiento').length;
                cantidades[clave] = total;

                const etiqueta = grupo.querySelector('[data-conteo]');
                if (etiqueta) etiqueta.textContent = total;

                const vacio = grupo.querySelector('.estado-vacio');
                if (vacio) vacio.classList.toggle('hidden', total > 0);
            });

            document.getElementById('contador-enviados').textContent = cantidades.enviados;
            document.getElementById('contador-fallidos').textContent = cantidades.fallidos;
            document.getElementById('contador-pendientes').textContent = cantidades.pendientes;
        }

        function mostrarAviso(mensaje) {
            const aviso = document.getElementById('aviso-envio');
            document.getElementById('aviso-envio-texto').textContent = mensaje;
            aviso.classList.remove('hidden');
            clearTimeout(temporizadorAviso);
            temporizadorAviso = setTimeout(function () {
                aviso.classList.add('hidden');
            }, 3200);
        }

        function marcarEnviado(id, canal, paciente) {
            const fila = document.querySelector('tr[data-id="' + id + '"]');
            if (!fila || fila.dataset.estado === 'Enviado') return;

            fila.dataset.estado = 'Enviado';
            const pildora = fila.querySelector('.estado-pill');
            pildora.textContent = 'Enviado';
            pildora.className = basePill + 'bg-emerald-50 text-emerald-700';

            fila.querySelectorAll('.btn-enviar').forEach(function (boton) {
                boton.disabled = true;
                boton.classList.add('opacity-40', 'cursor-not-allowed');
            });

            const item = document.querySelector('.item-seguimiento[data-id="' + id + '"]');
            if (item) {
                const reintentar = item.querySelector('.btn-reintentar');
                if (reintentar) reintentar.remove();
                document.querySelector('[data-grupo="enviados"] .lista-seguimiento').appendChild(item);
            }

            actualizarContadores();
            mostrarAviso('Recordatorio enviado por ' + (canal === 'correo' ? 'correo' : 'WhatsApp') + ' a ' + paciente + '.');
        }

        document.querySelectorAll('.btn-enviar').forEach(function (boton) {
            boton.addEventListener('click', function () {
                const fila = this.closest('tr');
                marcarEnviado(fila.dataset.id, this.dataset.canal, fila.dataset.paciente);
            });
        });

        document.querySelectorAll('.btn-reintentar').forEach(function (boton) {
            boton.addEventListener('click', function () {
                const item = this.closest('.item-seguimiento');
                const id = this.dataset.id;
                const fila = document.querySelector('tr[data-id="' + id + '"]');
                marcarEnviado(id, 'whatsapp', fila ? fila.dataset.paciente : 'el paciente');
            });
        });

        actualizarContadores();
    })();
</script>
@endpush
