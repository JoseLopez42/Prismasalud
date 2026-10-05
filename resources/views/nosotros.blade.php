<!-- nosotros.blade.php - Pagina institucional -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - PrismaSalud IPS</title>

    <meta name="description" content="Conoce a PrismaSalud IPS: nuestra historia, misión, visión y el equipo médico que cuida a las familias de Ibagué, Tolima.">

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

        /* Header fijo, igual al del index */
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

        /* Menu hamburguesa - drawer deslizante (igual al index) */
        .overlay-movil {
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity .25s ease, visibility .25s ease;
            z-index: 60;
        }
        .overlay-movil.abierto { opacity: 1; visibility: visible; }
        .drawer-movil {
            position: fixed;
            top: 0; right: 0;
            height: 100vh;
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
        .drawer-movil.abierto { transform: translateX(0); }
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
            color: var(--teal);
        }
        .drawer-movil .drawer-icono {
            width: 34px;
            height: 34px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Boton CTA unico (igual al del index) */
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

        /* Animacion de aparicion al hacer scroll (igual al index) */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .6s ease, transform .6s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-white text-gray-800">

    <!-- Navbar (igual al del index) -->
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
                <a href="{{ url('/') }}" class="nav-link">Inicio</a>
                <a href="{{ route('nosotros') }}" class="nav-link" style="color:var(--teal);">Nosotros</a>
                <a href="{{ route('servicios.index') }}" class="nav-link">Servicios</a>
                <a href="{{ route('blog.index') }}" class="nav-link">Blog</a>
                <a href="{{ route('contacto.index') }}" class="nav-link text-sm">Contacto</a>
                <a href="{{ route('planes.index') }}" class="nav-link text-sm">Planes</a>
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
                <a href="{{ route('nosotros') }}" class="drawer-link" style="color:var(--teal);">
                    <span class="drawer-icono bg-blue-50 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" d="M4 21c0-4 3.5-6 8-6s8 2 8 6"/></svg>
                    </span> Nosotros
                </a>
                <a href="{{ route('servicios.index') }}" class="drawer-link">
                    <span class="drawer-icono bg-green-50 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6.5-4.35-9.3-8.4C.8 9.4 1.9 5.6 5.3 4.6c2-.6 4 .1 5.2 1.7l1.5 2 1.5-2c1.2-1.6 3.2-2.3 5.2-1.7 3.4 1 4.5 4.8 2.6 8-2.8 4.05-9.3 8.4-9.3 8.4z"/></svg>
                    </span> Servicios
                </a>
                <a href="{{ route('blog.index') }}" class="drawer-link">
                    <span class="drawer-icono bg-purple-50 text-purple-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path stroke-linecap="round" d="M7 8h10M7 12h10M7 16h6"/></svg>
                    </span> Blog
                </a>
                <a href="{{ route('contacto.index') }}" class="drawer-link">
                    <span class="drawer-icono bg-amber-50 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    </span> Contacto
                </a>
                <a href="{{ route('planes.index') }}" class="drawer-link">
                    <span class="drawer-icono bg-teal-50 text-teal-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" d="M3 10h18M7 15h4"/></svg>
                    </span> Planes
                </a>
            </nav>
            <div class="p-5 border-t border-gray-100 bg-gray-50">
                <a href="{{ url('/login') }}"
                   class="px-4 py-2.5 text-sm font-semibold border rounded-md text-center bg-white block"
                   style="color:var(--teal); border-color:var(--teal);">
                    Inicio sesión
                </a>
            </div>
        </div>
    </header>

    <div class="header-spacer"></div>

    <!-- Hero de la pagina -->
    <section class="gradiente-salud text-white">
        <div class="max-w-4xl mx-auto px-6 py-16 text-center">
            <span class="inline-block bg-white/10 text-white text-xs px-3 py-1 rounded-full mb-4">
                Nuestra historia
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                Más de 15 años cuidando <span class="text-teal-300">tu salud</span>
            </h1>
            <p class="text-white/80 max-w-2xl mx-auto">
                PrismaSalud IPS nació en Ibagué con un propósito claro: ofrecer atención
                médica humana, cercana y de alta calidad a cada paciente que confía en nosotros.
            </p>
        </div>
    </section>

    <!-- Historia -->
    <section class="max-w-5xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-10 items-center">
        <div class="reveal">
            <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                Nuestra historia
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                Del Tolima, para el Tolima
            </h2>
            <p class="text-gray-500 mb-4">
                Desde el año 2000, PrismaSalud IPS ha acompañado a miles de familias
                tolimenses en cada etapa de su vida. Empezamos como un pequeño consultorio
                de medicina general y hoy contamos con más de 12 especialidades médicas
                y un equipo de más de 35 profesionales de la salud.
            </p>
            <p class="text-gray-500">
                Nuestro crecimiento siempre ha estado guiado por un mismo principio:
                la salud de nuestros pacientes es primero, y la tecnología y los
                procesos solo existen para servir mejor a ese propósito.
            </p>
        </div>
        <div class="reveal bg-gray-50 border border-gray-200 rounded-xl p-8">
            <div id="stats-bar" class="grid grid-cols-2 gap-6 text-center">
                <div>
                    <p class="text-3xl font-extrabold text-teal-600">
                        <span class="stat-numero" data-target="26">0</span>
                    </p>
                    <p class="text-sm text-gray-500">Años de experiencia</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-teal-600">
                        +<span class="stat-numero" data-target="18">0</span>K
                    </p>
                    <p class="text-sm text-gray-500">Pacientes atendidos</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-teal-600">
                        <span class="stat-numero" data-target="35">0</span>+
                    </p>
                    <p class="text-sm text-gray-500">Profesionales</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-teal-600">
                        <span class="stat-numero" data-target="12">0</span>
                    </p>
                    <p class="text-sm text-gray-500">Especialidades</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Linea de tiempo -->
    <section class="max-w-4xl mx-auto px-6 py-20">
        <div class="text-center mb-14">
            <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                Nuestro recorrido
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                Hitos que marcaron nuestro camino
            </h2>
        </div>

        @php
            $hitos = [
                ['anio' => '2000', 'titulo' => 'Fundación', 'texto' => 'Abrimos nuestro primer consultorio de medicina general en el centro de Ibagué.'],
                ['anio' => '2009', 'titulo' => 'Nueva sede', 'texto' => 'Nos trasladamos a nuestra sede actual, ampliando la capacidad de atención.'],
                ['anio' => '2015', 'titulo' => 'Especialidades', 'texto' => 'Incorporamos cardiología, pediatría y odontología a nuestro portafolio.'],
                ['anio' => '2022', 'titulo' => 'Digitalización', 'texto' => 'Lanzamos la plataforma de agendamiento de citas en línea.'],
                ['anio' => '2026', 'titulo' => 'Hoy', 'texto' => 'Más de 12 especialidades y 35 profesionales cuidando al Tolima.'],
            ];
        @endphp

        <div class="relative pl-8 border-l-2 border-teal-200 space-y-10">
            @foreach ($hitos as $hito)
                <div class="reveal relative">
                    <span class="absolute -left-[calc(2rem+5px)] top-1 w-3 h-3 rounded-full bg-teal-600 border-4 border-white shadow"></span>
                    <p class="text-teal-600 font-bold text-sm mb-1">{{ $hito['anio'] }}</p>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ $hito['titulo'] }}</h3>
                    <p class="text-sm text-gray-500">{{ $hito['texto'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Mision y vision -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-5xl mx-auto px-6 grid md:grid-cols-2 gap-6">
            <div class="reveal bg-white border border-gray-200 rounded-xl p-8">
                <div class="w-11 h-11 flex items-center justify-center bg-teal-50 text-teal-600 rounded-lg mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900 text-lg mb-2">Misión</h3>
                <p class="text-gray-500 text-sm">
                    Brindar servicios de salud integrales, seguros y humanizados,
                    contribuyendo al bienestar físico y emocional de cada paciente
                    y su familia en Ibagué y el Tolima.
                </p>
            </div>
            <div class="reveal bg-white border border-gray-200 rounded-xl p-8">
                <div class="w-11 h-11 flex items-center justify-center bg-teal-50 text-teal-600 rounded-lg mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12S6 5 12 5s9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900 text-lg mb-2">Visión</h3>
                <p class="text-gray-500 text-sm">
                    Para 2030, ser reconocidos como la IPS líder del Tolima en calidad
                    de atención, innovación tecnológica y satisfacción del paciente.
                </p>
            </div>
        </div>
    </section>

    <!-- Mensaje del director -->
    <section class="max-w-4xl mx-auto px-6 py-20 text-center">
        <div class="reveal">
            <div class="w-16 h-16 mx-auto rounded-full bg-teal-600 text-white flex items-center justify-center text-lg font-bold mb-5">
                RG
            </div>
            <p class="text-xl md:text-2xl text-gray-700 font-medium italic mb-5">
                "Cada paciente que entra por nuestra puerta merece ser escuchado,
                no solo diagnosticado. Esa es la promesa que hacemos todos los
                días en PrismaSalud."
            </p>
            <p class="font-semibold text-gray-900">Dr. Ricardo Gómez</p>
            <p class="text-sm text-teal-600">Director Médico, PrismaSalud IPS</p>
        </div>
    </section>

    <!-- Valores -->
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="text-center mb-12">
            <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                Nuestros valores
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                Lo que nos guía cada día
            </h2>
        </div>

        @php
            $valores = [
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6.5-4.35-9.3-8.4C.8 9.4 1.9 5.6 5.3 4.6c2-.6 4 .1 5.2 1.7l1.5 2 1.5-2c1.2-1.6 3.2-2.3 5.2-1.7 3.4 1 4.5 4.8 2.6 8-2.8 4.05-9.3 8.4-9.3 8.4z"/></svg>', 'titulo' => 'Calidez humana', 'texto' => 'Tratamos a cada paciente con respeto, empatía y cercanía.'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M12 3l8 4v5c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V7l8-4z"/></svg>', 'titulo' => 'Compromiso', 'texto' => 'Nos comprometemos con el bienestar real de cada persona.'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2"/></svg>', 'titulo' => 'Puntualidad', 'texto' => 'Valoramos el tiempo de nuestros pacientes en cada cita.'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'titulo' => 'Excelencia', 'texto' => 'Buscamos la mejora continua en cada servicio que ofrecemos.'],
            ];
        @endphp

        <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach ($valores as $valor)
                <div class="reveal border border-gray-200 rounded-xl p-6 text-center hover:shadow-md transition">
                    <div class="w-12 h-12 mx-auto flex items-center justify-center bg-teal-50 text-teal-600 rounded-full mb-3">
                        {!! $valor['icono'] !!}
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ $valor['titulo'] }}</h3>
                    <p class="text-sm text-gray-500">{{ $valor['texto'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Certificaciones -->
    <section class="max-w-6xl mx-auto px-6 py-16 text-center">
        <p class="text-gray-400 text-sm font-semibold uppercase tracking-wide mb-8">
            Certificaciones y habilitación
        </p>
        @php
            $certificaciones = [
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'texto' => 'Habilitación Secretaría de Salud del Tolima'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2 4 4 8-9 4 4"/></svg>', 'texto' => 'ISO 9001 en gestión de calidad'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3 8-7 9-4-1-7-4-7-9V7l7-4z"/></svg>', 'texto' => 'Registro INVIMA para laboratorio clínico'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" d="M4 21c0-4 3.5-6 8-6s8 2 8 6"/></svg>', 'texto' => 'Talento humano certificado y actualizado'],
            ];
        @endphp
        <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach ($certificaciones as $cert)
                <div class="reveal border border-gray-200 rounded-xl p-6">
                    <div class="w-14 h-14 mx-auto flex items-center justify-center bg-teal-50 text-teal-600 rounded-full mb-3">
                        {!! $cert['icono'] !!}
                    </div>
                    <p class="text-sm text-gray-600">{{ $cert['texto'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Equipo medico -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-12">
                <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                    Nuestro equipo
                </span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Profesionales que cuidan de ti
                </h2>
            </div>

            @php
                $medicos = [
                    ['nombre' => 'Dra. Ana Ramírez', 'especialidad' => 'Medicina General', 'iniciales' => 'AR'],
                    ['nombre' => 'Dr. Carlos Mendoza', 'especialidad' => 'Cardiología', 'iniciales' => 'CM'],
                    ['nombre' => 'Dra. Laura Torres', 'especialidad' => 'Pediatría', 'iniciales' => 'LT'],
                    ['nombre' => 'Dr. Andrés Vargas', 'especialidad' => 'Odontología', 'iniciales' => 'AV'],
                ];
            @endphp

            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6">
                @foreach ($medicos as $medico)
                    <div class="reveal bg-white border border-gray-200 rounded-xl p-6 text-center">
                        <div class="w-16 h-16 mx-auto rounded-full bg-teal-600 text-white flex items-center justify-center text-lg font-bold mb-3">
                            {{ $medico['iniciales'] }}
                        </div>
                        <h3 class="font-semibold text-gray-900">{{ $medico['nombre'] }}</h3>
                        <p class="text-sm text-teal-600">{{ $medico['especialidad'] }}</p>
                    </div>
                @endforeach
            </div>
            
        </div>
    </section>

    <!-- Video institucional -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                Conócenos
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8">
                Un recorrido por PrismaSalud IPS
            </h2>
            <div class="reveal aspect-video bg-gray-900 rounded-xl overflow-hidden flex items-center justify-center relative">
                <button type="button" aria-label="Reproducir video institucional"
                        class="w-16 h-16 rounded-full bg-white/90 flex items-center justify-center hover:scale-105 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-teal-700 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </button>
            </div>
            
        </div>
    </section>

    <!-- Galeria de instalaciones -->
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="text-center mb-10">
            <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                Nuestras instalaciones
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                Un espacio pensado para tu comodidad
            </h2>
        </div>
        @php
            $instalaciones = [
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>', 'texto' => 'Recepción y sala de espera'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="4"/><path d="M12 8v8M8 12h8"/></svg>', 'texto' => 'Consultorios médicos'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>', 'texto' => 'Laboratorio clínico'],
                ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5.5 9h13a2.5 2.5 0 010 6h-13a2.5 2.5 0 010-6z"/><path d="M12 9v6"/></svg>', 'texto' => 'Farmacia interna'],
            ];
        @endphp
        <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach ($instalaciones as $foto)
<div class="reveal aspect-[4/3] rounded-xl flex flex-col items-center justify-center text-white" style="background: linear-gradient(135deg, var(--azul-claro), var(--teal));">
                    <span class="mb-1 flex justify-center">{!! $foto['icono'] !!}</span>
                    <span class="text-xs font-medium px-3 text-center">{{ $foto['texto'] }}</span>
                </div>
            @endforeach
        </div>
        
    </section>

    <!-- Preguntas frecuentes institucionales -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-3xl mx-auto px-6">
            <div class="text-center mb-10">
                <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                    Sobre nosotros
                </span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Preguntas que nos hacen seguido
                </h2>
            </div>

            @php
                $preguntasInstitucionales = [
                    ['pregunta' => '¿Desde cuándo existe PrismaSalud IPS?', 'respuesta' => 'Fundada en el año 2000, llevamos más de 26 años atendiendo a familias del Tolima.'],
                    ['pregunta' => '¿Trabajan con todas las EPS?', 'respuesta' => 'Tenemos convenio con las principales EPS del país; consulta la sección de servicios en nuestro inicio para más detalle.'],
                    ['pregunta' => '¿Cuántos profesionales conforman el equipo?', 'respuesta' => 'Contamos con más de 35 profesionales de la salud entre médicos generales, especialistas y personal de apoyo.'],
                    ['pregunta' => '¿La IPS está habilitada oficialmente?', 'respuesta' => 'Sí, contamos con habilitación vigente ante la Secretaría de Salud del Tolima y certificación ISO 9001.'],
                ];
            @endphp

            <div class="space-y-3">
                @foreach ($preguntasInstitucionales as $p)
                    <div class="faq-institucional-item border border-gray-200 rounded-lg bg-white overflow-hidden">
                        <button type="button"
                                class="faq-institucional-pregunta w-full flex items-center justify-between text-left px-5 py-4 font-semibold text-gray-800"
                                aria-expanded="false">
                            <span>{{ $p['pregunta'] }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="faq-institucional-icono w-5 h-5 flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="faq-institucional-respuesta px-5 text-sm text-gray-500" style="max-height: 0; overflow: hidden; transition: max-height .3s ease;">
                            <p class="pb-4">{{ $p['respuesta'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Portafolio descargable -->
    <section class="max-w-4xl mx-auto px-6 py-16">
        <div class="reveal border border-gray-200 rounded-xl p-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h3 class="font-semibold text-gray-900 text-lg mb-1">¿Quieres conocernos por escrito?</h3>
                <p class="text-sm text-gray-500">Descarga nuestro portafolio institucional con misión, servicios y certificaciones.</p>
            </div>
            <a href="{{ asset('descargas/portafolio-prismasalud.pdf') }}"
               download="Portafolio-PrismaSalud-IPS.pdf"
               class="inline-flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white rounded-md hover:opacity-90 transition whitespace-nowrap" style="background: linear-gradient(120deg, var(--azul-claro), var(--teal));">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 19h16"/></svg>
                Descargar portafolio (PDF)
            </a>
        </div>
    </section>

    <!-- Banner CTA -->
    <section class="gradiente-salud text-white">
        <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <h2 class="text-2xl md:text-3xl font-bold text-center md:text-left">
                ¿Listo para cuidar tu <span class="text-teal-300">salud</span> hoy?
            </h2>
            <a href="{{ url('/citas/agendar') }}" class="btn-cta">
                Agendar tu cita ahora
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 12h15"/></svg>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white/80" style="background-color: #1A5C3A;">
        <div class="max-w-7xl mx-auto px-6 py-14 grid grid-cols-2 lg:grid-cols-5 gap-x-6 gap-y-10 md:gap-10 text-center md:text-left break-words">
            <div class="col-span-2 lg:col-span-1 flex flex-col items-center md:items-start">
                <p class="text-white font-bold text-lg mb-2">
                    Prisma<span class="text-teal-300">Salud</span> IPS
                </p>
                <p class="text-sm mb-4">
                    Institución Prestadora de Servicios de Salud comprometida con el
                    bienestar integral de las familias del Tolima desde el 2000.
                </p>
            </div>

            <!-- En móvil va a la IZQUIERDA -->
            <div>
                <p class="text-white font-semibold mb-3">Servicios</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/servicios#medicina-general') }}" class="hover:text-white">Medicina General</a></li>
                    <li><a href="{{ url('/servicios#cardiologia') }}" class="hover:text-white">Cardiología</a></li>
                    <li><a href="{{ url('/servicios#pediatria') }}" class="hover:text-white">Pediatría</a></li>
                </ul>
            </div>

            <!-- En móvil va a la DERECHA -->
            <div>
                <p class="text-white font-semibold mb-3">Navegación</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/') }}" class="hover:text-white">Inicio</a></li>
                    <li><a href="{{ route('nosotros') }}" class="hover:text-white">Nosotros</a></li>
                    <li><a href="{{ route('servicios.index') }}" class="hover:text-white">Servicios</a></li>
                    <li><a href="{{ route('contacto.index') }}" class="hover:text-white">Contacto</a></li>
                    <li><a href="{{ route('planes.index') }}" class="hover:text-white">Planes</a></li>
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

            <div>
                <p class="text-white font-semibold mb-3">Contacto</p>
                <ul class="space-y-2 text-sm">
                    <li>Calle 42 #5-28, Ibagué</li>
                    <li>(601) 123-4667</li>
                    <li>info@prismasaludips.com</li>
                </ul>
            </div>
        </div>

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

        // Contador animado de las cifras (igual que en el index)
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

        // Acordeon de preguntas frecuentes institucionales
        document.querySelectorAll('.faq-institucional-item').forEach(function (item) {
            const boton = item.querySelector('.faq-institucional-pregunta');
            const respuesta = item.querySelector('.faq-institucional-respuesta');
            const icono = item.querySelector('.faq-institucional-icono');

            boton.addEventListener('click', function () {
                const abierta = boton.getAttribute('aria-expanded') === 'true';

                document.querySelectorAll('.faq-institucional-item .faq-institucional-pregunta[aria-expanded="true"]').forEach(function (otroBoton) {
                    if (otroBoton !== boton) {
                        otroBoton.setAttribute('aria-expanded', 'false');
                        otroBoton.closest('.faq-institucional-item').querySelector('.faq-institucional-respuesta').style.maxHeight = '0px';
                        otroBoton.closest('.faq-institucional-item').querySelector('.faq-institucional-icono').style.transform = 'rotate(0deg)';
                    }
                });

                boton.setAttribute('aria-expanded', String(!abierta));
                respuesta.style.maxHeight = abierta ? '0px' : respuesta.scrollHeight + 'px';
                icono.style.transform = abierta ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        });
    </script>

    @include('partials.cookies')

</body>
</html>