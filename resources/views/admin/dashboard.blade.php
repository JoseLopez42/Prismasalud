{{-- Panel principal del Administrador. Hereda layout para el
     sidebar responsive con menú hamburguesa en móvil. --}}
@extends('admin.layout')

@section('titulo', 'Dashboard')
@section('encabezado', 'Panel del Administrador')

@section('contenido')

    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">
        Bienvenida, {{ $usuarioActual->nombres }}
    </h2>
    <p class="text-sm text-gray-500 mb-6">Resumen general de PrismaSalud IPS · hoy</p>

    {{-- Estadísticas: datos de ejemplo por ahora --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        @foreach ($estadisticas as $stat)
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 mb-3">
                    @include('admin.partials.icono', ['icono' => $stat['icono'], 'clase' => 'w-5 h-5'])
                </div>
                <p class="text-2xl font-extrabold text-gray-900">{{ $stat['numero'] }}</p>
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
            <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md hover:border-teal-200 transition">
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 flex-shrink-0">
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
                                default => '#',
                            };
                        @endphp
                        <a href="{{ $rutaTabla }}"
                           class="group inline-flex items-center gap-1 text-xs font-medium text-teal-700 bg-teal-50 border border-teal-100 px-3 py-1.5 rounded-full hover:bg-teal-600 hover:text-white hover:border-teal-600 hover:shadow-md transition">
                            {{ ucfirst($tabla) }}
                            @include('admin.partials.icono', ['icono' => 'flecha', 'clase' => 'w-3 h-3 opacity-0 group-hover:opacity-100'])
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

@endsection