{{--
    =====================================================================
    VISTA: admin/dashboard.blade.php
    Panel principal del Administrador (lo primero que ve después de
    iniciar sesión). Tiene 3 partes:
      1. Sidebar izquierdo (menú de módulos del sistema)
      2. Topbar (título de la página + datos del usuario logueado)
      3. Contenido: tarjetas de estadísticas + accesos directos a los
         módulos de la base de datos (Usuarios y Roles, Ubicaciones,
         EPS, Afiliaciones, Citas Médicas, Historia Clínica,
         Procedimientos, Médicos y Consultorios, Farmacia).

    Por ahora casi todos los enlaces del sidebar y de las tarjetas de
    módulos apuntan con "#" porque esas secciones todavía no están
    construidas; se van reemplazando por su ruta real a medida que
    se arma cada módulo.
    =====================================================================
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Administrador - PrismaSalud IPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Verde institucional del sidebar, tal como lo definiste: #1A5C3A */
        .sidebar-admin { background-color: #1A5C3A; }
        .sidebar-admin .nav-item { color: rgba(255, 255, 255, 0.75); transition: color .15s ease, background-color .15s ease; }
        .sidebar-admin .nav-item:hover { background-color: rgba(255, 255, 255, 0.08); color: #60a5fa; } /* azul, como el logo */
        .sidebar-admin .nav-item.activo { background-color: rgba(255, 255, 255, 0.12); color: #60a5fa; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <div class="flex min-h-screen">

        {{-- =================================================
             1. SIDEBAR
             Menú lateral fijo con los módulos del sistema.
             El módulo activo (Dashboard) se marca con la clase
             "activo" para resaltarlo.
        ================================================= --}}
        <aside class="sidebar-admin w-64 flex-shrink-0 text-white flex flex-col">

            {{-- Logo del panel --}}
            <div class="flex items-center gap-2 px-5 py-5 border-b border-white/10">
                <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud" class="w-8 h-8 object-contain">
                <div class="leading-tight">
                    <p class="font-bold text-sm">PrismaSalud</p>
                    <p class="text-xs text-white/60">Panel Administrador</p>
                </div>
            </div>

            {{-- Ítems del menú.
                 NOTA: se recorren desde un arreglo de PHP para no
                 repetir el mismo bloque de HTML por cada enlace.
                 - Si un ítem tiene "tablas", se muestra como un
                   desplegable (<details>) con el triangulito y sus
                   subopciones debajo.
                 - Si "tablas" es null, es un enlace simple (como
                   Dashboard, Reportes o Configuración). --}}
            @php
                $menu = [
                    ['icono' => '🏠', 'texto' => 'Dashboard',              'ruta' => route('admin.dashboard'), 'activo' => true,  'tablas' => null],
                    ['icono' => '👥', 'texto' => 'Usuarios y Roles',        'ruta' => '#', 'activo' => false, 'tablas' => ['usuario', 'rol', 'tipo_documento']],
                    ['icono' => '📍', 'texto' => 'Ubicaciones',             'ruta' => '#', 'activo' => false, 'tablas' => ['departamento', 'ciudad', 'sede']],
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
                        {{-- Ítem simple, sin submenú --}}
                        <a href="{{ $item['ruta'] }}"
                           class="nav-item flex items-center gap-3 px-5 py-2.5 text-sm font-medium {{ $item['activo'] ? 'activo' : '' }}">
                            <span>{{ $item['icono'] }}</span>
                            <span>{{ $item['texto'] }}</span>
                        </a>
                    @else
                        {{-- Ítem con submenú tipo "flyout": el panel con las
                             tablas aparece pegado al lado derecho SOLO con
                             el mouse encima (hover), sin necesidad de clic.
                             "group" + "group-hover" son utilidades de
                             Tailwind: cuando el mouse está sobre el <div>
                             padre (class="group"), todo lo que tenga
                             "group-hover:..." adentro reacciona. --}}
                        <div class="relative group">
                            <div class="nav-item flex items-center gap-3 px-5 py-2.5 text-sm font-medium cursor-default">
                                <span>{{ $item['icono'] }}</span>
                                <span class="flex-1">{{ $item['texto'] }}</span>
                                <span class="text-white/40 group-hover:text-white">›</span>
                            </div>

                            {{-- Panel flotante con las tablas del módulo.
                                 Oculto por defecto (opacity-0 invisible) y
                                 visible al pasar el mouse sobre el ítem
                                 padre (group-hover:opacity-100 visible). --}}
                            <div class="absolute left-full top-0 ml-1 min-w-[190px] rounded-md shadow-lg py-2 z-50
                                        bg-[#1A5C3A] border border-white/10
                                        opacity-0 invisible
                                        group-hover:opacity-100 group-hover:visible
                                        transition duration-150">
                                @foreach ($item['tablas'] as $tabla)
                                    <a href="#" class="block px-4 py-2 text-sm text-white/80 hover:text-[#60a5fa] hover:bg-white/10 transition">
                                        {{ $tabla }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>
        </aside>

        {{-- =================================================
             CONTENIDO PRINCIPAL (topbar + tarjetas + módulos)
        ================================================= --}}
        <div class="flex-1">

            {{-- =============================================
                 2. TOPBAR
                 Título de la página + insignia de rol + datos
                 del usuario logueado + botón de salir.
            ============================================= --}}
            <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
                <h1 class="text-lg font-bold text-gray-900">Panel del Administrador</h1>

                <div class="flex items-center gap-4">
                    <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-3 py-1 rounded-full">
                        Administrador
                    </span>

                    {{-- Iniciales del usuario logueado (sacadas de su nombre) --}}
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

                    {{-- Botón "Salir": hace POST a logout (con @csrf, como todo POST en Laravel) --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="text-sm font-semibold text-red-600 bg-red-50 px-3 py-1.5 rounded-md hover:bg-red-100">
                            Salir
                        </button>
                    </form>
                </div>
            </header>

            {{-- =============================================
                 3. CONTENIDO: bienvenida + estadísticas + módulos
            ============================================= --}}
            <main class="p-8">

                <h2 class="text-2xl font-bold text-gray-900 mb-1">
                    Bienvenida, {{ $usuarioActual->nombres }} 👋
                </h2>
                <p class="text-sm text-gray-500 mb-6">Resumen general de PrismaSalud IPS · hoy</p>

                {{-- Tarjetas de estadísticas (vienen de DashboardController,
                     por ahora con datos de ejemplo/placeholder) --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                    @foreach ($estadisticas as $stat)
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="text-xl mb-2">{{ $stat['icono'] }}</div>
                            <p class="text-2xl font-extrabold text-gray-900">{{ $stat['numero'] }}</p>
                            <p class="text-xs text-gray-500">{{ $stat['texto'] }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Sección de accesos directos a los módulos de la BD.
                     Cada tarjeta agrupa varias tablas relacionadas.
                     Por ahora los "tags" de tablas son solo informativos
                     (sin enlace); se van conectando a medida que se
                     construye cada módulo. --}}
                <h3 class="text-base font-bold text-gray-900 mb-1">Gestión de la base de datos</h3>
                <p class="text-sm text-gray-500 mb-4">Acceso directo a las tablas del sistema, agrupadas por módulo</p>

                @php
                    $modulos = [
                        ['icono' => '👥', 'titulo' => 'Usuarios y Roles',        'cantidad' => 3, 'tablas' => ['usuario', 'rol', 'tipo_documento']],
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
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="flex items-center gap-2 mb-1">
                                <span>{{ $modulo['icono'] }}</span>
                                <p class="font-semibold text-gray-900 text-sm">{{ $modulo['titulo'] }}</p>
                            </div>
                            <p class="text-xs text-gray-400 mb-3">{{ $modulo['cantidad'] }} tablas</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($modulo['tablas'] as $tabla)
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-md">{{ $tabla }}</span>
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