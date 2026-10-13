{{-- Layout base para el panel del Médico: sidebar verde con
     menú hamburguesa en móvil, igual que los paneles admin/usuario. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Panel del Médico') - PrismaSalud IPS</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        .contenido-medico {
            background: linear-gradient(180deg, #f2fbf7 0%, #f8fafc 100%);
            min-height: 100vh;
        }

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
            .sidebar-admin.abierto { transform: translateX(0); }
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
        .overlay-sidebar.abierto { opacity: 1; visibility: visible; }
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
                        <p class="text-xs text-white/60">Panel del Médico</p>
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
                    ['icono' => 'dashboard', 'texto' => 'Inicio',          'ruta' => route('medico.inicio'),          'clave' => 'inicio'],
                    ['icono' => 'historia',  'texto' => 'Historia médica', 'ruta' => route('medico.historia.index'),  'clave' => 'historia'],
                    ['icono' => 'procedimientos', 'texto' => 'Procedimientos', 'ruta' => route('medico.procedimiento.index'), 'clave' => 'procedimientos'],
                ];
            @endphp

            <nav class="flex-1 py-4 space-y-1 overflow-y-auto">
                @foreach ($menu as $item)
                    <a href="{{ $item['ruta'] }}"
                       class="nav-item flex items-center gap-3 px-5 py-2.5 text-sm font-medium {{ ($seccionActiva ?? '') === $item['clave'] ? 'activo' : '' }}">
                        @include('admin.partials.icono', ['icono' => $item['icono']])
                        <span>{{ $item['texto'] }}</span>
                    </a>
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
                    <h1 class="text-base md:text-lg font-bold text-gray-900">@yield('encabezado', 'Panel del Médico')</h1>
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <span class="hidden sm:inline text-xs font-semibold text-teal-700 bg-teal-50 px-3 py-1 rounded-full">
                        Médico
                    </span>

                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr($_SESSION['medico_nombres'] ?? 'M', 0, 1)) }}
                        </div>
                        <p class="hidden sm:block text-sm font-semibold text-gray-900">
                            {{ $_SESSION['medico_nombres'] ?? '' }}
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

            <main class="contenido-medico p-4 md:p-8">
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
    </script>

    @stack('scripts')

    @include('partials.cookies')

</body>
</html>
