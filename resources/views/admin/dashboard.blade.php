{{-- Panel principal del Administrador. Hereda layout para el
     sidebar responsive con menú hamburguesa en móvil. --}}
@extends('admin.layout')

@section('titulo', 'Dashboard')
@section('encabezado', 'Panel del Administrador')

@push('estilos')
<style>
    @keyframes subir-dashboard {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .dashboard-entrada { animation: subir-dashboard .45s ease both; }
</style>
@endpush

@section('contenido')

    @php
        $hora = (int) now()->format('G');
        $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');

        $fechaHoy = now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y');

        // Variedad de acentos para las tarjetas de estadísticas
        $acentosStats = [
            ['barra' => 'bg-gradient-to-r from-teal-500 to-teal-300', 'icono' => 'bg-teal-50 text-teal-600'],
            ['barra' => 'bg-gradient-to-r from-blue-500 to-sky-300',   'icono' => 'bg-blue-50 text-blue-600'],
            ['barra' => 'bg-gradient-to-r from-amber-500 to-amber-300','icono' => 'bg-amber-50 text-amber-600'],
            ['barra' => 'bg-gradient-to-r from-violet-500 to-violet-300','icono' => 'bg-violet-50 text-violet-600'],
        ];

        $accesos = [
            ['icono' => 'citas',    'texto' => 'Citas médicas',  'ruta' => route('cita.index')],
            ['icono' => 'usuarios', 'texto' => 'Usuarios',       'ruta' => route('usuarios.index')],
            ['icono' => 'medicos',  'texto' => 'Médicos',        'ruta' => route('medico.index')],
            ['icono' => 'historia', 'texto' => 'Historia clínica','ruta' => route('historia.index')],
            ['icono' => 'farmacia', 'texto' => 'Farmacia',       'ruta' => route('medicamento.index')],
            ['icono' => 'configuracion', 'texto' => 'Mi plan',   'ruta' => route('admin.plan')],
        ];

        // Resumen visual de todos los módulos del sistema
        $modulos = [
            ['icono' => 'usuarios',      'titulo' => 'Usuarios y Roles',        'tablas' => ['usuarios', 'roles', 'tipos de documento']],
            ['icono' => 'ubicaciones',   'titulo' => 'Ubicaciones',             'tablas' => ['departamento', 'ciudad', 'sede']],
            ['icono' => 'eps',           'titulo' => 'EPS',                     'tablas' => ['eps', 'regimen']],
            ['icono' => 'afiliaciones',  'titulo' => 'Afiliaciones',            'tablas' => ['afiliacion', 'beneficiarios', 'estado']],
            ['icono' => 'medicos',       'titulo' => 'Médicos y Consultorios',  'tablas' => ['medico', 'especialidad', 'consultorio', 'horario_medico']],
            ['icono' => 'citas',         'titulo' => 'Citas Médicas',           'tablas' => ['cita', 'tipo_cita', 'estado_cita']],
            ['icono' => 'historia',      'titulo' => 'Historia Clínica',        'tablas' => ['historia', 'diagnostico', 'enfermedad']],
            ['icono' => 'procedimientos','titulo' => 'Procedimientos',          'tablas' => ['procedimiento', 'tipo_procedimiento', 'detalle_procedimiento']],
            ['icono' => 'farmacia',      'titulo' => 'Farmacia y Medicamentos', 'tablas' => ['medicamentos']],
        ];
    @endphp

    {{-- Bienvenida --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1A5C3A] to-[#2E8B57] text-white p-6 md:p-8 mb-6">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center text-xl font-bold flex-shrink-0">
                {{ strtoupper(substr($usuarioActual->nombres ?? 'A', 0, 1)) }}
            </div>
            <div class="flex-1">
                <p class="text-white/70 text-sm capitalize">{{ $fechaHoy }}</p>
                <h2 class="text-xl md:text-2xl font-bold mt-0.5">{{ $saludo }}, {{ $usuarioActual->nombres }}</h2>
                <p class="text-white/80 text-sm mt-1">
                    Resumen general de PrismaSalud IPS · {{ $citasHoy }} {{ $citasHoy === 1 ? 'cita' : 'citas' }} para hoy
                </p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                @if ($licenciaActual)
                    <span class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 ring-1 ring-white/25 text-white text-sm font-semibold rounded-lg">
                        @include('admin.partials.icono', ['icono' => 'eps', 'clase' => 'w-4 h-4'])
                        Plan {{ $licenciaActual->nombrePlan() }}
                    </span>
                @endif
                <a href="{{ route('cita.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-[#1A5C3A] text-sm font-bold rounded-lg hover:bg-teal-50 shadow-md transition">
                    @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-4 h-4'])
                    Ver citas
                </a>
            </div>
        </div>
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5"></div>
        <div class="absolute -right-2 top-16 w-24 h-24 rounded-full bg-white/5"></div>
    </div>

    {{-- Estadísticas --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @foreach ($estadisticas as $stat)
            <a href="{{ $stat['ruta'] }}"
               class="dashboard-entrada group bg-white border border-gray-200 rounded-2xl p-5 relative overflow-hidden hover:-translate-y-1 hover:shadow-lg hover:border-teal-200 transition-all duration-300"
               style="animation-delay: {{ $loop->index * 70 }}ms">
                <span class="absolute inset-x-0 top-0 h-1 {{ $acentosStats[$loop->index % count($acentosStats)]['barra'] }}"></span>
                <div class="w-10 h-10 rounded-lg {{ $acentosStats[$loop->index % count($acentosStats)]['icono'] }} flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                    @include('admin.partials.icono', ['icono' => $stat['icono'], 'clase' => 'w-5 h-5'])
                </div>
                <p class="text-2xl font-extrabold text-gray-900">
                    <span class="tabular-nums" data-contar="{{ $stat['numero'] }}">0</span>
                </p>
                <p class="text-xs text-gray-500 flex items-center gap-1">
                    {{ $stat['texto'] }}
                    @include('admin.partials.icono', ['icono' => 'flecha', 'clase' => 'w-3 h-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 transition-transform'])
                </p>
            </a>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Panel de citas --}}
        <div class="lg:col-span-2">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Panel de citas</h3>
                    <p class="text-sm text-gray-500">{{ $citasHoy }} {{ $citasHoy === 1 ? 'cita' : 'citas' }} para hoy · próximas citas programadas</p>
                </div>
                <a href="{{ route('cita.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                    Ver todas
                </a>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Paciente</th>
                            <th class="px-4 py-3">Médico</th>
                            <th class="px-4 py-3">Consultorio</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($citasProximas as $cita)
                            @php $esHoy = $cita->fecha->isToday(); @endphp
                            <tr class="hover:bg-teal-50/40 transition {{ $esHoy ? 'bg-teal-50/50' : '' }}">
                                <td class="px-4 py-3">
                                    <span class="font-medium text-gray-700">{{ $cita->fecha->format('d/m/Y') }}</span>
                                    <span class="block text-xs {{ $esHoy ? 'text-teal-600 font-semibold' : 'text-gray-400' }} capitalize">
                                        {{ $esHoy ? 'Hoy' : $cita->dia_semana }} · {{ substr($cita->hora_inicio, 0, 5) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-gray-900">
                                        {{ $cita->paciente ? $cita->paciente->nombres . ' ' . $cita->paciente->apellidos : 'Paciente no encontrado' }}
                                    </span>
                                    <span class="block text-xs text-gray-400">C.C. {{ $cita->cedula_usuario }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $cita->medico ? 'Dr(a). ' . $cita->medico->nombres . ' ' . $cita->medico->apellidos : '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $cita->consultorio->nombre ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full border border-teal-100">
                                        {{ $cita->estadoCita->nombre ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('cita.edit', $cita->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-teal-700 bg-teal-50 border border-teal-100 text-xs font-semibold hover:bg-teal-600 hover:text-white transition">
                                        Gestionar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-14 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-full bg-teal-50 flex items-center justify-center mb-3">
                                            @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-8 h-8 text-teal-400'])
                                        </div>
                                        <p class="text-sm font-semibold text-gray-700">No hay citas próximas programadas</p>
                                        <p class="text-xs text-gray-400 mt-1">Agenda una cita para que aparezca aquí.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Columna lateral --}}
        <div class="space-y-6">

            {{-- Accesos rápidos --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4">Accesos rápidos</h3>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($accesos as $acceso)
                        <a href="{{ $acceso['ruta'] }}"
                           class="group flex flex-col gap-2 border border-gray-200 rounded-xl p-3 hover:border-teal-300 hover:bg-teal-50/40 transition">
                            <span class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                @include('admin.partials.icono', ['icono' => $acceso['icono'], 'clase' => 'w-4 h-4'])
                            </span>
                            <span class="text-xs font-semibold text-gray-700">{{ $acceso['texto'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Mi licencia --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                <div class="flex items-center gap-2.5 mb-4 pb-4 border-b border-gray-100">
                    <span class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                        @include('admin.partials.icono', ['icono' => 'eps', 'clase' => 'w-4 h-4'])
                    </span>
                    <h3 class="font-bold text-gray-900">Mi licencia</h3>
                </div>

                @if ($licenciaActual)
                    @php
                        $restantes = $licenciaActual->diasRestantes();
                        $vencida = $licenciaActual->estaVencida();
                        $porVencer = $licenciaActual->estaPorVencer();
                        $estadoClases = $vencida
                            ? 'text-red-700 bg-red-50 border-red-100'
                            : ($porVencer ? 'text-amber-700 bg-amber-50 border-amber-100' : 'text-green-700 bg-green-50 border-green-100');
                        $totalDias = $licenciaActual->fecha_inicio && $licenciaActual->fecha_fin
                            ? $licenciaActual->fecha_inicio->diffInDays($licenciaActual->fecha_fin)
                            : 0;
                        $diasUsados = $licenciaActual->fecha_inicio
                            ? (int) floor($licenciaActual->fecha_inicio->diffInDays(now()->min($licenciaActual->fecha_fin ?? now())))
                            : 0;
                    @endphp
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <span class="text-sm font-bold text-gray-900">{{ $licenciaActual->nombrePlan() }}</span>
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border {{ $estadoClases }}">
                            {{ $vencida ? 'Vencida' : ($porVencer ? 'Por vencer' : ucfirst($licenciaActual->estado ?? 'Activa')) }}
                        </span>
                    </div>
                    <dl class="text-sm space-y-2.5">
                        <div class="flex justify-between gap-4"><dt class="text-gray-500">Inicio</dt><dd class="text-gray-700">{{ $licenciaActual->fecha_inicio ? $licenciaActual->fecha_inicio->format('d/m/Y') : '—' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-gray-500">Vence</dt><dd class="text-gray-700">{{ $licenciaActual->fecha_fin ? $licenciaActual->fecha_fin->format('d/m/Y') : '-' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-gray-500">Días usados</dt><dd class="text-gray-700">{{ $diasUsados }} de {{ $totalDias }}</dd></div>
                        @if ($restantes !== null)
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Días restantes</dt>
                                <dd class="font-semibold {{ $vencida ? 'text-red-600' : ($porVencer ? 'text-amber-600' : 'text-green-600') }}">{{ $restantes }}</dd>
                            </div>
                        @endif
                    </dl>
                    @if ($totalDias > 0)
                        <div class="mb-3 mt-2">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                                <span>Vigencia usada</span>
                                <span>{{ $diasUsados }} de {{ $totalDias }} días</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full {{ $vencida ? 'bg-red-500' : ($porVencer ? 'bg-amber-500' : 'bg-teal-500') }}"
                                     style="width: {{ min(100, $totalDias > 0 ? round(($diasUsados / $totalDias) * 100) : 0) }}%;"></div>
                            </div>
                        </div>
                    @endif
                    <a href="{{ route('admin.plan') }}"
                       class="mt-4 inline-flex w-full items-center justify-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                        Ver mi plan
                    </a>
                @else
                    <p class="text-sm text-gray-400">No tienes una licencia asociada.</p>
                    <a href="{{ route('admin.plan') }}"
                       class="mt-4 inline-flex w-full items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                        Ver planes
                    </a>
                @endif
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Contadores animados de las tarjetas de estadísticas
        document.addEventListener('DOMContentLoaded', function () {
            const contadores = document.querySelectorAll('[data-contar]');
            if (!contadores.length) return;

            const duracion = 900;

            contadores.forEach(function (contador) {
                const objetivo = parseInt(contador.getAttribute('data-contar'), 10) || 0;
                const inicio = performance.now();

                function paso(ahora) {
                    const progreso = Math.min((ahora - inicio) / duracion, 1);
                    contador.textContent = Math.round(objetivo * progreso).toLocaleString('es-CO');
                    if (progreso < 1) requestAnimationFrame(paso);
                }
                requestAnimationFrame(paso);
            });
        });
    </script>
@endpush
