<!-- servicios.blade.php - Catalogo de servicios -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios - PrismaSalud IPS</title>

    <meta name="description" content="Catálogo de servicios de PrismaSalud IPS: especialidades médicas, servicios de apoyo, tiempos, valores y cobertura EPS en Ibagué, Tolima.">

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

        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .6s ease, transform .6s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .icono-c1 { background: #e6f3fa; color: var(--azul-claro); }
        .icono-c2 { background: #e6f9f6; color: var(--teal); }
        .icono-c3 { background: #e0f7f4; color: var(--naranja); }

        .btn-outline {
            border: 1.5px solid rgba(255,255,255,0.7);
            transition: transform .2s ease, background-color .2s ease;
        }
        .btn-outline:hover { transform: translateY(-2px); }

        .tarjeta-especialidad {
            position: relative;
            overflow: hidden;
        }
        .tarjeta-especialidad::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, var(--azul-claro), var(--teal), var(--naranja));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .3s ease;
        }
        .tarjeta-especialidad:hover::before { transform: scaleX(1); }
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
                <a href="{{ route('nosotros') }}" class="nav-link">Nosotros</a>
                <a href="{{ route('servicios.index') }}" class="nav-link" style="color:var(--teal);">Servicios</a>
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
                <a href="{{ route('nosotros') }}" class="drawer-link">
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

    <!-- Hero de la pagina (sin botones duplicados) -->
    <section class="gradiente-salud text-white">
        <div class="max-w-4xl mx-auto px-6 py-16 text-center">
            <span class="inline-block bg-white/10 text-white text-xs px-3 py-1 rounded-full mb-4">
                Catálogo de servicios
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                Todos nuestros <span class="text-teal-300">servicios</span>
            </h1>
            <p class="text-white/80 max-w-2xl mx-auto">
                Consultas, especialidades, diagnóstico y servicios de apoyo en un solo lugar.
                Elige el que necesitas, revisa sus tiempos y agendas en línea cuando quieras.
            </p>
        </div>
    </section>

    <!-- Atajos por categoria -->
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-6 flex flex-wrap justify-center gap-3">
            <a href="#catalogo" class="text-sm font-semibold px-4 py-2 rounded-full border transition hover:bg-gray-50" style="border-color: var(--teal); color: var(--teal);">Especialidades médicas</a>
            <a href="#apoyo" class="text-sm font-semibold px-4 py-2 rounded-full border transition hover:bg-gray-50" style="border-color: var(--azul-claro); color: var(--azul-claro);">Servicios de apoyo</a>
            <a href="#valores" class="text-sm font-semibold px-4 py-2 rounded-full border transition hover:bg-gray-50" style="border-color: var(--naranja); color: var(--naranja);">Tiempos y valores</a>
            <a href="#cobertura" class="text-sm font-semibold px-4 py-2 rounded-full border transition hover:bg-gray-50" style="border-color: var(--teal); color: var(--teal);">Cobertura y EPS</a>
            <a href="#preguntas" class="text-sm font-semibold px-4 py-2 rounded-full border transition hover:bg-gray-50" style="border-color: var(--azul-claro); color: var(--azul-claro);">Preguntas frecuentes</a>
        </div>
    </section>

    <!-- Especialidades -->
    <section id="catalogo" class="max-w-7xl mx-auto px-6 py-20">
        <span class="inline-block border text-xs px-3 py-1 rounded-full mb-4" style="border-color: var(--teal); color: var(--teal);">
            Especialidades médicas
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
                ['id' => 'medicina-general', 'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l5-5 4 4 8-8M15 8h5v5"/></svg>', 'titulo' => 'Medicina General', 'texto' => 'Consulta preventiva, diagnóstico y tratamiento de enfermedades comunes para pacientes de todas las edades.', 'color' => 'icono-c1'],
                ['id' => 'cardiologia', 'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6.5-4.35-9.3-8.4C.8 9.4 1.9 5.6 5.3 4.6c2-.6 4 .1 5.2 1.7l1.5 2 1.5-2c1.2-1.6 3.2-2.3 5.2-1.7 3.4 1 4.5 4.8 2.6 8-2.8 4.05-9.3 8.4-9.3 8.4z"/></svg>', 'titulo' => 'Cardiología', 'texto' => 'Evaluación cardiovascular con equipos de última generación. Electrocardiogramas, ecocardiogramas y más.', 'color' => 'icono-c3'],
                ['id' => 'pediatria', 'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" d="M4 21c0-4 3.5-6 8-6s8 2 8 6"/></svg>', 'titulo' => 'Pediatría', 'texto' => 'Cuidado especializado para niños desde el nacimiento hasta la adolescencia. Controles de crecimiento y vacunación.', 'color' => 'icono-c2'],
                ['id' => 'ginecologia', 'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 11v9M8.5 16.5h7"/></svg>', 'titulo' => 'Ginecología y Obstetricia', 'texto' => 'Controles prenicial, planificación familiar y control ginecológico de rutina con total confidencialidad.', 'color' => 'icono-c1'],
                ['id' => 'odontologia', 'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/></svg>', 'titulo' => 'Odontología', 'texto' => 'Salud oral completa: limpieza, ortodoncia, endodoncia y cirugía oral en un ambiente cómodo y moderno.', 'color' => 'icono-c3'],
                ['id' => 'psicologia', 'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.5 3a3.5 3.5 0 00-3.5 3.5c0 .35.04.7.13 1.02A3.5 3.5 0 003 11a3.5 3.5 0 002.13 3.22A3.5 3.5 0 009.5 19a3.5 3.5 0 003.5-3.5V6.5A3.5 3.5 0 009.5 3zM14.5 3a3.5 3.5 0 013.5 3.5c0 .35-.04.7-.13 1.02A3.5 3.5 0 0121 11a3.5 3.5 0 01-2.13 3.22A3.5 3.5 0 0114.5 19a3.5 3.5 0 01-3.5-3.5V6.5A3.5 3.5 0 0114.5 3z"/></svg>', 'titulo' => 'Psicología', 'texto' => 'Acompañamiento psicológico para el manejo del estrés, ansiedad, depresión y bienestar emocional.', 'color' => 'icono-c2'],
                ['id' => 'laboratorio', 'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 2v6L4 18a2 2 0 002 3h12a2 2 0 002-3L15 8V2M9 2h6M8 15h8"/></svg>', 'titulo' => 'Laboratorio Clínico', 'texto' => 'Análisis de sangre, orina, cultivos y más. Resultados rápidos y entrega digital desde nuestra plataforma.', 'color' => 'icono-c1'],
                ['id' => 'imagenologia', 'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V6a2 2 0 012-2h2M16 4h2a2 2 0 012 2v2M20 16v2a2 2 0 01-2 2h-2M8 20H6a2 2 0 01-2-2v-2"/><circle cx="12" cy="11" r="3"/></svg>', 'titulo' => 'Imagenología', 'texto' => 'Ecografías y estudios diagnósticos con informe del especialista el mismo día de tu cita.', 'color' => 'icono-c3'],
            ];
        @endphp

        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($especialidades as $esp)
                <div id="{{ $esp['id'] }}" class="reveal tarjeta-especialidad border border-gray-200 rounded-xl p-6 scroll-mt-28 hover:shadow-lg transition">
                    <div class="w-11 h-11 flex items-center justify-center {{ $esp['color'] }} rounded-lg mb-3">
                        {!! $esp['icono'] !!}
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $esp['titulo'] }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $esp['texto'] }}</p>
                    <a href="#valores" class="text-sm font-semibold hover:underline" style="color: var(--teal);">Ver tiempo y valor →</a>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Servicios de apoyo -->
    <section id="apoyo" class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-14">
                <span class="inline-block border text-xs px-3 py-1 rounded-full mb-4" style="border-color: var(--azul-claro); color: var(--azul-claro);">
                    Servicios de apoyo
                </span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Todo lo demás que ponemos a tu disposición
                </h2>
            </div>

            @php
                $serviciosApoyo = [
                    ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 20.5L3.5 13.5a5 5 0 117.07-7.07l7 7a5 5 0 01-7.07 7.07z"/><line x1="8.5" y1="8.5" x2="15.5" y2="15.5" stroke-linecap="round"/></svg>', 'titulo' => 'Farmacia interna', 'texto' => 'Medicamentos con hasta 40% de descuento para afiliados EPS, sin necesidad de cita.', 'color' => 'icono-c2'],
                    ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18M9 15l2 2 4-4"/></svg>', 'titulo' => 'Citas en línea', 'texto' => 'Agenda, reprograma o cancela tu cita desde el celular, las 24 horas.', 'color' => 'icono-c1'],
                    ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6M9 13h6M9 17h6"/></svg>', 'titulo' => 'Resultados digitales', 'texto' => 'Consulta tus estudios de laboratorio e imagen desde tu cuenta el mismo día.', 'color' => 'icono-c3'],
                    ['icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>', 'titulo' => 'Autorizaciones y PQRS', 'texto' => 'Gestiona autorizaciones, radica peticiones y da seguimiento a tus trámites.', 'color' => 'icono-c2'],
                ];
            @endphp

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($serviciosApoyo as $sv)
                    <div class="reveal bg-white border border-gray-200 rounded-xl p-6 text-center hover:shadow-lg transition">
                        <div class="w-12 h-12 mx-auto flex items-center justify-center {{ $sv['color'] }} rounded-full mb-3">
                            {!! $sv['icono'] !!}
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $sv['titulo'] }}</h3>
                        <p class="text-sm text-gray-500">{{ $sv['texto'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Tiempos y valores -->
    <section id="valores" class="max-w-6xl mx-auto px-6 py-20 scroll-mt-24">
        <span class="inline-block border text-xs px-3 py-1 rounded-full mb-4" style="border-color: var(--naranja); color: var(--naranja);">
            Tiempos y valores
        </span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3">
            Sabes en cuánto tiempo
            <span style="background: linear-gradient(90deg, var(--azul-claro), var(--teal)); -webkit-background-clip: text; background-clip: text; color: transparent;">y cuánto cuesta</span>
        </h2>
        <p class="text-gray-500 mb-8 max-w-2xl">
            Referencia de duración y valor para consulta particular. Si estás afiliado a
            una EPS con convenio, aplica el copago definido por tu plan.
        </p>

        <div class="reveal overflow-x-auto border border-gray-200 rounded-xl">
            <table class="w-full text-sm text-left">
                <thead style="background: linear-gradient(120deg, var(--azul), var(--teal)); color: #ffffff;">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Servicio</th>
                        <th class="px-5 py-3 font-semibold">Duración</th>
                        <th class="px-5 py-3 font-semibold">Valor refer.</th>
                        <th class="px-5 py-3 font-semibold">Disponibilidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @php
                        $portafolio = [
                            ['Medicina general',    '20 min',  '$35.000',         'Lun a Sáb'],
                            ['Cardiología',         '30 min',  '$75.000',         'Mar y Jue'],
                            ['Pediatría',           '25 min',  '$60.000',         'Lun a Vie'],
                            ['Odontología',         '40 min',  '$70.000',         'Lun a Vie'],
                            ['Psicología',          '50 min',  '$55.000',         'Lun a Sáb'],
                            ['Laboratorio clínico', '15 min',  'desde $22.000',   'Lun-Vie 6:30 a.m.'],
                            ['Electrocardiograma',  '20 min',  '$45.000',         'Lun a Vie'],
                            ['Ecocardiograma',      '40 min',  '$120.000',        'Mar y Jue'],
                            ['Farmacia interna',    'Sin cita','hasta 40% dcto.', 'Todos los días'],
                        ];
                    @endphp
                    @foreach ($portafolio as $fila)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3 font-semibold text-gray-900">{{ $fila[0] }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $fila[1] }}</td>
                            <td class="px-5 py-3 font-semibold" style="color: var(--teal);">{{ $fila[2] }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $fila[3] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 bg-gray-50 border-l-4 rounded-r-lg p-5 text-sm text-gray-600 space-y-2" style="border-color: var(--teal);">
            <p><strong class="text-gray-900">Valores de referencia:</strong> se actualizan cada semestre y no incluyen procedimientos adicionales que el médico pueda ordenar durante la consulta.</p>
            <p><strong class="text-gray-900">Convenio EPS:</strong> el copago por consulta particular suele ser inferior al 30% del valor lista; verifica tu porcentaje en ventanilla o en la plataforma de citas.</p>
            <p><strong class="text-gray-900">Laboratorio:</strong> la toma de muestras inicia a las 6:30 a.m. en ayuno; los resultados quedan disponibles en tu cuenta el mismo día antes de las 6:00 p.m.</p>
        </div>
    </section>

    <!-- Cobertura y EPS -->
    <section id="cobertura" class="bg-gray-50 py-20 scroll-mt-24">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-12">
                <span class="inline-block border text-xs px-3 py-1 rounded-full mb-4" style="border-color: var(--teal); color: var(--teal);">
                    Cobertura
                </span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Convenios, régimen y trámites
                </h2>
                <p class="text-gray-500 mt-3 max-w-2xl mx-auto">
                    Trabajamos con las principales aseguradoras del país bajo convenio vigente.
                    Antes de tu primera consulta verificamos tu afiliación y, si aplica,
                    tramitamos la autorización por ti.
                </p>
            </div>

            <div class="reveal bg-white border border-gray-200 rounded-xl p-6 mb-8">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-4">EPS con convenio vigente</p>
                <div class="flex flex-wrap gap-x-8 gap-y-3">
                    @php $eps = ['Sura', 'Nueva EPS', 'Sanitas', 'Compensar', 'Famisanar', 'Salud Total']; @endphp
                    @foreach ($eps as $nombreEps)
                        <span class="font-bold text-gray-700" style="color: var(--naranja);">{{ $nombreEps }}</span>
                    @endforeach
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="reveal bg-white border border-gray-200 rounded-xl p-6">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-4">Regímenes atendidos</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex gap-3"><span class="mt-1.5 w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: var(--teal);"></span><span><strong class="text-gray-900">Contributivo:</strong> trabajadores independientes y dependientes, pensionados y cotizantes voluntarios.</span></li>
                        <li class="flex gap-3"><span class="mt-1.5 w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: var(--teal);"></span><span><strong class="text-gray-900">Subsidiado:</strong> familias en situación de afiliación SISBÉN grupo 1 y grupo 2.</span></li>
                        <li class="flex gap-3"><span class="mt-1.5 w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: var(--teal);"></span><span><strong class="text-gray-900">Grupos especiales:</strong> fuerzas militares, policía nacional y docentes del sector oficial.</span></li>
                    </ul>
                </div>

                <div class="reveal bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 px-6 pt-6 pb-4">Trámites y tiempos de respuesta</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase tracking-wide" style="background: #f0fdfa; color: var(--teal);">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Trámite</th>
                                    <th class="px-4 py-3 font-semibold">Canal</th>
                                    <th class="px-4 py-3 font-semibold">Tiempo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php
                                    $tramites = [
                                        ['Verificación de vigencia', 'Ventanilla o teléfono', 'Inmediato'],
                                        ['Autorización de servicios', 'EPS + plataforma web', '24 a 48 horas'],
                                        ['Copia de historia clínica', 'Derechos del paciente', '5 días hábiles'],
                                        ['Radicación de facturas', 'Facturación', '10 días hábiles'],
                                        ['Cambio de médico tratante', 'Portal del paciente', '24 horas'],
                                    ];
                                @endphp
                                @foreach ($tramites as $t)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3 font-semibold text-gray-900">{{ $t[0] }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $t[1] }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $t[2] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <p class="text-sm text-gray-500 text-center max-w-3xl mx-auto">
                Si tu EPS no aparece en la lista, puedes agendar como particular o pedir que la
                incluyamos escribiendo a <a href="mailto:convenios@prismasaludips.com" class="font-semibold hover:underline" style="color: var(--teal);">convenios@prismasaludips.com</a>.
                Actualizamos los convenios cada año en enero.
            </p>
        </div>
    </section>

    <!-- Como agendar tu cita -->
    <section class="bg-white py-20">
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

    <!-- Preguntas frecuentes de servicios -->
    <section id="preguntas" class="bg-gray-50 py-20 scroll-mt-24">
        <div class="max-w-3xl mx-auto px-6">
            <div class="text-center mb-10">
                <span class="inline-block border text-xs px-3 py-1 rounded-full mb-4" style="border-color: var(--teal); color: var(--teal);">
                    Preguntas frecuentes
                </span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Dudas sobre nuestros servicios
                </h2>
            </div>

            @php
                $preguntasServicios = [
                    ['pregunta' => '¿Necesito autorización de mi EPS para agendar?', 'respuesta' => 'Depende del servicio. Para consulta de medicina general y control de pediatría normalmente no la necesitas. Para especialidades y procedimientos, tu EPS debe autorizarlos; nosotros hacemos el trámite y queda listo en 24 a 48 horas.'],
                    ['pregunta' => '¿Atienden sin cita?', 'respuesta' => 'La farmacia interna funciona sin cita todos los días. Para consultas médicas pedimos cita para garantizar tiempos de espera cortos, aunque en caso de urgencia te atendemos el mismo día según disponibilidad.'],
                    ['pregunta' => '¿Cuánto demora un resultado de laboratorio?', 'respuesta' => 'La toma de muestras inicia a las 6:30 a.m. en ayuno. La mayoría de los resultados quedan disponibles en tu cuenta el mismo día antes de las 6:00 p.m.; los cultivos pueden demorar hasta 72 horas.'],
                    ['pregunta' => '¿Cómo sé cuánto es mi copago?', 'respuesta' => 'Puedes verificarlo en ventanilla con tu cédula antes de la consulta o en la plataforma de citas junto con la vigencia de tu plan. Como referencia, el copago por consulta particular suele ser inferior al 30% del valor de lista.'],
                    ['pregunta' => '¿Atienden a pacientes particulares?', 'respuesta' => 'Sí. Si tu EPS no aparece en nuestra lista de convenios puedes agendar como particular con los valores de referencia publicados en esta página.'],
                ];
            @endphp

            <div class="space-y-3">
                @foreach ($preguntasServicios as $p)
                    <div class="faq-institucional-item border border-gray-200 rounded-lg bg-white overflow-hidden">
                        <button type="button"
                                class="faq-institucional-pregunta w-full flex items-center justify-between text-left px-5 py-4 font-semibold text-gray-800"
                                aria-expanded="false">
                            <span>{{ $p['pregunta'] }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="faq-institucional-icono w-5 h-5 flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="faq-institucional-respuesta px-5 text-sm text-gray-500" style="max-height: 0; overflow: hidden; transition: max-height .3s ease;">
                            <div class="pb-4">{{ $p['respuesta'] }}</div>
                        </div>
                    </div>
                @endforeach
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

            <div>
                <p class="text-white font-semibold mb-3">Servicios</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/servicios#medicina-general') }}" class="hover:text-white">Medicina General</a></li>
                    <li><a href="{{ url('/servicios#cardiologia') }}" class="hover:text-white">Cardiología</a></li>
                    <li><a href="{{ url('/servicios#pediatria') }}" class="hover:text-white">Pediatría</a></li>
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

        // Acordeon de preguntas frecuentes de servicios
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