<!-- index.blade.php - Home publica (rediseño multicolor) -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrismaSalud IPS </title>

    <meta name="description" content="PrismaSalud IPS - Atención médica integral en Ibagué, Tolima. Medicina general, cardiología, pediatría, laboratorio, odontología y psicología.">
    <meta property="og:title" content="PrismaSalud IPS">
    <meta property="og:description" content="Atención médica integral, humana y de alta calidad en Ibagué, Tolima.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('img/logo.png') }}">

    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --azul: #1B4D6B;
            --azul-claro: #2E86AB;
            --naranja: #0C7B93;
            --teal: #12A594;
            --teal-claro: #4ADE80;
        }

        body { font-family: 'Inter', sans-serif; }

        /* Degradado multicolor (azul -> teal -> naranja), tal como el logo */
        .gradiente-salud {
            background: linear-gradient(120deg, var(--azul) 0%, var(--teal) 55%, var(--naranja) 100%);
            background-size: 220% 220%;
            animation: moverGradiente 8s ease infinite;
        }
        @keyframes moverGradiente {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Formas decorativas flotantes, ahora con los 3 colores */
        .hero-decoraciones {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }
        .blob {
            position: absolute;
            border-radius: 9999px;
            filter: blur(6px);
            animation: flotar 11s ease-in-out infinite;
            opacity: 0.35;
        }
        .blob-1 { width: 260px; height: 260px; top: -70px; right: 6%; background: var(--naranja); animation-delay: 0s; }
        .blob-2 { width: 160px; height: 160px; bottom: 8%; left: 3%; background: var(--teal-claro); animation-delay: 2.5s; }
        .blob-3 { width: 110px; height: 110px; top: 32%; right: 28%; background: var(--azul-claro); animation-delay: 5s; }
        @keyframes flotar {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-22px) translateX(14px); }
        }

        /* Glow que sigue el cursor dentro del hero */
        #glow-cursor {
            position: absolute;
            width: 420px; height: 420px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(18,165,148,0.20) 0%, rgba(18,165,148,0) 70%);
            pointer-events: none;
            transform: translate(-50%, -50%);
            transition: left .12s linear, top .12s linear;
            z-index: 0;
        }

        /* Header fijo */
        .site-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 50;
            background-color: #ffffff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            transition: background-color .3s ease, box-shadow .3s ease, backdrop-filter .3s ease;
        }
        .site-header.scrolled {
            background-color: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06);
        }
        .site-header .logo-text { color: #111827; }
        .site-header .logo-sub { color: #9ca3af; }
        .site-header .brand-accent {
            background: linear-gradient(90deg, var(--azul-claro), var(--teal));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .site-header .nav-link { color: #4b5563; position: relative; }
        .site-header .nav-link:hover { color: var(--teal); }
        .nav-link::after {
            content: '';
            position: absolute;
            left: 0; bottom: -4px;
            width: 0; height: 2px;
            background: linear-gradient(90deg, var(--azul-claro), var(--naranja));
            transition: width .25s ease;
        }
        .nav-link:hover::after { width: 100%; }
        .header-spacer { height: 80px; }

        /* Menu hamburguesa - drawer */
        .overlay-movil {
            position: fixed; left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0; visibility: hidden;
            transition: opacity .25s ease, visibility .25s ease;
            z-index: 60;
        }
        .overlay-movil.abierto { opacity: 1; visibility: visible; }
        .drawer-movil {
            position: fixed; top: 0; right: 0; height: 100vh;
            width: 78%; max-width: 320px;
            background: #ffffff;
            box-shadow: -8px 0 24px rgba(0, 0, 0, 0.15);
            transform: translateX(100%);
            transition: transform .3s ease;
            z-index: 70;
            display: flex; flex-direction: column;
        }
        .drawer-movil.abierto { transform: translateX(0); }
        .drawer-movil .drawer-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.9rem 1.5rem; font-size: 0.95rem; font-weight: 600;
            color: #374151; border-bottom: 1px solid #f3f4f6;
        }
        .drawer-movil .drawer-link:active, .drawer-movil .drawer-link:hover {
            background-color: #f0fdfa; color: var(--teal);
        }
        .drawer-movil .drawer-icono {
            width: 34px; height: 34px; border-radius: 9999px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        /* Boton CTA principal: gradiente naranja->teal, se ve mas "vivo" */
        .btn-cta {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.85rem 1.6rem;
            background: linear-gradient(120deg, var(--naranja), var(--teal));
            background-size: 180% 180%;
            color: #ffffff;
            font-weight: 600;
            border-radius: 0.5rem;
            box-shadow: 0 4px 14px rgba(0,0,0,0.18);
            transition: transform .2s ease, box-shadow .2s ease, background-position .4s ease;
            white-space: nowrap;
        }
        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(0,0,0,0.24);
            background-position: 100% 50%;
        }

        /* Boton secundario (outline) */
        .btn-outline {
            border: 1.5px solid rgba(255,255,255,0.7);
            transition: transform .2s ease, background-color .2s ease;
        }
        .btn-outline:hover { transform: translateY(-2px); }

        /* Animacion de aparicion al hacer scroll */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .6s ease, transform .6s ease;
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* Mapa */
        .mapa-wrap {
            position: relative; padding-bottom: 40%; height: 0;
            overflow: hidden; border-radius: 0.75rem;
        }
        .mapa-wrap iframe {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;
        }

        /* Tarjetas del hero: efecto 3D al pasar el mouse (tilt) */
        .tarjeta-hero {
            transition: transform .15s ease, background-color .2s ease, box-shadow .2s ease;
            transform-style: preserve-3d;
            will-change: transform;
        }
        .tarjeta-hero:hover {
            background-color: rgba(255, 255, 255, 0.16);
            box-shadow: 0 10px 24px rgba(0,0,0,0.18);
        }

        /* Tarjetas de especialidades: barra de color superior que aparece al hover */
        .tarjeta-especialidad {
            position: relative;
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .tarjeta-especialidad::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, var(--azul-claro), var(--teal), var(--naranja));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .3s ease;
        }
        .tarjeta-especialidad:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 28px rgba(0,0,0,0.1);
        }
        .tarjeta-especialidad:hover::before { transform: scaleX(1); }

        /* Icono de cada especialidad: colores rotativos (azul/teal/naranja) */
        .icono-c1 { background: #e6f3fa; color: var(--azul-claro); }
        .icono-c2 { background: #e6f9f6; color: var(--teal); }
        .icono-c3 { background: #e0f7f4; color: var(--naranja); }

        /* Avatares de testimonios con colores rotativos */
        .avatar-c1 { background: var(--azul-claro); }
        .avatar-c2 { background: var(--teal); }
        .avatar-c3 { background: var(--naranja); }

        /* Insignia con degradado sutil */
        .badge-multicolor {
            background: linear-gradient(90deg, rgba(46,134,171,0.12), rgba(18,165,148,0.12), rgba(12,123,147,0.12));
            border: 1px solid rgba(255,255,255,0.4);
        }

        /* Contador con numero en degradado */
        .stat-gradiente {
            background: linear-gradient(90deg, #ffffff, var(--teal-claro));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
    </style>
</head>
<body class="bg-white text-gray-800">

    <!-- Navbar -->
    <header id="site-header" class="site-header w-full">
        <div class="max-w-7xl mx-auto flex items-center px-6 py-4">

            <div class="flex items-center gap-2 flex-shrink-0">
                <img src="{{ asset('img/logo.png') }}"
                     alt="Logo PrismaSalud IPS"
                     class="w-9 h-9 object-contain">
                <div class="leading-tight">
                    <p class="logo-text font-bold">
                        Prisma<span class="brand-accent">Salud</span> IPS
                    </p>
                    <p class="logo-sub text-xs">Ibagué · Tolima</p>
                </div>
            </div>

            <nav class="hidden md:flex items-center gap-5 xl:gap-8 text-sm font-medium ml-10" aria-label="Navegación principal">
                <a href="{{ url('/') }}" class="nav-link" style="color:var(--teal);">Inicio</a>
                <a href="{{ route('nosotros') }}" class="nav-link">Nosotros</a>
                <a href="{{ route('servicios.index') }}" class="nav-link">Servicios</a>
                <a href="{{ route('blog.index') }}" class="nav-link">Blog</a>
                <a href="{{ route('contacto.index') }}" class="nav-link text-sm">Contacto</a>
            </nav>

            <div class="hidden md:flex items-center gap-3 ml-auto">
                <a href="{{ url('/login') }}"
                   class="px-4 xl:px-5 py-2 text-sm font-semibold text-white rounded-md shadow-sm hover:opacity-90 transition"
                   style="background: linear-gradient(120deg, var(--azul-claro), var(--teal));">
                    Inicio sesión
                </a>
            </div>

            <button id="boton-hamburguesa" class="md:hidden nav-link ml-auto" aria-label="Abrir menú">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <div id="overlay-movil" class="overlay-movil md:hidden"></div>

        <div id="drawer-movil" class="drawer-movil md:hidden">
            <div class="gradiente-salud flex items-center justify-between px-5 py-5">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud IPS" class="w-9 h-9 object-contain">
                    <p class="font-bold text-white text-sm">
                        Prisma<span style="color:#e0f7f4;">Salud</span> IPS
                    </p>
                </div>
                <button id="boton-cerrar-drawer" aria-label="Cerrar menú" class="text-white/80 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto py-2">
                <a href="{{ url('/') }}" class="drawer-link" style="color:var(--teal);">
                    <span class="drawer-icono icono-c1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-8 9 8M5 10v10h14V10"/></svg>
                    </span> Inicio
                </a>
                <a href="{{ route('nosotros') }}" class="drawer-link">
                    <span class="drawer-icono icono-c2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" d="M4 21c0-4 3.5-6 8-6s8 2 8 6"/></svg>
                    </span> Nosotros
                </a>
                <a href="{{ route('servicios.index') }}" class="drawer-link">
                    <span class="drawer-icono icono-c3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6.5-4.35-9.3-8.4C.8 9.4 1.9 5.6 5.3 4.6c2-.6 4 .1 5.2 1.7l1.5 2 1.5-2c1.2-1.6 3.2-2.3 5.2-1.7 3.4 1 4.5 4.8 2.6 8-2.8 4.05-9.3 8.4-9.3 8.4z"/></svg>
                    </span> Servicios
                </a>
                <a href="{{ route('blog.index') }}" class="drawer-link">
                    <span class="drawer-icono icono-c1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path stroke-linecap="round" d="M7 8h10M7 12h10M7 16h6"/></svg>
                    </span> Blog
                </a>
                <a href="{{ route('contacto.index') }}" class="drawer-link">
                    <span class="drawer-icono icono-c3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    </span> Contacto
                </a>
            </nav>
            <div class="p-5 border-t border-gray-100 bg-gray-50 flex flex-col gap-3">
                <a href="{{ url('/login') }}"
                   class="px-4 py-2.5 text-sm font-semibold border rounded-md text-center bg-white"
                   style="color: var(--teal); border-color: var(--teal);">
                    Inicio sesión
                </a>
            </div>
        </div>
    </header>

    <div class="header-spacer"></div>

    <!-- Hero -->
    <section id="contenido-principal" class="gradiente-salud text-white relative overflow-hidden">
        <div class="hero-decoraciones">
            <span class="blob blob-1"></span>
            <span class="blob blob-2"></span>
            <span class="blob blob-3"></span>
        </div>
        <div id="glow-cursor"></div>

        <div class="max-w-7xl mx-auto px-6 py-16 grid md:grid-cols-2 gap-12 items-center relative" style="z-index: 1;">

            <div>
                <span class="badge-multicolor inline-block text-white text-xs px-3 py-1 rounded-full mb-4">
                    IPS Certificada en Ibagué · Tolima
                </span>

                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                    Tu Salud es nuestra <br>
                    <span style="background: linear-gradient(90deg, #e0f7f4, #b6f5e8); -webkit-background-clip: text; background-clip: text; color: transparent;">razón de ser</span>
                </h1>

                <p class="text-white/80 mb-8 max-w-md">
                    En PrismaSalud IPS ofrecemos atención médica integral, humana y de
                    alta calidad. Más de 15 años cuidando a las familias del Tolima.
                </p>

                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('servicios.index') }}"
                       class="btn-outline px-5 py-3 rounded-md font-semibold hover:bg-white hover:text-[var(--azul)] transition">
                        Ver nuestros servicios
                    </a>
                    <a href="{{ url('/login') }}" class="btn-cta">
                        Agendar cita
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 12h15"/></svg>
                    </a>
                </div>
            </div>

            <div class="space-y-4">
                @php
                    $serviciosDestacados = [
                        ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6.5-4.35-9.3-8.4C.8 9.4 1.9 5.6 5.3 4.6c2-.6 4 .1 5.2 1.7l1.5 2 1.5-2c1.2-1.6 3.2-2.3 5.2-1.7 3.4 1 4.5 4.8 2.6 8-2.8 4.05-9.3 8.4-9.3 8.4z"/></svg>', 'titulo' => 'Consulta médica',  'texto' => 'Medicina general y especializada'],
                        ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 20.5L3.5 13.5a5 5 0 117.07-7.07l7 7a5 5 0 01-7.07 7.07z"/><line x1="8.5" y1="8.5" x2="15.5" y2="15.5" stroke-linecap="round"/></svg>', 'titulo' => 'Farmacia interna', 'texto' => 'Medicamentos con descuento EPS'],
                        ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 2v6L4 18a2 2 0 002 3h12a2 2 0 002-3L15 8V2M9 2h6M8 15h8"/></svg>', 'titulo' => 'Laboratorio clínico', 'texto' => 'Resultados en 24 horas'],
                        ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>', 'titulo' => 'Citas en línea',   'texto' => 'Agenda fácil desde tu celular'],
                    ];
                @endphp
                @foreach ($serviciosDestacados as $servicio)
                    <div class="reveal tarjeta-hero flex items-center gap-4 bg-white/10 rounded-lg px-5 py-4">
                        <div class="w-10 h-10 flex items-center justify-center bg-white/20 rounded-md">
                            {!! $servicio['icono'] !!}
                        </div>
                        <div>
                            <p class="font-semibold">{{ $servicio['titulo'] }}</p>
                            <p class="text-sm text-white/70">{{ $servicio['texto'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Estadisticas -->
        <div class="border-t border-white/10 relative" style="background-color: rgba(0,0,0,0.15); z-index: 1;">
            @php
                $estadisticas = [
                    ['target' => 18, 'sufijo' => 'K+', 'texto' => 'Pacientes atendidos'],
                    ['target' => 35, 'sufijo' => '+',  'texto' => 'Profesionales de salud'],
                    ['target' => 12, 'sufijo' => '',   'texto' => 'Especialidades médicas'],
                    ['target' => 99, 'sufijo' => '%',  'texto' => 'Satisfacción del paciente'],
                    ['target' => 26, 'sufijo' => '',   'texto' => 'Años de experiencia'],
                ];
            @endphp
            <div id="stats-bar" class="max-w-7xl mx-auto px-6 py-8 grid grid-cols-2 md:grid-cols-5 gap-6 text-center">
                @foreach ($estadisticas as $stat)
                    <div class="reveal {{ $loop->last ? 'col-span-2 md:col-span-1' : '' }}">
                        <p class="text-3xl font-extrabold stat-gradiente">
                            <span class="stat-numero" data-target="{{ $stat['target'] }}">0</span>{{ $stat['sufijo'] }}
                        </p>
                        <p class="text-sm text-white/70">{{ $stat['texto'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- EPS afiliadas -->
    <section class="bg-white py-14 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-gray-400 text-sm font-semibold uppercase tracking-wide mb-8">
                Trabajamos con tu EPS
            </p>
            @php
                $eps = ['Sura', 'Nueva EPS', 'Sanitas', 'Compensar', 'Famisanar', 'Salud Total'];
            @endphp
            <div class="flex flex-wrap justify-center gap-x-10 gap-y-4">
                @foreach ($eps as $nombreEps)
                    <span class="text-gray-400 hover:text-[var(--teal)] font-bold text-lg transition">
                        {{ $nombreEps }}
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Servicios -->
    <section id="servicios" class="max-w-7xl mx-auto px-6 py-20">
        <span class="inline-block border text-xs px-3 py-1 rounded-full mb-4" style="border-color: var(--teal); color: var(--teal);">
            Nuestros servicios
        </span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3">
            Atención integral
            <span style="background: linear-gradient(90deg, var(--azul-claro), var(--teal), var(--naranja)); -webkit-background-clip: text; background-clip: text; color: transparent;">en un solo lugar</span>
        </h2>
        <p class="text-gray-500 mb-10 max-w-xl">
            Contamos con un equipo médico completo para cuidar tu salud y la de
            tu familia en todas las etapas de la vida.
        </p>

        @php
            $especialidades = [
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l5-5 4 4 8-8M15 8h5v5"/></svg>', 'titulo' => 'Medicina General', 'texto' => 'Consulta preventiva, diagnóstico y tratamiento de enfermedades comunes para pacientes de todas las edades.', 'color' => 'icono-c1'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6.5-4.35-9.3-8.4C.8 9.4 1.9 5.6 5.3 4.6c2-.6 4 .1 5.2 1.7l1.5 2 1.5-2c1.2-1.6 3.2-2.3 5.2-1.7 3.4 1 4.5 4.8 2.6 8-2.8 4.05-9.3 8.4-9.3 8.4z"/></svg>', 'titulo' => 'Cardiología',      'texto' => 'Evaluación cardiovascular con equipos de última generación. Electrocardiogramas, ecocardiogramas y más.', 'color' => 'icono-c3'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" d="M4 21c0-4 3.5-6 8-6s8 2 8 6"/></svg>', 'titulo' => 'Pediatría',        'texto' => 'Cuidado especializado para niños desde el nacimiento hasta la adolescencia. Controles de crecimiento y vacunación.', 'color' => 'icono-c2'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 2v6L4 18a2 2 0 002 3h12a2 2 0 002-3L15 8V2M9 2h6M8 15h8"/></svg>', 'titulo' => 'Laboratorio Clínico', 'texto' => 'Análisis de sangre, orina, cultivos y más. Resultados rápidos y entrega digital desde nuestra plataforma.', 'color' => 'icono-c1'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/></svg>', 'titulo' => 'Odontología',      'texto' => 'Salud oral completa: limpieza, ortodoncia, endodoncia y cirugía oral en un ambiente cómodo y moderno.', 'color' => 'icono-c3'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.5 3a3.5 3.5 0 00-3.5 3.5c0 .35.04.7.13 1.02A3.5 3.5 0 003 11a3.5 3.5 0 002.13 3.22A3.5 3.5 0 009.5 19a3.5 3.5 0 003.5-3.5V6.5A3.5 3.5 0 009.5 3zM14.5 3a3.5 3.5 0 013.5 3.5c0 .35-.04.7-.13 1.02A3.5 3.5 0 0121 11a3.5 3.5 0 01-2.13 3.22A3.5 3.5 0 0114.5 19a3.5 3.5 0 01-3.5-3.5V6.5A3.5 3.5 0 0114.5 3z"/></svg>', 'titulo' => 'Psicología',       'texto' => 'Acompañamiento psicológico para el manejo del estrés, ansiedad, depresión y bienestar emocional.', 'color' => 'icono-c2'],
            ];
        @endphp

        <div class="grid md:grid-cols-3 gap-6">
            @foreach ($especialidades as $esp)
                <div class="reveal tarjeta-especialidad border border-gray-200 rounded-xl p-6">
                    <div class="w-11 h-11 flex items-center justify-center {{ $esp['color'] }} rounded-lg mb-3">
                        {!! $esp['icono'] !!}
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $esp['titulo'] }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $esp['texto'] }}</p>
                    <a href="#" class="text-sm font-semibold hover:underline" style="color: var(--teal);">Conocer más →</a>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Como agendar tu cita -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-14">
                <span class="inline-block border text-xs px-3 py-1 rounded-full mb-4" style="border-color: var(--azul-claro); color: var(--azul-claro);">
                    Es muy fácil
                </span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Agenda tu cita en 3 pasos
                </h2>
            </div>

            @php
                $pasos = [
                    ['numero' => '1', 'titulo' => 'Elige tu especialidad', 'texto' => 'Selecciona el servicio médico que necesitas de nuestro catálogo.', 'color' => 'icono-c1'],
                    ['numero' => '2', 'titulo' => 'Elige fecha y horario', 'texto' => 'Escoge el día y la hora que mejor se acomode a tu agenda.', 'color' => 'icono-c2'],
                    ['numero' => '3', 'titulo' => 'Confirma tu cita', 'texto' => 'Recibe la confirmación al instante y listo, te esperamos.', 'color' => 'icono-c3'],
                ];
            @endphp

            <div class="grid md:grid-cols-3 gap-8">
                @foreach ($pasos as $paso)
                    <div class="reveal text-center">
                        <div class="w-14 h-14 mx-auto flex items-center justify-center {{ $paso['color'] }} rounded-full text-xl font-extrabold mb-4">
                            {{ $paso['numero'] }}
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $paso['titulo'] }}</h3>
                        <p class="text-sm text-gray-500">{{ $paso['texto'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ url('/login') }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white rounded-md hover:opacity-90 transition" style="background: linear-gradient(120deg, var(--azul-claro), var(--teal));">
                    Agendar ahora
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 12h15"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonios -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <span class="inline-block border text-xs px-3 py-1 rounded-full mb-4" style="border-color: var(--naranja); color: var(--naranja);">
                Testimonios
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-10">
                Lo que dicen nuestros pacientes
            </h2>

            @php
                $testimonios = [
                    ['estrellas' => 5, 'texto' => 'Excelente atención desde la recepción hasta el médico. El proceso de agendar cita fue muy sencillo y rápido. Totalmente recomendado.', 'iniciales' => 'MP', 'nombre' => 'María Pérez', 'desde' => 'Paciente desde 2021', 'color' => 'avatar-c1'],
                    ['estrellas' => 5, 'texto' => 'Los médicos son muy humanos y profesionales. Me siento en buenas manos cada vez que vengo. Las instalaciones son limpias y modernas.', 'iniciales' => 'JR', 'nombre' => 'Jose Rodríguez', 'desde' => 'Paciente desde 2019', 'color' => 'avatar-c2'],
                    ['estrellas' => 4, 'texto' => 'El servicio de pediatría es excepcional. Mi hijo siempre sale tranquilo después de sus controles. Los tiempos de espera son muy cortos.', 'iniciales' => 'CG', 'nombre' => 'Carolina Gómez', 'desde' => 'Paciente desde 2022', 'color' => 'avatar-c3'],
                ];
            @endphp

            <div class="grid md:grid-cols-3 gap-6 text-left">
                @foreach ($testimonios as $t)
                    <div class="reveal bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition">
                        <div class="mb-3 flex gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ $i <= $t['estrellas'] ? 'text-amber-400 fill-amber-400' : 'text-gray-300 fill-gray-300' }}" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600 italic mb-4">
                            "{{ $t['texto'] }}"
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full {{ $t['color'] }} text-white flex items-center justify-center text-xs font-bold">
                                {{ $t['iniciales'] }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $t['nombre'] }}</p>
                                <p class="text-xs text-gray-400">{{ $t['desde'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Mapa de ubicacion -->
    <section class="max-w-7xl mx-auto px-6 py-16">
        <span class="inline-block border text-xs px-3 py-1 rounded-full mb-4" style="border-color: var(--azul-claro); color: var(--azul-claro);">
            Ubicación
        </span>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
            Visítanos en nuestra sede principal, Ibagué
        </h2>

        <div class="grid md:grid-cols-3 gap-6 items-stretch">
            <div class="reveal mapa-wrap border border-gray-200 md:col-span-2">
                <iframe
                    src="https://www.google.com/maps?q=Calle+42+%235-28,+Ibagué,+Tolima&output=embed"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <div class="reveal bg-gray-50 border border-gray-200 rounded-xl p-6 flex flex-col justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">Sede principal</h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--teal);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            <span>Calle 42 #5-28, Ibagué, Tolima</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--teal);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5c0 9 7 16 16 16l3-4-6-3-2 2c-2-1-4-3-5-5l2-2-3-6z"/></svg>
                            <span>(601) 123-4667</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--teal);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/></svg>
                            <span>Lun - Vie: 7:00 am - 7:00 pm</span>
                        </li>
                    </ul>
                </div>
                <a href="https://www.google.com/maps?q=Calle+42+%235-28,+Ibagué,+Tolima"
                   target="_blank" rel="noopener"
                   class="mt-6 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-md hover:opacity-90 transition"
                   style="background: linear-gradient(120deg, var(--azul-claro), var(--teal));">
                    Cómo llegar
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 12h15"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Blog -->
    <section id="blog" class="max-w-7xl mx-auto px-6 py-20">
        <div class="text-center mb-12">
            <span class="inline-block border text-xs px-3 py-1 rounded-full mb-4" style="border-color: var(--teal); color: var(--teal);">
                Blog
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                Consejos para cuidar tu salud
            </h2>
        </div>

        @php
            $articulos = [
                ['color' => 'icono-c1', 'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>', 'fecha' => '10 sep 2026', 'titulo' => '5 hábitos para un corazón sano', 'resumen' => 'Pequeños cambios diarios que reducen el riesgo cardiovascular a largo plazo.'],
                ['color' => 'icono-c2', 'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>', 'fecha' => '2 sep 2026', 'titulo' => 'Calendario de vacunación infantil', 'resumen' => 'Qué vacunas necesita tu hijo y en qué edades, según el esquema nacional.'],
                ['color' => 'icono-c3', 'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>', 'fecha' => '25 ago 2026', 'titulo' => 'Manejo del estrés en el trabajo', 'resumen' => 'Estrategias simples respaldadas por psicología para el día a día.'],
            ];
        @endphp

        <div class="grid md:grid-cols-3 gap-6">
            @foreach ($articulos as $art)
                <a href="#" class="reveal block border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition">
                    <div class="{{ $art['color'] }} h-36 flex items-center justify-center">
                        {!! $art['icono'] !!}
                    </div>
                    <div class="p-5">
                        <p class="text-xs text-gray-400 mb-2">{{ $art['fecha'] }}</p>
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $art['titulo'] }}</h3>
                        <p class="text-sm text-gray-500">{{ $art['resumen'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mt-6 text-center">* Artículos de ejemplo, se reemplazan por contenido real.</p>
    </section>

    <!-- Banner CTA -->
    <section class="gradiente-salud text-white">
        <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <h2 class="text-2xl md:text-3xl font-bold text-center md:text-left">
                ¿Listo para cuidar tu
                <span style="background: linear-gradient(90deg, #e0f7f4, #b6f5e8); -webkit-background-clip: text; background-clip: text; color: transparent;">salud</span> hoy?
            </h2>
            <a href="{{ url('/login') }}" class="btn-cta">
                Agendar tu cita ahora
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 12h15"/></svg>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white/80" style="background-color: #1A5C3A;">
        <div class="max-w-7xl mx-auto px-6 py-14 grid grid-cols-2 lg:grid-cols-6 gap-x-6 gap-y-10 md:gap-10 text-center md:text-left break-words">

            <div class="col-span-2 lg:col-span-2 flex flex-col items-center md:items-start">
                <p class="text-white font-bold text-lg mb-2">
                    Prisma<span class="text-teal-300">Salud</span> IPS
                </p>
                <p class="text-sm mb-4">
                    Institución Prestadora de Servicios de Salud comprometida con el
                    bienestar integral de las familias del Tolima desde el 2000.
                </p>
                <p class="text-white font-semibold text-sm mb-2">Redes Sociales</p>
                <div class="flex gap-3 justify-center md:justify-start">
                    <a href="#" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 10-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0022 12z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5.1-1.3A10 10 0 1012 2zm5.8 14.2c-.2.7-1.4 1.3-2 1.4-.5.1-1.1.1-1.8-.1-.4-.1-1-.3-1.7-.6-3-1.3-4.9-4.3-5.1-4.5-.1-.2-1.2-1.6-1.2-3.1s.8-2.2 1.1-2.5c.3-.3.6-.4.8-.4h.6c.2 0 .5 0 .7.5.3.7.9 2.2 1 2.4.1.2.1.4 0 .6-.1.2-.2.4-.4.6-.2.2-.4.5-.5.6-.2.2-.4.4-.2.8.2.4.9 1.5 2 2.4 1.4 1.2 2.5 1.6 2.9 1.8.4.2.6.1.8-.1.2-.2.9-1 1.1-1.4.2-.4.5-.3.8-.2.3.1 2 .9 2.3 1.1.3.2.5.3.6.4.1.2.1.9-.1 1.7z"/></svg>
                    </a>
                </div>
            </div>

            <div>
                <p class="text-white font-semibold mb-3">Servicios</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/servicios#medicina-general') }}" class="hover:text-white">Medicina General</a></li>
                    <li><a href="{{ url('/servicios#cardiologia') }}" class="hover:text-white">Cardiología</a></li>
                    <li><a href="{{ url('/servicios#pediatria') }}" class="hover:text-white">Pediatría</a></li>
                    <li><a href="{{ url('/servicios#laboratorio') }}" class="hover:text-white">Laboratorio</a></li>
                    <li><a href="{{ url('/servicios#odontologia') }}" class="hover:text-white">Odontología</a></li>
                </ul>
            </div>

            <div>
                <p class="text-white font-semibold mb-3">Navegación</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/') }}" class="hover:text-white">Inicio</a></li>
                    <li><a href="{{ route('nosotros') }}" class="hover:text-white">Nosotros</a></li>
                    <li><a href="{{ route('servicios.index') }}" class="hover:text-white">Servicios</a></li>
                    <li><a href="{{ route('blog.index') }}" class="hover:text-white">Blog</a></li>
                    <li><a href="{{ route('contacto.index') }}" class="hover:text-white">Contacto</a></li>
                </ul>
            </div>

            <div>
                <p class="text-white font-semibold mb-3">Marco legal</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('terminos.index') }}" class="hover:text-white">Términos y Condiciones</a></li>
                    <li><a href="{{ route('privacidad.index') }}" class="hover:text-white">Política de Privacidad</a></li>
                    <li><a href="{{ url('/contacto#pqrs') }}" class="hover:text-white">PQRS</a></li>
                    <li><a href="{{ url('/contacto#derechos') }}" class="hover:text-white">Derechos del paciente</a></li>
                </ul>
            </div>

            <div id="contacto">
                <p class="text-white font-semibold mb-3">Contacto</p>
                <ul class="space-y-2.5 text-sm">
                    <li class="flex items-center gap-2 justify-center md:justify-start min-w-0 break-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        Calle 42 #5-28, Ibagué
                    </li>
                    <li class="flex items-center gap-2 justify-center md:justify-start min-w-0 break-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5c0 9 7 16 16 16l3-4-6-3-2 2c-2-1-4-3-5-5l2-2-3-6z"/></svg>
                        (601) 123-4667
                    </li>
                    <li class="flex items-center gap-2 justify-center md:justify-start min-w-0 break-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9 6 9-6"/></svg>
                        info@prismasaludips.com
                    </li>
                    <li class="flex items-center gap-2 justify-center md:justify-start min-w-0 break-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/></svg>
                        Lun - Vie: 7am - 7pm
                    </li>
                </ul>
            </div>
        </div>

        {{-- Barra legal inferior: normas y avisos de privacidad --}}
        <div class="border-t border-white/10">
            <div class="max-w-7xl mx-auto px-6 py-5 flex flex-col-reverse md:flex-row items-center justify-between gap-3 text-xs text-white/60">
                <p>© {{ date('Y') }} PrismaSalud IPS · NIT 900.000.000-0 · Todos los derechos reservados</p>
                <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2">
                    <a href="{{ route('terminos.index') }}" class="hover:text-white transition">Términos y Condiciones</a>
                    <span class="opacity-30">•</span>
                    <a href="{{ route('privacidad.index') }}" class="hover:text-white transition">Política de Privacidad</a>
                    <span class="opacity-30">•</span>
                    <a href="{{ url('/contacto#pqrs') }}" class="hover:text-white transition">PQRS</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const siteHeader = document.getElementById('site-header');
        function actualizarHeaderSegunScroll() {
            if (window.scrollY > 40) {
                siteHeader.classList.add('scrolled');
            } else {
                siteHeader.classList.remove('scrolled');
            }
        }
        window.addEventListener('scroll', actualizarHeaderSegunScroll);
        document.addEventListener('DOMContentLoaded', actualizarHeaderSegunScroll);

        // Menu hamburguesa
        const botonHamburguesa = document.getElementById('boton-hamburguesa');
        const botonCerrarDrawer = document.getElementById('boton-cerrar-drawer');
        const drawerMovil = document.getElementById('drawer-movil');
        const overlayMovil = document.getElementById('overlay-movil');
        function abrirMenuMovil() { drawerMovil.classList.add('abierto'); overlayMovil.classList.add('abierto'); }
        function cerrarMenuMovil() { drawerMovil.classList.remove('abierto'); overlayMovil.classList.remove('abierto'); }
        botonHamburguesa.addEventListener('click', abrirMenuMovil);
        botonCerrarDrawer.addEventListener('click', cerrarMenuMovil);
        overlayMovil.addEventListener('click', cerrarMenuMovil);
        drawerMovil.querySelectorAll('a').forEach(function (enlace) {
            enlace.addEventListener('click', cerrarMenuMovil);
        });

        // Animacion "reveal"
        const observadorReveal = new IntersectionObserver(function (entradas) {
            entradas.forEach(function (entrada) {
                if (entrada.isIntersecting) {
                    entrada.target.classList.add('visible');
                    observadorReveal.unobserve(entrada.target);
                }
            });
        }, { threshold: 0.15 });
        document.querySelectorAll('.reveal').forEach(function (el) {
            observadorReveal.observe(el);
        });

        // Contador animado
        function animarContador(elemento) {
            const meta = parseInt(elemento.dataset.target, 10);
            const duracion = 1200;
            const inicio = performance.now();
            function paso(ahora) {
                const progreso = Math.min((ahora - inicio) / duracion, 1);
                elemento.textContent = Math.floor(progreso * meta);
                if (progreso < 1) { requestAnimationFrame(paso); } else { elemento.textContent = meta; }
            }
            requestAnimationFrame(paso);
        }
        const statsBar = document.getElementById('stats-bar');
        if (statsBar) {
            const observadorStats = new IntersectionObserver(function (entradas) {
                entradas.forEach(function (entrada) {
                    if (entrada.isIntersecting) {
                        statsBar.querySelectorAll('.stat-numero').forEach(animarContador);
                        observadorStats.unobserve(statsBar);
                    }
                });
            }, { threshold: 0.3 });
            observadorStats.observe(statsBar);
        }

        // Glow que sigue el cursor dentro del hero
        const heroSection = document.getElementById('contenido-principal');
        const glow = document.getElementById('glow-cursor');
        if (heroSection && glow) {
            heroSection.addEventListener('mousemove', function (e) {
                const rect = heroSection.getBoundingClientRect();
                glow.style.left = (e.clientX - rect.left) + 'px';
                glow.style.top = (e.clientY - rect.top) + 'px';
            });
        }

        // Efecto tilt 3D en las tarjetas del hero
        document.querySelectorAll('.tarjeta-hero').forEach(function (tarjeta) {
            tarjeta.addEventListener('mousemove', function (e) {
                const rect = tarjeta.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centroX = rect.width / 2;
                const centroY = rect.height / 2;
                const rotX = ((y - centroY) / centroY) * -6;
                const rotY = ((x - centroX) / centroX) * 6;
                tarjeta.style.transform = 'perspective(600px) rotateX(' + rotX + 'deg) rotateY(' + rotY + 'deg) translateY(-2px)';
            });
            tarjeta.addEventListener('mouseleave', function () {
                tarjeta.style.transform = 'perspective(600px) rotateX(0deg) rotateY(0deg) translateY(0)';
            });
        });
    </script>

    <!-- Boton flotante de WhatsApp -->
    <a href="https://wa.me/573017880161?text=Hola%2C%20quiero%20agendar%20una%20cita"
       target="_blank"
       rel="noopener"
       aria-label="Escribir por WhatsApp"
       class="fixed bottom-6 right-6 z-40 w-14 h-14 rounded-full bg-green-500 hover:bg-green-600 text-white flex items-center justify-center shadow-lg transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2a10 10 0 00-8.6 15.1L2 22l5.1-1.3A10 10 0 1012 2zm5.8 14.2c-.2.7-1.4 1.3-2 1.4-.5.1-1.1.1-1.8-.1-.4-.1-1-.3-1.7-.6-3-1.3-4.9-4.3-5.1-4.5-.1-.2-1.2-1.6-1.2-3.1s.8-2.2 1.1-2.5c.3-.3.6-.4.8-.4h.6c.2 0 .5 0 .7.5.3.7.9 2.2 1 2.4.1.2.1.4 0 .6-.1.2-.2.4-.4.6-.2.2-.4.5-.5.6-.2.2-.4.4-.2.8.2.4.9 1.5 2 2.4 1.4 1.2 2.5 1.6 2.9 1.8.4.2.6.1.8-.1.2-.2.9-1 1.1-1.4.2-.4.5-.3.8-.2.3.1 2 .9 2.3 1.1.3.2.5.3.6.4.1.2.1.9-.1 1.7z"/>
        </svg>
    </a>

    @include('partials.cookies')

</body>
</html>