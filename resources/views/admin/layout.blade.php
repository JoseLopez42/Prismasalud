{{-- Layout base para todas las vistas del panel de Administrador --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Panel Administrador') - PrismaSalud IPS</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Fondo suave y con un toque de color: la zona de contenido se
           diferencia de las tarjetas blancas sin llegar a verse plana. */
        .contenido-admin {
            background: linear-gradient(180deg, #f2fbf7 0%, #f8fafc 100%);
            min-height: 100vh;
        }

        /* Las tarjetas de listado usan overflow-x-auto (para que el menú de
           acciones no se corte al desplegarse), así que redondeamos la
           cabecera de la tabla a mano para conservar la esquina superior. */
        #tabla-listado thead th:first-child { border-top-left-radius: 1rem; }
        #tabla-listado thead th:last-child { border-top-right-radius: 1rem; }

        .sidebar-admin { background-color: #1A5C3A; }
        .sidebar-admin .nav-item { color: rgba(255, 255, 255, 0.75); transition: color .15s ease, background-color .15s ease; }
        .sidebar-admin .nav-item:hover { background-color: rgba(255, 255, 255, 0.08); color: #60a5fa; }
        .sidebar-admin .nav-item.activo { background-color: rgba(255, 255, 255, 0.12); color: #60a5fa; }

        /* Sidebar como drawer en móvil, fijo en escritorio */
        @media (max-width: 767px) {
            .sidebar-admin {
                position: fixed;
                top: 0; left: 0;
                height: 100%;
                width: 260px;
                transform: translateX(-100%);
                transition: transform .3s ease;
                z-index: 70;
            }
            .sidebar-admin.abierto {
                transform: translateX(0);
            }
        }

        .overlay-sidebar {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity .25s ease, visibility .25s ease;
            z-index: 60;
        }
        .overlay-sidebar.abierto {
            opacity: 1;
            visibility: visible;
        }

        /* Flechita del acordeón móvil: gira 90° cuando está abierto */
        .flecha-submenu-movil {
            transition: transform .2s ease;
        }
        .flecha-submenu-movil.rotada {
            transform: rotate(90deg);
        }

        /* Modal de confirmación propio (activar/desactivar) */
        #modal-confirmar {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transition: opacity .2s ease, visibility .2s ease;
        }
        #modal-confirmar.abierto {
            opacity: 1;
            visibility: visible;
        }
        #modal-confirmar .tarjeta-modal {
            transform: scale(.95) translateY(8px);
            transition: transform .2s ease;
        }
        #modal-confirmar.abierto .tarjeta-modal {
            transform: scale(1) translateY(0);
        }

        /* Fila oculta por el buscador rápido */
        tr.oculto {
            display: none;
        }
    </style>
    @stack('estilos')
</head>
<body class="bg-gray-50 min-h-screen">

    <div class="flex min-h-screen">

        <div id="overlay-sidebar" class="overlay-sidebar md:hidden"></div>

        {{-- Sidebar --}}
        <aside id="sidebar-admin" class="sidebar-admin w-64 flex-shrink-0 text-white flex flex-col">

            <div class="flex items-center justify-between gap-2 px-5 py-5 border-b border-white/10">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud" class="w-8 h-8 object-contain">
                    <div class="leading-tight">
                        <p class="font-bold text-sm">PrismaSalud</p>
                        <p class="text-xs text-white/60">Panel Administrador</p>
                    </div>
                </div>
                <button id="boton-cerrar-sidebar" class="md:hidden text-white/70 hover:text-white" aria-label="Cerrar menú">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- tablas = null -> enlace simple; tablas = array -> submenú
                 (flyout con hover en escritorio, acordeón con toque en móvil) --}}
            @php
                $menu = [
                    ['icono' => 'dashboard',     'texto' => 'Dashboard',              'ruta' => route('admin.dashboard'),     'clave' => 'dashboard',    'tablas' => null],
                    ['icono' => 'licencias',     'texto' => 'Mi Plan',                'ruta' => route('admin.plan'),          'clave' => 'plan',         'tablas' => null],
                    ['icono' => 'usuarios',      'texto' => 'Usuarios y Roles',        'ruta' => route('usuarios.index'),      'clave' => 'usuarios',     'tablas' => ['usuarios', 'roles', 'tipos de documento']],
                    ['icono' => 'ubicaciones',   'texto' => 'Ubicaciones',             'ruta' => route('departamentos.index'), 'clave' => 'ubicaciones',  'tablas' => ['departamentos', 'ciudades', 'sedes']],
                    ['icono' => 'eps',           'texto' => 'EPS',                    'ruta' => route('eps.index'), 'clave' => 'eps', 'tablas' => ['eps', 'regimen']],
                    ['icono' => 'afiliaciones',  'texto' => 'Afiliaciones',           'ruta' => route('afiliaciones.index'), 'clave' => 'afiliaciones',   'tablas' => ['afiliacion', 'beneficiarios', 'detalle de afiliacion', 'estado']],
                    ['icono' => 'medicos',       'texto' => 'Médicos y Consultorios',  'ruta' => route('medico.index'), 'clave' => 'medicos',        'tablas' => ['medico', 'especialidad', 'consultorio', 'horario_medico']],
                    ['icono' => 'citas',         'texto' => 'Citas Médicas',          'ruta' => route('cita.index'), 'clave' => 'citas',          'tablas' => ['cita', 'tipo_cita', 'estado_cita']],
                    ['icono' => 'historia',      'texto' => 'Historia Clínica',        'ruta' => route('historia.index'), 'clave' => 'historia',       'tablas' => null],
                    ['icono' => 'procedimientos','texto' => 'Procedimientos',         'ruta' => route('procedimiento.index'), 'clave' => 'procedimientos', 'tablas' => ['procedimiento', 'tipo_procedimiento']],
                    ['icono' => 'farmacia',      'texto' => 'Farmacia y Medicamentos', 'ruta' => route('medicamento.index'), 'clave' => 'farmacia',       'tablas' => ['medicamentos']],
                    ['icono' => 'reportes',      'texto' => 'Reportes',                'ruta' => '#', 'clave' => 'reportes',       'tablas' => null],
                    ['icono' => 'configuracion', 'texto' => 'Configuración',          'ruta' => '#', 'clave' => 'configuracion',  'tablas' => null],
                    ['icono' => 'api',           'texto' => 'API',                    'ruta' => route('api.medicamentos'), 'clave' => 'api', 'tablas' => null],

                    // Última opción del menú: vista previa de los recordatorios por WhatsApp.
                    ['icono' => 'mensaje',       'texto' => 'Recordatorios',          'ruta' => route('admin.recordatorios'), 'clave' => 'recordatorios', 'tablas' => null],
                ];
            @endphp

            <nav class="flex-1 py-4 space-y-1 overflow-y-auto md:overflow-visible">
                @foreach ($menu as $item)
                    @if (is_null($item['tablas']))
                        <a href="{{ $item['ruta'] }}"
                           class="nav-item flex items-center gap-3 px-5 py-2.5 text-sm font-medium {{ ($seccionActiva ?? '') === $item['clave'] ? 'activo' : '' }}">
                            @include('admin.partials.icono', ['icono' => $item['icono']])
                            <span>{{ $item['texto'] }}</span>
                        </a>
                    @else
                        <div class="relative group">
                            {{-- Fila principal: el texto/ícono navegan directo;
                                 la flechita es un botón aparte, solo para
                                 abrir/cerrar el acordeón en móvil. --}}
                            <div class="flex items-center item-con-submenu {{ ($seccionActiva ?? '') === $item['clave'] ? 'activo' : '' }}">
                                <a href="{{ $item['ruta'] }}"
                                   class="nav-item flex-1 flex items-center gap-3 px-5 py-2.5 text-sm font-medium">
                                    @include('admin.partials.icono', ['icono' => $item['icono']])
                                    <span>{{ $item['texto'] }}</span>
                                </a>
                                <button type="button"
                                        class="boton-submenu-movil md:hidden pr-5 pl-2 text-white/50 hover:text-white"
                                        data-target="submenu-movil-{{ $loop->index }}"
                                        aria-label="Ver opciones de {{ $item['texto'] }}">
                                    <span class="flecha-submenu-movil inline-block">›</span>
                                </button>
                                {{-- Flechita decorativa, solo en escritorio (el hover la resalta) --}}
                                <span class="hidden md:inline-block pr-5 text-white/40 group-hover:text-white">›</span>
                            </div>

                            {{-- ACORDEÓN MÓVIL: lista simple debajo del ítem,
                                 oculta hasta que se toca la flechita. Solo
                                 existe en pantallas chicas (md:hidden). --}}
                            <div id="submenu-movil-{{ $loop->index }}" class="hidden md:hidden pl-14 pb-2 space-y-1">
                                @foreach ($item['tablas'] as $tabla)
                                    @php
                                        $rutaTabla = match ($tabla) {
                                            'departamento', 'departamentos' => route('departamentos.index'),
                                            'ciudad', 'ciudades' => route('ciudades.index'),
                                            'sede', 'sedes' => route('sedes.index'),
                                            'usuario', 'usuarios' => route('usuarios.index'),
                                            'tipo de documento', 'tipos de documento' => route('tipos_documento.index'),
                                            'eps' => route('eps.index'),
                                            'regimen' => route('regimen.index'),
                                            'medico' => route('medico.index'),
                                            'especialidad' => route('especialidad.index'),
                                            'consultorio' => route('consultorio.index'),
                                            'horario_medico' => route('horario_medico.index'),
                                            'procedimiento' => route('procedimiento.index'),
                                            'tipo_procedimiento' => route('tipo_procedimiento.index'),
                                            'cita' => route('cita.index'),
                                            'tipo_cita' => route('tipo_cita.index'),
                                            'estado_cita' => route('estado_cita.index'),
                                            'medicamento', 'medicamentos' => route('medicamento.index'),
                                            'afiliacion' => route('afiliaciones.index'),
                                            'beneficiarios' => route('beneficiarios.index'),
                                            'detalle de afiliacion' => route('afiliaciones.detalle.lista'),
                                            'estado' => route('estados.index'),
                                            default => '#',
                                        };
                                    @endphp
                                    <a href="{{ $rutaTabla }}" class="block py-1.5 text-sm text-white/70 hover:text-[#60a5fa]">
                                        {{ ucfirst($tabla) }}
                                    </a>
                                @endforeach
                            </div>

                            {{-- FLYOUT ESCRITORIO: panel a la derecha, solo con hover.
                                 Nunca se muestra en móvil (hidden md:block). --}}
                            <div class="hidden md:block absolute left-full top-0 min-w-[190px] rounded-md shadow-lg py-2 z-50
                                        bg-[#1A5C3A] border border-white/10
                                        opacity-0 invisible pointer-events-none
                                        group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto
                                        group-focus-within:opacity-100 group-focus-within:visible group-focus-within:pointer-events-auto
                                        transition-opacity duration-150">
                                @foreach ($item['tablas'] as $tabla)
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
                                            'medico' => route('medico.index'),
                                            'especialidad' => route('especialidad.index'),
                                            'consultorio' => route('consultorio.index'),
                                            'horario_medico' => route('horario_medico.index'),
                                            'procedimiento' => route('procedimiento.index'),
                                            'tipo_procedimiento' => route('tipo_procedimiento.index'),
                                            'cita' => route('cita.index'),
                                            'tipo_cita' => route('tipo_cita.index'),
                                            'estado_cita' => route('estado_cita.index'),
                                            'medicamento', 'medicamentos' => route('medicamento.index'),
                                            'afiliacion' => route('afiliaciones.index'),
                                            'beneficiarios' => route('beneficiarios.index'),
                                            'detalle de afiliacion' => route('afiliaciones.detalle.lista'),
                                            'estado' => route('estados.index'),
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

        {{-- Contenido --}}
        <div class="flex-1 w-full">

            <header class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 flex items-center justify-between gap-3">

                <div class="flex items-center gap-3">
                    <button id="boton-abrir-sidebar" class="md:hidden text-gray-600" aria-label="Abrir menú">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-base md:text-lg font-bold text-gray-900">@yield('encabezado', 'Panel del Administrador')</h1>
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <span class="hidden sm:inline text-xs font-semibold text-teal-700 bg-teal-50 px-3 py-1 rounded-full">
                        Administrador
                    </span>

                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr($_SESSION['usuario_nombres'] ?? 'A', 0, 1) . substr($_SESSION['usuario_apellidos'] ?? '', 0, 1)) }}
                        </div>
                        <p class="hidden sm:block text-sm font-semibold text-gray-900">
                            {{ $_SESSION['usuario_nombres'] ?? '' }} {{ $_SESSION['usuario_apellidos'] ?? '' }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="text-sm font-semibold text-red-600 bg-red-50 px-3 py-1.5 rounded-md hover:bg-red-100">
                            Salir
                        </button>
                    </form>
                </div>
            </header>

            <main class="contenido-admin p-4 md:p-8">
                @yield('contenido')
            </main>
        </div>
    </div>

    <script>
        // Abre/cierra el sidebar en móvil (drawer + overlay)
        const sidebarAdmin = document.getElementById('sidebar-admin');
        const overlaySidebar = document.getElementById('overlay-sidebar');
        const botonAbrirSidebar = document.getElementById('boton-abrir-sidebar');
        const botonCerrarSidebar = document.getElementById('boton-cerrar-sidebar');

        function abrirSidebar() {
            sidebarAdmin.classList.add('abierto');
            overlaySidebar.classList.add('abierto');
        }
        function cerrarSidebar() {
            sidebarAdmin.classList.remove('abierto');
            overlaySidebar.classList.remove('abierto');
        }

        botonAbrirSidebar.addEventListener('click', abrirSidebar);
        botonCerrarSidebar.addEventListener('click', cerrarSidebar);
        overlaySidebar.addEventListener('click', cerrarSidebar);

        // Acordeón de submenús en móvil: tanto la palabra (el enlace)
        // como la flechita abren/cierran el panel de tablas. En
        // escritorio, la palabra sigue navegando normal (ahí el
        // submenú se ve con el flyout de hover, no hace falta tocar).
        function esPantallaMovil() {
            return window.matchMedia('(max-width: 767px)').matches;
        }

        document.querySelectorAll('.item-con-submenu').forEach(function (fila) {
            const enlace = fila.querySelector('.nav-item');
            const boton = fila.querySelector('.boton-submenu-movil');
            const idObjetivo = boton.getAttribute('data-target');
            const panel = document.getElementById(idObjetivo);
            const flecha = boton.querySelector('.flecha-submenu-movil');

            function alternarSubmenu(evento) {
                if (esPantallaMovil()) {
                    evento.preventDefault(); // en móvil no navega, solo despliega
                    const estabaOculto = panel.classList.contains('hidden');

                    // Organiza el menú: cierra cualquier otro submenú abierto
                    // para que no se acumulen/monten uno sobre otro.
                    document.querySelectorAll('[id^="submenu-movil-"]').forEach(function (p) {
                        p.classList.add('hidden');
                    });
                    document.querySelectorAll('.flecha-submenu-movil').forEach(function (f) {
                        f.classList.remove('rotada');
                    });

                    if (estabaOculto) {
                        panel.classList.remove('hidden');
                        flecha.classList.add('rotada');
                    }
                }
                // en escritorio no hacemos preventDefault: el enlace navega normal
            }

            enlace.addEventListener('click', alternarSubmenu);
            boton.addEventListener('click', alternarSubmenu);
        });
    </script>

    <script src="{{ asset('js/validacion-live.js') }}"></script>
    @stack('scripts')

    @include('partials.cookies')

</body>
</html>