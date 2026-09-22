{{-- Panel principal del Administrador --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Administrador - PrismaSalud IPS</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Verde institucional del sidebar */
        .sidebar-admin { background-color: #1A5C3A; }
        .sidebar-admin .nav-item { color: rgba(255, 255, 255, 0.75); transition: color .15s ease, background-color .15s ease; }
        .sidebar-admin .nav-item:hover { background-color: rgba(255, 255, 255, 0.08); color: #60a5fa; }
        .sidebar-admin .nav-item.activo { background-color: rgba(255, 255, 255, 0.12); color: #60a5fa; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside class="sidebar-admin w-64 flex-shrink-0 text-white flex flex-col">

            {{-- Logo --}}
            <div class="flex items-center gap-2 px-5 py-5 border-b border-white/10">
                <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud" class="w-8 h-8 object-contain">
                <div class="leading-tight">
                    <p class="font-bold text-sm">PrismaSalud</p>
                    <p class="text-xs text-white/60">Panel Administrador</p>
                </div>
            </div>

            {{-- tablas = null -> enlace simple; tablas = array -> submenú --}}
            @php
                $menu = [
                    ['icono' => '🏠', 'texto' => 'Dashboard',              'ruta' => route('admin.dashboard'), 'activo' => true,  'tablas' => null],
                    ['icono' => '👥', 'texto' => 'Usuarios y Roles',        'ruta' => route('usuarios.index'), 'activo' => false, 'tablas' => ['usuarios', 'roles', 'tipos de documento']],
                    ['icono' => '📍', 'texto' => 'Ubicaciones',             'ruta' => route('departamentos.index'), 'activo' => false, 'tablas' => ['departamento', 'ciudad', 'sede']],
                    ['icono' => '🗂️', 'texto' => 'EPS',                    'ruta' => '#', 'activo' => false, 'tablas' => ['eps', 'regimen']],
                    ['icono' => '🗂️', 'texto' => 'Afiliaciones',           'ruta' => '#', 'activo' => false, 'tablas' => ['afiliacion', 'beneficiarios', 'detalle_afiliado_afiliacion', 'estado']],
                    ['icono' => '🩺', 'texto' => 'Médicos y Consultorios',  'ruta' => '#', 'activo' => false, 'tablas' => ['medico', 'especialidad', 'consultorio', 'horario_medico']],
                    ['icono' => '🗓️', 'texto' => 'Citas Médicas',          'ruta' => '#', 'activo' => false, 'tablas' => ['cita', 'tipo_cita', 'estado_cita', 'Enfermedad', 'Tipo Enfermedad', 'Historia Clínica(solo admin)']],
                    ['icono' => '📄', 'texto' => 'Historia Clínica',        'ruta' => '#', 'activo' => false, 'tablas' => ['historia_clinica', 'diagnostico', 'enfermedad']],
                    ['icono' => '⚙️', 'texto' => 'Procedimientos',         'ruta' => '#', 'activo' => false, 'tablas' => ['procedimiento', 'tipo_procedimiento', 'detalle_procedimiento']],
                    ['icono' => '💊', 'texto' => 'Farmacia y Medicamentos', 'ruta' => '#', 'activo' => false, 'tablas' => ['medicamentos', 'entrega_medicamento', 'Fecha Vencimiento', 'estado_entrega', 'farmacia']],
                    ['icono' => '📊', 'texto' => 'Reportes',                'ruta' => '#', 'activo' => false, 'tablas' => null],
                    ['icono' => '🔧', 'texto' => 'Configuración',          'ruta' => '#', 'activo' => false, 'tablas' => null],
                ];
            @endphp

            <nav class="flex-1 py-4 space-y-1">
                @foreach ($menu as $item)
                    @if (is_null($item['tablas']))
                        <a href="{{ $item['ruta'] }}"
                           class="nav-item flex items-center gap-3 px-5 py-2.5 text-sm font-medium {{ $item['activo'] ? 'activo' : '' }}">
                            <span>{{ $item['icono'] }}</span>
                            <span>{{ $item['texto'] }}</span>
                        </a>
                    @else
                        {{-- Submenú flyout: aparece a la derecha solo con hover --}}
                        <div class="relative group">
                            <a href="{{ $item['ruta'] }}"
                               class="nav-item flex items-center gap-3 px-5 py-2.5 text-sm font-medium">
                                <span>{{ $item['icono'] }}</span>
                                <span class="flex-1">{{ $item['texto'] }}</span>
                                <span class="text-white/40 group-hover:text-white">›</span>
                            </a>

                            <div class="absolute left-full top-0 ml-1 min-w-[190px] rounded-md shadow-lg py-2 z-50
                                        bg-[#1A5C3A] border border-white/10
                                        opacity-0 invisible
                                        group-hover:opacity-100 group-hover:visible
                                        transition duration-150">
                                @foreach ($item['tablas'] as $tabla)
                                    @php
                                        // Mapeo temporal: mientras no todas las
                                        // tablas tienen su propia vista, aquí
                                        // decimos a cuál sí redirigir.
                                        $rutaTabla = match ($tabla) {
                                            'departamento', 'departamentos' => route('departamentos.index'),
                                            'ciudad', 'ciudades' => route('ciudades.index'),
                                            'sede', 'sedes' => route('sedes.index'),
                                            'usuario', 'usuarios' => route('usuarios.index'),
                                            'tipo de documento', 'tipos de documento' => route('tipos_documento.index'),
                                            default => '#',
                                        };
                                    @endphp
                                    <a href="{{ $rutaTabla }}" class="block px-4 py-2 text-sm text-white/80 hover:text-[#60a5fa] hover:bg-white/10 transition">
                                        {{ ucfirst($tabla) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>
        </aside>

        {{-- Contenido principal --}}
        <div class="flex-1">

            {{-- Topbar --}}
            <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
                <h1 class="text-lg font-bold text-gray-900">Panel del Administrador</h1>

                <div class="flex items-center gap-4">
                    <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-3 py-1 rounded-full">
                        Administrador
                    </span>

                    {{-- Iniciales del usuario logueado --}}
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr($usuarioActual->nombres, 0, 1) . substr($usuarioActual->apellidos, 0, 1)) }}
                        </div>
                        <div class="leading-tight">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $usuarioActual->nombres }} {{ $usuarioActual->apellidos }}
                            </p>
                            <p class="text-xs text-gray-400">Documento verificado</p>
                        </div>
                    </div>

                    {{-- Logout por POST --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="text-sm font-semibold text-red-600 bg-red-50 px-3 py-1.5 rounded-md hover:bg-red-100">
                            Salir
                        </button>
                    </form>
                </div>
            </header>

            {{-- Bienvenida + estadísticas + módulos --}}
            <main class="p-8">

                <h2 class="text-2xl font-bold text-gray-900 mb-1">
                    Bienvenida, {{ $usuarioActual->nombres }} 👋
                </h2>
                <p class="text-sm text-gray-500 mb-6">Resumen general de PrismaSalud IPS · hoy</p>

                {{-- Estadísticas: datos de ejemplo por ahora --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                    @foreach ($estadisticas as $stat)
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="text-xl mb-2">{{ $stat['icono'] }}</div>
                            <p class="text-2xl font-extrabold text-gray-900">{{ $stat['numero'] }}</p>
                            <p class="text-xs text-gray-500">{{ $stat['texto'] }}</p>
                        </div>
                    @endforeach
                </div>

                <h3 class="text-base font-bold text-gray-900 mb-1">Gestión de la base de datos</h3>
                <p class="text-sm text-gray-500 mb-4">Acceso directo a las tablas del sistema, agrupadas por módulo</p>

                @php
                    $modulos = [
                        ['icono' => '👥', 'titulo' => 'Usuarios y Roles',        'cantidad' => 3, 'tablas' => ['usuarios', 'roles', 'tipos de documento']],
                        ['icono' => '📍', 'titulo' => 'Ubicaciones',             'cantidad' => 3, 'tablas' => ['departamento', 'ciudad', 'sede']],
                        ['icono' => '🗂️', 'titulo' => 'EPS',                    'cantidad' => 2, 'tablas' => ['eps', 'regimen']],
                        ['icono' => '🗂️', 'titulo' => 'Afiliaciones',           'cantidad' => 4, 'tablas' => ['afiliacion', 'beneficiarios', 'detalle_afiliado_afiliacion', 'estado']],
                        ['icono' => '🗓️', 'titulo' => 'Citas Médicas',          'cantidad' => 6, 'tablas' => ['cita', 'tipo_cita', 'estado_cita', 'Enfermedad', 'Tipo Enfermedad', 'Historia Clínica(solo admin)']],
                        ['icono' => '📄', 'titulo' => 'Historia Clínica',        'cantidad' => 3, 'tablas' => ['historia_clinica', 'diagnostico', 'enfermedad']],
                        ['icono' => '⚙️', 'titulo' => 'Procedimientos',         'cantidad' => 3, 'tablas' => ['procedimiento', 'tipo_procedimiento', 'detalle_procedimiento']],
                        ['icono' => '🩺', 'titulo' => 'Médicos y Consultorios',  'cantidad' => 4, 'tablas' => ['medico', 'especialidad', 'consultorio', 'horario_medico']],
                        ['icono' => '💊', 'titulo' => 'Farmacia y Medicamentos', 'cantidad' => 4, 'tablas' => ['medicamentos', 'entrega_medicamento', 'Fecha Vencimiento', 'estado_entrega', 'farmacia']],
                    ];
                @endphp

                <div class="grid md:grid-cols-3 gap-4">
                    @foreach ($modulos as $modulo)
                        <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md hover:border-teal-200 transition">
                            <div class="flex items-center gap-2 mb-1">
                                <span>{{ $modulo['icono'] }}</span>
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
                                        <span class="opacity-0 group-hover:opacity-100">→</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </main>
        </div>
    </div>

</body>
</html>