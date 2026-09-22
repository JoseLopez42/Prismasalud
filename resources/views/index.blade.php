<!-- index.blade.php - Home publica -->
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
        body { font-family: 'Inter', sans-serif; }

        /* Degradado verde/teal usado en hero y banner CTA, con movimiento sutil */
        .gradiente-salud {
            background: linear-gradient(120deg, #1A5C3A 0%, #0f766e 50%, #0d9488 100%);
            background-size: 200% 200%;
            animation: moverGradiente 7s ease infinite;
        }
        @keyframes moverGradiente {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Formas decorativas flotantes de fondo (solo dentro del hero) */
        .hero-decoraciones {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: -1;
        }
        .blob {
            position: absolute;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.08);
            filter: blur(2px);
            animation: flotar 10s ease-in-out infinite;
        }
        .blob-1 { width: 220px; height: 220px; top: -60px; right: 8%; animation-delay: 0s; }
        .blob-2 { width: 140px; height: 140px; bottom: 10%; left: 4%; animation-delay: 2.5s; }
        .blob-3 { width: 90px; height: 90px; top: 30%; right: 30%; animation-delay: 5s; }
        @keyframes flotar {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-18px) translateX(10px); }
        }

        /* Header fijo, se vuelve semi-transparente al hacer scroll */
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
        .site-header .brand-accent { color: #0d9488; }
        .site-header .nav-link { color: #4b5563; }
        .site-header .nav-link:hover { color: #0d9488; }

        /* Subrayado animado al pasar el mouse por el menu */
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            left: 0; bottom: -4px;
            width: 0;
            height: 2px;
            background-color: #0d9488;
            transition: width .25s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }

        .site-header .btn-login {
            color: #0d9488;
            border-color: #0d9488;
        }
        .site-header .btn-login:hover { background-color: #f0fdfa; }

        /* Espacio reservado porque el header es fixed */
        .header-spacer { height: 80px; }

        /* Menu hamburguesa - drawer deslizante */
        .overlay-movil {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity .25s ease, visibility .25s ease;
            z-index: 60;
        }
        .overlay-movil.abierto {
            opacity: 1;
            visibility: visible;
        }

        .drawer-movil {
            position: fixed;
            top: 0; right: 0;
            height: 100%;
            width: 78%;
            max-width: 320px;
            background: #ffffff;
            box-shadow: -8px 0 24px rgba(0, 0, 0, 0.15);
            transform: translateX(100%);
            transition: transform .3s ease;
            z-index: 70;
            display: flex;
            flex-direction: column;
        }
        .drawer-movil.abierto {
            transform: translateX(0);
        }

        .drawer-movil .drawer-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.9rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }
        .drawer-movil .drawer-link:active,
        .drawer-movil .drawer-link:hover {
            background-color: #f0fdfa;
            color: #0f766e;
        }
        .drawer-movil .drawer-icono {
            width: 34px;
            height: 34px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        /* Boton CTA unico (mismo estilo en hero y banner) */
        .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.85rem 1.6rem;
            background: #ffffff;
            color: #0f766e;
            font-weight: 600;
            border-radius: 0.5rem;
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
            transition: transform .2s ease, box-shadow .2s ease;
            white-space: nowrap;
        }
        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.18);
        }

        /* Animacion de aparicion al hacer scroll */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .6s ease, transform .6s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Mapa: proporcion 16:9 sin librerias */
        .mapa-wrap {
            position: relative;
            padding-bottom: 40%;
            height: 0;
            overflow: hidden;
            border-radius: 0.75rem;
        }
        .mapa-wrap iframe {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            border: 0;
        }
        /* Tarjetas del hero: leve resalte al pasar el mouse */
        .tarjeta-hero {
            transition: background-color .2s ease, transform .2s ease;
        }
        .tarjeta-hero:hover {
            background-color: rgba(255, 255, 255, 0.16);
            transform: translateX(4px);
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

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium ml-10">
                <a href="{{ url('/') }}" class="nav-link">Inicio</a>
                <a href="{{ route('nosotros') }}" class="nav-link">Nosotros</a>
                <a href="#servicios" class="nav-link">Servicios</a>
                <a href="#blog" class="nav-link">Blog</a>
                <a href="#contacto" class="nav-link">Contacto</a>
            </nav>

            <!-- Solo boton de sesion, empujado a la derecha -->
            <div class="hidden md:flex items-center gap-3 ml-auto">
                <a href="{{ url('/login') }}"
                   class="px-5 py-2 text-sm font-semibold text-white rounded-md shadow-sm hover:opacity-90 transition"
                   style="background: linear-gradient(120deg, #0f766e, #0d9488);">
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

        <!-- Drawer movil -->
        <div id="drawer-movil" class="drawer-movil md:hidden">

            <div class="gradiente-salud flex items-center justify-between px-5 py-5">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud IPS" class="w-9 h-9 object-contain">
                    <p class="font-bold text-white text-sm">
                        Prisma<span class="text-teal-300">Salud</span> IPS
                    </p>
                </div>
                <button id="boton-cerrar-drawer" aria-label="Cerrar menú" class="text-white/80 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto py-2">
                <a href="{{ url('/') }}" class="drawer-link">
                    <span class="drawer-icono bg-teal-50 text-teal-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-8 9 8M5 10v10h14V10"/></svg>
                    </span> Inicio
                </a>
                <a href="{{ route('nosotros') }}" class="drawer-link">
                    <span class="drawer-icono bg-blue-50 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" d="M4 21c0-4 3.5-6 8-6s8 2 8 6"/></svg>
                    </span> Nosotros
                </a>
                <a href="#servicios" class="drawer-link">
                    <span class="drawer-icono bg-green-50 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6.5-4.35-9.3-8.4C.8 9.4 1.9 5.6 5.3 4.6c2-.6 4 .1 5.2 1.7l1.5 2 1.5-2c1.2-1.6 3.2-2.3 5.2-1.7 3.4 1 4.5 4.8 2.6 8-2.8 4.05-9.3 8.4-9.3 8.4z"/></svg>
                    </span> Servicios
                </a>
                <a href="#blog" class="drawer-link">
                    <span class="drawer-icono bg-purple-50 text-purple-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path stroke-linecap="round" d="M7 8h10M7 12h10M7 16h6"/></svg>
                    </span> Blog
                </a>
                <a href="#contacto" class="drawer-link">
                    <span class="drawer-icono bg-amber-50 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    </span> Contacto
                </a>
            </nav>

            <!-- Solo boton de sesion -->
            <div class="p-5 border-t border-gray-100 bg-gray-50 flex flex-col gap-3">
                <a href="{{ url('/login') }}"
                   class="btn-login px-4 py-2.5 text-sm font-semibold border rounded-md text-center bg-white">
                    Inicio sesión
                </a>
            </div>
        </div>
    </header>

    <div class="header-spacer"></div>

    <!-- Hero -->
    <section id="contenido-principal" class="gradiente-salud text-white relative">
        <div class="hero-decoraciones">
            <span class="blob blob-1"></span>
            <span class="blob blob-2"></span>
            <span class="blob blob-3"></span>
        </div>
        <div class="max-w-7xl mx-auto px-6 py-16 grid md:grid-cols-2 gap-12 items-center">

            <div>
                <span class="inline-block bg-white/10 text-white text-xs px-3 py-1 rounded-full mb-4">
                    IPS Certificada en Ibagué · Tolima
                </span>

                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                    Tu Salud es nuestra <br>
                    <span class="text-teal-300">razón de ser</span>
                </h1>

                <p class="text-white/80 mb-8 max-w-md">
                    En PrismaSalud IPS ofrecemos atención médica integral, humana y de
                    alta calidad. Más de 15 años cuidando a las familias del Tolima.
                </p>

                <div class="flex flex-wrap gap-4">
                    <a href="#servicios"
                       class="px-5 py-3 border border-white rounded-md font-semibold hover:bg-white hover:text-teal-700 transition">
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
        <div class="border-t border-white/10" style="background-color: rgba(26, 92, 58, 0.4);">
            @php
                // 'target' = numero puro para animar, 'sufijo' = lo que se agrega despues (K, +, %)
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
                        <p class="text-3xl font-extrabold">
                            <span class="stat-numero" data-target="{{ $stat['target'] }}">0</span>{{ $stat['sufijo'] }}
                        </p>
                        <p class="text-sm text-white/70">{{ $stat['texto'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Servicios -->
    <section id="servicios" class="max-w-7xl mx-auto px-6 py-20">
        <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
            Nuestros servicios
        </span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3">
            Atención integral <span class="text-teal-500">en un solo lugar</span>
        </h2>
        <p class="text-gray-500 mb-10 max-w-xl">
            Contamos con un equipo médico completo para cuidar tu salud y la de
            tu familia en todas las etapas de la vida.
        </p>

        @php
            $especialidades = [
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l5-5 4 4 8-8M15 8h5v5"/></svg>', 'titulo' => 'Medicina General', 'texto' => 'Consulta preventiva, diagnóstico y tratamiento de enfermedades comunes para pacientes de todas las edades.'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6.5-4.35-9.3-8.4C.8 9.4 1.9 5.6 5.3 4.6c2-.6 4 .1 5.2 1.7l1.5 2 1.5-2c1.2-1.6 3.2-2.3 5.2-1.7 3.4 1 4.5 4.8 2.6 8-2.8 4.05-9.3 8.4-9.3 8.4z"/></svg>', 'titulo' => 'Cardiología',      'texto' => 'Evaluación cardiovascular con equipos de última generación. Electrocardiogramas, ecocardiogramas y más.'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" d="M4 21c0-4 3.5-6 8-6s8 2 8 6"/></svg>', 'titulo' => 'Pediatría',        'texto' => 'Cuidado especializado para niños desde el nacimiento hasta la adolescencia. Controles de crecimiento y vacunación.'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 2v6L4 18a2 2 0 002 3h12a2 2 0 002-3L15 8V2M9 2h6M8 15h8"/></svg>', 'titulo' => 'Laboratorio Clínico', 'texto' => 'Análisis de sangre, orina, cultivos y más. Resultados rápidos y entrega digital desde nuestra plataforma.'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/></svg>', 'titulo' => 'Odontología',      'texto' => 'Salud oral completa: limpieza, ortodoncia, endodoncia y cirugía oral en un ambiente cómodo y moderno.'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.5 3a3.5 3.5 0 00-3.5 3.5c0 .35.04.7.13 1.02A3.5 3.5 0 003 11a3.5 3.5 0 002.13 3.22A3.5 3.5 0 009.5 19a3.5 3.5 0 003.5-3.5V6.5A3.5 3.5 0 009.5 3zM14.5 3a3.5 3.5 0 013.5 3.5c0 .35-.04.7-.13 1.02A3.5 3.5 0 0121 11a3.5 3.5 0 01-2.13 3.22A3.5 3.5 0 0114.5 19a3.5 3.5 0 01-3.5-3.5V6.5A3.5 3.5 0 0114.5 3z"/></svg>', 'titulo' => 'Psicología',       'texto' => 'Acompañamiento psicológico para el manejo del estrés, ansiedad, depresión y bienestar emocional.'],
            ];
        @endphp

        <div class="grid md:grid-cols-3 gap-6">
            @foreach ($especialidades as $esp)
                <div class="reveal border border-gray-200 rounded-xl p-6 hover:shadow-md transition">
                    <div class="w-11 h-11 flex items-center justify-center bg-teal-50 text-teal-600 rounded-lg mb-3">
                        {!! $esp['icono'] !!}
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $esp['titulo'] }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $esp['texto'] }}</p>
                    <a href="#" class="text-teal-600 text-sm font-semibold hover:underline">Conocer más →</a>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Testimonios -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                Testimonios
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-10">
                Lo que dicen nuestros pacientes
            </h2>

            @php
                $testimonios = [
                    ['estrellas' => 5, 'texto' => 'Excelente atención desde la recepción hasta el médico. El proceso de agendar cita fue muy sencillo y rápido. Totalmente recomendado.', 'iniciales' => 'MP', 'nombre' => 'María Pérez', 'desde' => 'Paciente desde 2021'],
                    ['estrellas' => 5, 'texto' => 'Los médicos son muy humanos y profesionales. Me siento en buenas manos cada vez que vengo. Las instalaciones son limpias y modernas.', 'iniciales' => 'JR', 'nombre' => 'Jose Rodríguez', 'desde' => 'Paciente desde 2019'],
                    ['estrellas' => 4, 'texto' => 'El servicio de pediatría es excepcional. Mi hijo siempre sale tranquilo después de sus controles. Los tiempos de espera son muy cortos.', 'iniciales' => 'CG', 'nombre' => 'Carolina Gómez', 'desde' => 'Paciente desde 2022'],
                ];
            @endphp

            <div class="grid md:grid-cols-3 gap-6 text-left">
                @foreach ($testimonios as $t)
                    <div class="reveal bg-white border border-gray-200 rounded-xl p-6">
                        <div class="text-yellow-400 mb-3">
                            {{ str_repeat('★', $t['estrellas']) }}{{ str_repeat('☆', 5 - $t['estrellas']) }}
                        </div>
                        <p class="text-sm text-gray-600 italic mb-4">
                            "{{ $t['texto'] }}"
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs font-bold">
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
        <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
            Ubicación
        </span>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
            Visítanos en nuestra sede principal, Ibagué
        </h2>

        <div class="grid md:grid-cols-3 gap-6 items-stretch">
            <!-- Mapa: ocupa 2 columnas en desktop -->
            <div class="reveal mapa-wrap border border-gray-200 md:col-span-2">
                <iframe
                    src="https://www.google.com/maps?q=Calle+42+%235-28,+Ibagué,+Tolima&output=embed"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <!-- Tarjeta con datos de la sede -->
            <div class="reveal bg-gray-50 border border-gray-200 rounded-xl p-6 flex flex-col justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">Sede principal</h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            <span>Calle 42 #5-28, Ibagué, Tolima</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5c0 9 7 16 16 16l3-4-6-3-2 2c-2-1-4-3-5-5l2-2-3-6z"/></svg>
                            <span>(601) 123-4667</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/></svg>
                            <span>Lun - Vie: 7:00 am - 7:00 pm</span>
                        </li>
                    </ul>
                </div>
                <a href="https://www.google.com/maps?q=Calle+42+%235-28,+Ibagué,+Tolima"
                   target="_blank" rel="noopener"
                   class="mt-6 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-md hover:opacity-90 transition"
                   style="background: linear-gradient(120deg, #0f766e, #0d9488);">
                    Cómo llegar
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 12h15"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Banner CTA -->
    <section class="gradiente-salud text-white">
        <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <h2 class="text-2xl md:text-3xl font-bold text-center md:text-left">
                ¿Listo para cuidar tu <span class="text-teal-300">salud</span> hoy?
            </h2>
            <a href="{{ url('/login') }}" class="btn-cta">
                Agendar tu cita ahora
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 12h15"/></svg>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white/80" style="background-color: #1A5C3A;">
        <div class="max-w-7xl mx-auto px-6 py-14 grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-10 md:gap-10 text-center md:text-left">

            <div class="col-span-2 md:col-span-1 flex flex-col items-center md:items-start">
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

            <!-- En móvil va a la IZQUIERDA (orden natural del grid de 2 columnas) -->
            <div>
                <p class="text-white font-semibold mb-3">Servicios</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="#servicios" class="hover:text-white">Medicina General</a></li>
                    <li><a href="#servicios" class="hover:text-white">Cardiología</a></li>
                    <li><a href="#servicios" class="hover:text-white">Pediatría</a></li>
                    <li><a href="#servicios" class="hover:text-white">Laboratorio</a></li>
                    <li><a href="#servicios" class="hover:text-white">Odontología</a></li>
                </ul>
            </div>

            <!-- En móvil va a la DERECHA -->
            <div>
                <p class="text-white font-semibold mb-3">Navegación</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/') }}" class="hover:text-white">Inicio</a></li>
                    <li><a href="{{ route('nosotros') }}" class="hover:text-white">Nosotros</a></li>
                    <li><a href="#servicios" class="hover:text-white">Servicios</a></li>
                    <li><a href="#blog" class="hover:text-white">Blog</a></li>
                    <li><a href="#contacto" class="hover:text-white">Contacto</a></li>
                </ul>
            </div>

            <div id="contacto" class="col-span-2 md:col-span-1">
                <p class="text-white font-semibold mb-3">Contacto</p>
                <ul class="space-y-2.5 text-sm">
                    <li class="flex items-center gap-2 justify-center md:justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        Calle 42 #5-28, Ibagué
                    </li>
                    <li class="flex items-center gap-2 justify-center md:justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5c0 9 7 16 16 16l3-4-6-3-2 2c-2-1-4-3-5-5l2-2-3-6z"/></svg>
                        (601) 123-4667
                    </li>
                    <li class="flex items-center gap-2 justify-center md:justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9 6 9-6"/></svg>
                        info@prismasaludips.com
                    </li>
                    <li class="flex items-center gap-2 justify-center md:justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/></svg>
                        Lun - Vie: 7am - 7pm
                    </li>
                </ul>
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

        function abrirMenuMovil() {
            drawerMovil.classList.add('abierto');
            overlayMovil.classList.add('abierto');
        }

        function cerrarMenuMovil() {
            drawerMovil.classList.remove('abierto');
            overlayMovil.classList.remove('abierto');
        }

        botonHamburguesa.addEventListener('click', abrirMenuMovil);
        botonCerrarDrawer.addEventListener('click', cerrarMenuMovil);
        overlayMovil.addEventListener('click', cerrarMenuMovil);

        drawerMovil.querySelectorAll('a').forEach(function (enlace) {
            enlace.addEventListener('click', cerrarMenuMovil);
        });

        // Animacion "reveal": agrega la clase "visible" cuando el
        // elemento entra en pantalla al hacer scroll.
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

        // Contador animado de la barra de estadisticas: sube de 0
        // al numero real solo cuando el usuario llega a esa seccion.
        function animarContador(elemento) {
            const meta = parseInt(elemento.dataset.target, 10);
            const duracion = 1200;
            const inicio = performance.now();

            function paso(ahora) {
                const progreso = Math.min((ahora - inicio) / duracion, 1);
                elemento.textContent = Math.floor(progreso * meta);
                if (progreso < 1) {
                    requestAnimationFrame(paso);
                } else {
                    elemento.textContent = meta;
                }
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
    </script>

</body>
</html>