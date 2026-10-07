<!-- resources/views/superadmin/layout.blade.php - Estructura común del panel Superadministrador -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('titulo', 'Panel') - Superadmin PrismaSalud</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                prisma: { azul: '#1B4D6B', claro: '#2E86AB', turquesa: '#0C7B93', teal: '#12A594' }
            } } }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .menu-fondo { background: linear-gradient(180deg, #1B4D6B 0%, #0C7B93 100%); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

@php
    $enlaces = [
        ['ruta' => 'superadmin.dashboard',            'activo' => 'superadmin.dashboard',         'texto' => 'Inicio',         'icono' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10'],
        ['ruta' => 'superadmin.empresas.index',       'activo' => 'superadmin.empresas.*',        'texto' => 'Empresas',       'icono' => 'M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M9 13h.01M9 17h.01M15 9h.01M15 13h.01M15 17h.01'],
        ['ruta' => 'superadmin.administradores.index','activo' => 'superadmin.administradores.*', 'texto' => 'Administradores','icono' => 'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0'],
        ['ruta' => 'superadmin.planes.index',         'activo' => 'superadmin.planes.*',          'texto' => 'Planes',         'icono' => 'M12 2l9 5-9 5-9-5 9-5zM3 12l9 5 9-5M3 17l9 5 9-5'],
        ['ruta' => 'superadmin.pagos.index',          'activo' => 'superadmin.pagos.*',           'texto' => 'Pagos',          'icono' => 'M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM3 10h18M7 15h3'],
        ['ruta' => 'superadmin.cuenta.edit',          'activo' => 'superadmin.cuenta.*',          'texto' => 'Mi cuenta',      'icono' => 'M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z'],
    ];
    $nombreSuper = $_SESSION['superadmin_nombre'] ?? 'Superadministrador';
@endphp

    {{-- ===== Menú lateral ===== --}}
    <aside id="menu" class="menu-fondo hidden lg:flex fixed inset-y-0 left-0 z-40 w-64 flex-col text-white shadow-xl">
        <div class="px-5 py-5 flex items-center gap-3 border-b border-white/15">
            <span class="bg-white rounded-xl p-1.5"><img src="{{ asset('img/logo.png') }}" alt="PrismaSalud" class="h-8"></span>
            <div>
                <p class="text-sm font-extrabold leading-tight">PrismaSalud</p>
                <p class="text-[11px] text-white/70 leading-tight">Panel Superadmin</p>
            </div>
        </div>

        <nav class="flex-1 px-3 py-5 space-y-1">
            @foreach ($enlaces as $e)
                @php $activo = request()->routeIs($e['activo']); @endphp
                <a href="{{ route($e['ruta']) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ $activo ? 'bg-white text-prisma-azul shadow' : 'text-white/85 hover:bg-white/15' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $e['icono'] }}"/></svg>
                    {{ $e['texto'] }}
                </a>
            @endforeach
        </nav>

        <div class="px-4 py-4 border-t border-white/15">
            <p class="text-xs text-white/70">Sesión iniciada como</p>
            <p class="text-sm font-bold truncate">{{ $nombreSuper }}</p>
            <form method="POST" action="{{ route('superadmin.logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full rounded-lg bg-white/15 hover:bg-white/25 text-white text-sm font-semibold py-2 transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== Barra superior (celular) ===== --}}
    <header class="lg:hidden sticky top-0 z-30 bg-prisma-azul text-white flex items-center justify-between px-4 py-3 shadow">
        <button type="button" id="abrir-menu" aria-label="Abrir menú" class="p-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <p class="text-sm font-extrabold">Panel Superadmin</p>
        <span class="w-6"></span>
    </header>
    <div id="fondo-menu" class="hidden fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

    {{-- ===== Contenido ===== --}}
    <div class="lg:pl-64">
        <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8">

            @if (session('exito'))
                <div class="mb-5 flex items-start gap-2 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3" role="status">
                    <svg class="w-5 h-5 shrink-0 mt-px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('exito') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3" role="alert">{{ session('error') }}</div>
            @endif

            @yield('contenido')
        </main>
    </div>

    <script>
        // Abrir y cerrar el menú en el celular
        (function () {
            var menu = document.getElementById('menu');
            var fondo = document.getElementById('fondo-menu');
            function abrir() { menu.classList.remove('hidden'); menu.classList.add('flex'); fondo.classList.remove('hidden'); }
            function cerrar() { menu.classList.add('hidden'); menu.classList.remove('flex'); fondo.classList.add('hidden'); }
            document.getElementById('abrir-menu').addEventListener('click', abrir);
            fondo.addEventListener('click', cerrar);
        })();
    </script>
    @stack('scripts')
</body>
</html>