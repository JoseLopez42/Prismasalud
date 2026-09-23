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

        $dias = ['Sunday' => 'domingo', 'Monday' => 'lunes', 'Tuesday' => 'martes', 'Wednesday' => 'miércoles',
                 'Thursday' => 'jueves', 'Friday' => 'viernes', 'Saturday' => 'sábado'];
        $meses = [1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
                 7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'];
        $fechaHoy = $dias[now()->format('l')] . ', ' . now()->format('j') . ' de ' . $meses[(int) now()->format('n')] . ' de ' . now()->format('Y');

        // Variedad de acentos para las tarjetas de estadísticas
        $acentosStats = [
            ['barra' => 'bg-gradient-to-r from-teal-500 to-teal-300', 'icono' => 'bg-teal-50 text-teal-600'],
            ['barra' => 'bg-gradient-to-r from-blue-500 to-sky-300',   'icono' => 'bg-blue-50 text-blue-600'],
            ['barra' => 'bg-gradient-to-r from-amber-500 to-amber-300','icono' => 'bg-amber-50 text-amber-600'],
            ['barra' => 'bg-gradient-to-r from-violet-500 to-violet-300','icono' => 'bg-violet-50 text-violet-600'],
        ];
    @endphp

    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">
        {{ $saludo }}, {{ $usuarioActual->nombres }}
    </h2>
    <p class="text-sm text-gray-500 mb-6">{{ $fechaHoy }} · Resumen general de PrismaSalud IPS</p>

    {{-- Estadísticas: contadores animados + acento de color por tarjeta --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        @foreach ($estadisticas as $stat)
            <div class="dashboard-entrada bg-white border border-gray-200 rounded-xl p-5 relative overflow-hidden group hover:-translate-y-1 hover:shadow-lg hover:border-teal-200 transition-all duration-300"
                 style="animation-delay: {{ $loop->index * 70 }}ms">
                <span class="absolute inset-x-0 top-0 h-1 {{ $acentosStats[$loop->index % count($acentosStats)]['barra'] }}"></span>
                <div class="w-10 h-10 rounded-lg {{ $acentosStats[$loop->index % count($acentosStats)]['icono'] }} flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                    @include('admin.partials.icono', ['icono' => $stat['icono'], 'clase' => 'w-5 h-5'])
                </div>
                <p class="text-2xl font-extrabold text-gray-900">
                    <span class="tabular-nums" data-contar="{{ $stat['numero'] }}">0</span>
                </p>
                <p class="text-xs text-gray-500">{{ $stat['texto'] }}</p>
            </div>
        @endforeach
    </div>

    <h3 class="text-base font-bold text-gray-900 mb-1">Gestión de la base de datos</h3>
    <p class="text-sm text-gray-500 mb-4">Acceso directo a las tablas del sistema, agrupadas por módulo</p>

    @php
        $modulos = [
            ['icono' => 'usuarios',     'titulo' => 'Usuarios y Roles',        'cantidad' => 3, 'tablas' => ['usuarios', 'roles', 'tipos de documento']],
            ['icono' => 'ubicaciones',  'titulo' => 'Ubicaciones',             'cantidad' => 3, 'tablas' => ['departamento', 'ciudad', 'sede']],
            ['icono' => 'eps',          'titulo' => 'EPS',                    'cantidad' => 2, 'tablas' => ['eps', 'regimen']],
            ['icono' => 'afiliaciones', 'titulo' => 'Afiliaciones',           'cantidad' => 4, 'tablas' => ['afiliacion', 'beneficiarios', 'detalle_afiliado_afiliacion', 'estado']],
            ['icono' => 'citas',        'titulo' => 'Citas Médicas',          'cantidad' => 6, 'tablas' => ['cita', 'tipo_cita', 'estado_cita', 'Enfermedad', 'Tipo Enfermedad', 'Historia Clínica(solo admin)']],
            ['icono' => 'historia',     'titulo' => 'Historia Clínica',        'cantidad' => 3, 'tablas' => ['historia_clinica', 'diagnostico', 'enfermedad']],
            ['icono' => 'procedimientos','titulo' => 'Procedimientos',         'cantidad' => 3, 'tablas' => ['procedimiento', 'tipo_procedimiento', 'detalle_procedimiento']],
            ['icono' => 'medicos',      'titulo' => 'Médicos y Consultorios',  'cantidad' => 4, 'tablas' => ['medico', 'especialidad', 'consultorio', 'horario_medico']],
            ['icono' => 'farmacia',     'titulo' => 'Farmacia y Medicamentos', 'cantidad' => 4, 'tablas' => ['medicamentos', 'entrega_medicamento', 'Fecha Vencimiento', 'estado_entrega', 'farmacia']],
        ];
    @endphp

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($modulos as $modulo)
            <div class="dashboard-entrada bg-white border border-gray-200 rounded-xl p-5 hover:-translate-y-1 hover:shadow-lg hover:border-teal-300 transition-all duration-300"
                 style="animation-delay: {{ 300 + $loop->index * 60 }}ms">
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                        @include('admin.partials.icono', ['icono' => $modulo['icono'], 'clase' => 'w-5 h-5'])
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $modulo['titulo'] }}</p>
                </div>
                <p class="text-xs text-gray-400 mb-3">{{ $modulo['cantidad'] }} tablas</p>

                {{-- Botón pill por tabla, listo para su ruta real --}}
                <div class="flex flex-wrap gap-2">
                    @foreach ($modulo['tablas'] as $tabla)
                        @php
                            $rutaTabla = match ($tabla) {
                                'departamento', 'departamentos' => route('departamentos.index'),
                                'ciudad', 'ciudades' => route('ciudades.index'),
                                'sede', 'sedes' => route('sedes.index'),
                                'usuario', 'usuarios' => route('usuarios.index'),
                                'tipo de documento', 'tipos de documento' => route('tipos_documento.index'),
                                'rol', 'roles' => route('roles.index'),
                                'eps' => route('eps.index'),
                                'regimen' => route('regimen.index'),
                                default => '#',
                            };
                        @endphp
                        <a href="{{ $rutaTabla }}"
                           class="group inline-flex items-center gap-1 text-xs font-medium text-teal-700 bg-teal-50 border border-teal-100 px-3 py-1.5 rounded-full hover:bg-teal-600 hover:text-white hover:border-teal-600 hover:shadow-md hover:-translate-y-0.5 transition">
                            {{ ucfirst($tabla) }}
                            @include('admin.partials.icono', ['icono' => 'flecha', 'clase' => 'w-3 h-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 transition-transform'])
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
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