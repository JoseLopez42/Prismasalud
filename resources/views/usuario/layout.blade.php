{{-- Layout base para todas las vistas del panel del Usuario.
     Misma estructura que el panel del Administrador: sidebar
     verde con menú hamburguesa en móvil. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Panel del Usuario') - PrismaSalud IPS</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        .sidebar-admin { background-color: #1A5C3A; }
        .sidebar-admin .nav-item { color: rgba(255, 255, 255, 0.75); transition: color .15s ease, background-color .15s ease; }
        .sidebar-admin .nav-item:hover { background-color: rgba(255, 255, 255, 0.08); color: #60a5fa; }
        .sidebar-admin .nav-item.activo { background-color: rgba(255, 255, 255, 0.12); color: #60a5fa; }

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

        .flecha-submenu-movil {
            transition: transform .2s ease;
        }
        .flecha-submenu-movil.rotada {
            transform: rotate(90deg);
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
                        <p class="text-xs text-white/60">Panel del Usuario</p>
                    </div>
                </div>
                <button id="boton-cerrar-sidebar" class="md:hidden text-white/70 hover:text-white" aria-label="Cerrar menú">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @php
                $menu = [
                    ['icono' => 'dashboard',      'texto' => 'Inicio',             'ruta' => route('usuario.inicio'), 'clave' => 'inicio',   'tablas' => null],
                    ['icono' => 'citas',          'texto' => 'Agendar cita',        'ruta' => '#', 'clave' => 'agendar',     'tablas' => null],
                    ['icono' => 'citas',          'texto' => 'Mis citas',           'ruta' => '#', 'clave' => 'mis_citas',   'tablas' => null],
                    ['icono' => 'historia',       'texto' => 'Historia clínica',    'ruta' => '#', 'clave' => 'historia',    'tablas' => null],
                    ['icono' => 'afiliaciones',   'texto' => 'Mi afiliación',       'ruta' => '#', 'clave' => 'afiliacion',  'tablas' => ['beneficiarios', 'eps', 'estado']],
                    ['icono' => 'farmacia',       'texto' => 'Mis medicamentos',    'ruta' => '#', 'clave' => 'medicinas',   'tablas' => ['medicamentos', 'entregas']],
                    ['icono' => 'configuracion',  'texto' => 'Mi perfil',           'ruta' => '#', 'clave' => 'perfil',      'tablas' => null],
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
                            <div class="flex items-center item-con-submenu {{ ($seccionActiva ?? '') === $item['clave'] ? 'activo' : '' }}">
                                <a href="{{ $item['ruta'] }}"
                                   class="nav-item flex-1 flex items-center gap-3 px-5 py-2.5 text-sm font-medium">
                                    @include('admin.partials.icono', ['icono' => $item['icono']])
                                    <span>{{ $item['texto'] }}</span>
                                </a>
                                <button type="button"
                                        class="boton-submenu-movil md:hidden pr-5 pl-2 text-white/50 hover:text-white"
                                        data-target="submenu-movil-u-{{ $loop->index }}"
                                        aria-label="Ver opciones de {{ $item['texto'] }}">
                                    <span class="flecha-submenu-movil inline-block">›</span>
                                </button>
                                <span class="hidden md:inline-block pr-5 text-white/40 group-hover:text-white">›</span>
                            </div>

                            <div id="submenu-movil-u-{{ $loop->index }}" class="hidden md:hidden pl-14 pb-2 space-y-1">
                                @foreach ($item['tablas'] as $tabla)
                                    <a href="#" class="block py-1.5 text-sm text-white/70 hover:text-[#60a5fa]">
                                        {{ ucfirst($tabla) }}
                                    </a>
                                @endforeach
                            </div>

                            <div class="hidden md:block absolute left-full top-0 ml-1 min-w-[190px] rounded-md shadow-lg py-2 z-50
                                        bg-[#1A5C3A] border border-white/10
                                        opacity-0 invisible
                                        group-hover:opacity-100 group-hover:visible
                                        transition duration-150">
                                @foreach ($item['tablas'] as $tabla)
                                    <a href="#" class="block px-4 py-2 text-sm text-white/80 hover:text-[#60a5fa] hover:bg-white/10 transition">
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
                    <h1 class="text-base md:text-lg font-bold text-gray-900">@yield('encabezado', 'Panel del Usuario')</h1>
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <span class="hidden sm:inline text-xs font-semibold text-teal-700 bg-teal-50 px-3 py-1 rounded-full">
                        Usuario
                    </span>

                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr($_SESSION['usuario_nombres'] ?? 'U', 0, 1)) }}
                        </div>
                        <p class="hidden sm:block text-sm font-semibold text-gray-900">
                            {{ $_SESSION['usuario_nombres'] ?? '' }}
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

            <main class="p-4 md:p-8">
                @yield('contenido')
            </main>
        </div>
    </div>

    <script>
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
                    evento.preventDefault();
                    panel.classList.toggle('hidden');
                    flecha.classList.toggle('rotada');
                }
            }

            enlace.addEventListener('click', alternarSubmenu);
            boton.addEventListener('click', alternarSubmenu);
        });
    </script>

</body>
</html>