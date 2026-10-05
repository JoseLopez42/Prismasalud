<!-- blog.blade.php - Listado de articulos -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - PrismaSalud IPS</title>

    <meta name="description" content="Artículos y consejos de salud de PrismaSalud IPS: prevención, salud infantil, chequeos preventivos, salud mental y más.">

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

        .tarjeta-articulo {
            transition: transform .2s ease, box-shadow .2s ease;
            cursor: default;
        }
        .tarjeta-articulo:hover {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 14px 28px rgba(0,0,0,0.14);
        }

        .chip-categoria {
            transition: transform .15s ease, opacity .15s ease;
        }
        .chip-categoria:hover { transform: translateY(-2px); }

        /* Barra de categorias: se queda pegada debajo del header al hacer scroll */
        .barra-categorias {
            position: sticky;
            top: 80px;
            z-index: 40;
            background-color: #ffffff;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
        }
        .barra-categorias .chip-categoria { flex-shrink: 0; }
    </style>
</head>
<body class="bg-white text-gray-800">

    <!-- Navbar (igual al resto del sitio) -->
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
                <a href="{{ route('servicios.index') }}" class="nav-link">Servicios</a>
                <a href="{{ route('blog.index') }}" class="nav-link" style="color:var(--teal);">Blog</a>
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
                <a href="{{ route('blog.index') }}" class="drawer-link" style="color:var(--teal);">
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

    <!-- Hero -->
    <section class="gradiente-salud text-white">
        <div class="max-w-4xl mx-auto px-6 py-16 text-center">
            <span class="inline-block bg-white/10 text-white text-xs px-3 py-1 rounded-full mb-4">
                Blog
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                Consejos para cuidar <span class="text-teal-300">tu salud</span>
            </h1>
            <p class="text-white/80 max-w-2xl mx-auto">
                Artículos escritos por nuestro equipo médico, pensados para que entiendas
                tu salud y la de tu familia de forma clara y sencilla.
            </p>
        </div>
    </section>

    <!-- Categorias (sticky: se mantiene visible al bajar) -->
    <section id="barra-categorias" class="barra-categorias bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-4 sm:py-6 flex flex-wrap justify-center gap-2 sm:gap-3">
            <button type="button" class="chip-categoria activo text-sm font-semibold px-4 py-2 rounded-full border"
                    data-categoria="" data-color="var(--teal)"
                    style="border-color: var(--teal); color: #ffffff; background-color: var(--teal);">Todos</button>
            <button type="button" class="chip-categoria text-sm font-semibold px-4 py-2 rounded-full border"
                    data-categoria="Prevención" data-color="var(--teal)"
                    style="border-color: var(--teal); color: var(--teal);">Prevención</button>
            <button type="button" class="chip-categoria text-sm font-semibold px-4 py-2 rounded-full border"
                    data-categoria="Salud infantil" data-color="var(--azul-claro)"
                    style="border-color: var(--azul-claro); color: var(--azul-claro);">Salud infantil</button>
            <button type="button" class="chip-categoria text-sm font-semibold px-4 py-2 rounded-full border"
                    data-categoria="Salud mental" data-color="var(--naranja)"
                    style="border-color: var(--naranja); color: var(--naranja);">Salud mental</button>
            <button type="button" class="chip-categoria text-sm font-semibold px-4 py-2 rounded-full border"
                    data-categoria="Exámenes médicos" data-color="var(--teal)"
                    style="border-color: var(--teal); color: var(--teal);">Exámenes médicos</button>
            <button type="button" class="chip-categoria text-sm font-semibold px-4 py-2 rounded-full border"
                    data-categoria="EPS y trámites" data-color="var(--azul-claro)"
                    style="border-color: var(--azul-claro); color: var(--azul-claro);">EPS y trámites</button>
        </div>
    </section>

    <!-- Articulo destacado -->
    <section id="articulo-destacado" data-categoria="Prevención" class="max-w-7xl mx-auto px-6 py-16">
        <div class="reveal grid md:grid-cols-2 gap-8 items-center border border-gray-200 rounded-2xl overflow-hidden">
            <div class="icono-c2 h-64 md:h-full flex items-center justify-center text-6xl">
                🫀
            </div>
            <div class="p-8">
                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full mb-3" style="background:#e6f9f6; color: var(--teal);">Destacado · Prevención</span>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">5 hábitos simples para cuidar tu corazón</h2>
                <p class="text-gray-500 mb-4">
                    Pequeños cambios en tu rutina diaria pueden reducir de forma significativa
                    el riesgo cardiovascular a largo plazo. Aquí te contamos cuáles.
                </p>
                <div class="flex items-center gap-3 text-xs text-gray-400 mb-4">
                    <span>10 sep 2026</span>
                    <span>•</span>
                    <span>Dr. Carlos Mendoza, Cardiología</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Listado de articulos -->
    <section id="listado-articulos" class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-6">
            @php
                $articulos = [
                    ['id' => 'infantil', 'color' => 'icono-c2', 'icono' => '💉', 'categoria' => 'Salud infantil', 'fecha' => '2 sep 2026', 'autor' => 'Dra. Laura Torres, Pediatría', 'titulo' => 'Calendario de vacunación infantil', 'resumen' => 'Qué vacunas necesita tu hijo y en qué edades, según el esquema nacional de vacunación.'],
                    ['id' => 'mental', 'color' => 'icono-c3', 'icono' => '🧠', 'categoria' => 'Salud mental', 'fecha' => '25 ago 2026', 'autor' => 'Equipo de Psicología', 'titulo' => 'Manejo del estrés en el trabajo', 'resumen' => 'Estrategias simples respaldadas por psicología para el día a día laboral.'],
                    ['id' => 'prevencion', 'color' => 'icono-c1', 'icono' => '🤒', 'categoria' => 'Prevención', 'fecha' => '18 ago 2026', 'autor' => 'Medicina General', 'titulo' => 'Resfriado, gripe o algo más: cómo diferenciarlos', 'resumen' => 'Aprende a identificar los síntomas y cuándo es momento de consultar al médico.'],
                    ['id' => 'examenes', 'color' => 'icono-c2', 'icono' => '🧪', 'categoria' => 'Exámenes médicos', 'fecha' => '10 ago 2026', 'autor' => 'Laboratorio Clínico', 'titulo' => 'Cómo prepararte para un examen de sangre', 'resumen' => 'Ayuno, hidratación y todo lo que debes saber antes de tu toma de muestra.'],
                    ['id' => 'infantil-2', 'color' => 'icono-c1', 'icono' => '🍎', 'categoria' => 'Salud infantil', 'fecha' => '2 ago 2026', 'autor' => 'Dra. Laura Torres, Pediatría', 'titulo' => 'Alimentación saludable para niños en edad escolar', 'resumen' => 'Loncheras balanceadas y hábitos alimenticios que sí funcionan.'],
                    ['id' => 'tramites', 'color' => 'icono-c3', 'icono' => '📋', 'categoria' => 'EPS y trámites', 'fecha' => '25 jul 2026', 'autor' => 'Atención al usuario', 'titulo' => 'Contributivo vs. subsidiado: la diferencia explicada simple', 'resumen' => 'Entiende a qué régimen perteneces y qué significa para tu atención.'],
                    ['id' => 'prevencion-2', 'color' => 'icono-c2', 'icono' => '❤️', 'categoria' => 'Prevención', 'fecha' => '18 jul 2026', 'autor' => 'Cardiología', 'titulo' => 'Chequeos que toda mujer debería hacerse según su edad', 'resumen' => 'Una guía de exámenes preventivos recomendados década por década.'],
                    ['id' => 'mental-2', 'color' => 'icono-c1', 'icono' => '🌿', 'categoria' => 'Salud mental', 'fecha' => '10 jul 2026', 'autor' => 'Equipo de Psicología', 'titulo' => 'Técnicas de respiración para momentos de ansiedad', 'resumen' => 'Ejercicios simples que puedes practicar en cualquier lugar, en minutos.'],
                ];
            @endphp

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($articulos as $art)
                    <div data-categoria="{{ $art['categoria'] }}" class="reveal tarjeta-articulo block bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="{{ $art['color'] }} h-32 flex items-center justify-center text-4xl">
                            {{ $art['icono'] }}
                        </div>
                        <div class="p-5">
                            <p class="text-xs font-semibold mb-2" style="color: var(--teal);">{{ $art['categoria'] }}</p>
                            <h3 class="font-semibold text-gray-900 mb-2 leading-snug">{{ $art['titulo'] }}</h3>
                            <p class="text-sm text-gray-500 mb-3">{{ $art['resumen'] }}</p>
                            <div class="text-xs text-gray-400">
                                <p>{{ $art['fecha'] }}</p>
                                <p>{{ $art['autor'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <p id="aviso-categoria-vacia" class="text-sm text-gray-500 mt-8 text-center" style="display:none;">
                No hay artículos en esta categoría por ahora.
            </p>

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
        function abrirMenuMovil() { drawerMovil.classList.add('abierto'); overlayMovil.classList.add('abierto'); }
        function cerrarMenuMovil() { drawerMovil.classList.remove('abierto'); overlayMovil.classList.remove('abierto'); }
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

        // Filtro por categoria (chips superiores)
        const chips = document.querySelectorAll('.chip-categoria');
        const tarjetasFiltrables = document.querySelectorAll('.tarjeta-articulo, #articulo-destacado');
        const avisoVacio = document.getElementById('aviso-categoria-vacia');
        const listadoArticulos = document.getElementById('listado-articulos');

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                const categoria = chip.dataset.categoria;

                // Chip activo (relleno con su propio color)
                chips.forEach(function (otro) {
                    const activo = otro === chip;
                    otro.classList.toggle('activo', activo);
                    otro.style.backgroundColor = activo ? otro.dataset.color : 'transparent';
                    otro.style.color = activo ? '#ffffff' : otro.dataset.color;
                });

                // Mostrar solo las tarjetas de la categoria elegida
                let visibles = 0;
                tarjetasFiltrables.forEach(function (tarjeta) {
                    const coincide = categoria === '' || tarjeta.dataset.categoria === categoria;
                    tarjeta.style.display = coincide ? '' : 'none';
                    if (coincide && tarjeta.id !== 'articulo-destacado') {
                        visibles++;
                    }
                });

                avisoVacio.style.display = visibles === 0 ? 'block' : 'none';

                // Cualquier chip (incluido "Todos") lleva el listado completo a la vista,
                // dejando libre el espacio del header + la barra de chips fija.
                centrarListado();
            });
        });

        // La barra de categorias siempre queda justo debajo del header fijo
        const barraCategorias = document.getElementById('barra-categorias');
        function ajustarPosicionBarra() {
            barraCategorias.style.top = document.getElementById('site-header').offsetHeight + 'px';
        }
        ajustarPosicionBarra();
        window.addEventListener('resize', ajustarPosicionBarra);
        window.addEventListener('load', ajustarPosicionBarra);

        // Baja hasta el listado posicionando la primera fila por debajo de
        // header + barra de chips (así no se corta ninguna tarjeta).
        function centrarListado() {
            const headerH = document.getElementById('site-header').offsetHeight;
            const barraH = barraCategorias.offsetHeight;
            const desplome = listadoArticulos.getBoundingClientRect().top + window.scrollY;
            const destino = desplome - (headerH + barraH + 16);
            window.scrollTo({ top: Math.max(destino, 0), behavior: 'smooth' });
        }
    </script>

    @include('partials.cookies')

</body>
</html>