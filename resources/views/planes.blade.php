<!-- planes.blade.php - Planes del software PrismaSalud (vista estática, sin modelo ni rutas) -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes del software - PrismaSalud IPS</title>

    <meta name="description" content="Conoce los planes del software PrismaSalud para IPS, consultorios y equipos de salud. Comienza gratis o elige el plan que se adapte a tu operación.">

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
            --verde-oscuro: #14532D;
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
            background-color: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.07);
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
            inset: 0;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.85rem 1.5rem;
            background: linear-gradient(120deg, var(--naranja), var(--teal));
            background-size: 180% 180%;
            color: #ffffff;
            font-weight: 700;
            border-radius: 0.6rem;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
            transition: transform .2s ease, box-shadow .2s ease, background-position .4s ease;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.24);
            background-position: 100% 50%;
        }

        .reveal {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity .6s ease, transform .6s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .hero-orbit {
            position: absolute;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-orbit--one { width: 27rem; height: 27rem; right: -9rem; top: -9rem; }
        .hero-orbit--two { width: 18rem; height: 18rem; right: 4rem; bottom: -10rem; }

        .mini-dashboard {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            box-shadow: 0 22px 55px rgba(8, 47, 73, 0.28);
            backdrop-filter: blur(12px);
        }

        .mini-stat {
            background: rgba(255, 255, 255, 0.13);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .plan-card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            height: 100%;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 1.15rem;
            padding: 1.5rem;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .plan-card:hover {
            transform: translateY(-6px);
            border-color: #99f6e4;
            box-shadow: 0 18px 38px rgba(15, 118, 110, 0.12);
        }

        .plan-card--featured {
            border: 2px solid var(--teal);
            box-shadow: 0 18px 42px rgba(18, 165, 148, 0.18);
        }

        .plan-card--free { background: linear-gradient(180deg, #f0fdfa 0%, #ffffff 38%); }

        .plan-badge {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            border-radius: 9999px;
            padding: 0.35rem 0.8rem;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .plan-badge--featured { color: #ffffff; background: var(--teal); }
        .plan-badge--free { color: #0f766e; background: #ccfbf1; }

        .plan-icon {
            width: 2.7rem;
            height: 2.7rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .8rem;
            margin-bottom: 1rem;
        }

        .plan-icon svg { width: 1.4rem; height: 1.4rem; }
        .plan-icon--teal { color: #0f766e; background: #ccfbf1; }
        .plan-icon--blue { color: #1d4ed8; background: #dbeafe; }
        .plan-icon--purple { color: #7e22ce; background: #f3e8ff; }
        .plan-icon--orange { color: #c2410c; background: #ffedd5; }
        .plan-icon--pink { color: #be185d; background: #fce7f3; }
        .plan-icon--green { color: #15803d; background: #dcfce7; }

        .plan-title { color: #111827; font-size: 1.2rem; font-weight: 800; }
        .plan-description { min-height: 5rem; color: #6b7280; font-size: .82rem; line-height: 1.5; }
        .plan-price { color: #111827; font-size: 2.15rem; font-weight: 800; line-height: 1; letter-spacing: -.04em; }
        .plan-price-currency { margin-right: .15rem; color: var(--teal); font-size: 1rem; vertical-align: top; }
        .plan-period { color: #9ca3af; font-size: .7rem; }

        .plan-ideal { min-height: 4.5rem; }
        .plan-summary { min-height: 9.5rem; }

        .plan-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 2.75rem;
            margin: 1.15rem 0;
            padding: .65rem 1rem;
            border-radius: .65rem;
            font-size: .8rem;
            font-weight: 800;
            white-space: nowrap;
            transition: transform .15s ease, background-color .15s ease, border-color .15s ease;
        }

        .plan-cta:hover { transform: translateY(-2px); }
        .plan-cta--primary { color: #ffffff; background: var(--teal); }
        .plan-cta--primary:hover { background: #0f766e; }
        .plan-cta--outline { color: #0f766e; background: #ffffff; border: 1px solid #99f6e4; }
        .plan-cta--outline:hover { background: #f0fdfa; }

        .plan-list { display: grid; gap: .7rem; padding-top: 1rem; border-top: 1px solid #f3f4f6; }
        .plan-list li { display: flex; align-items: flex-start; gap: .55rem; color: #4b5563; font-size: .78rem; line-height: 1.4; }
        .plan-list svg { width: 1rem; height: 1rem; flex: 0 0 auto; margin-top: .1rem; color: var(--teal); }

        .plan-more {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            width: 100%;
            min-height: 2.4rem;
            padding: .55rem .8rem;
            border: 1px solid #99f6e4;
            border-radius: .6rem;
            color: #0f766e;
            background: #ffffff;
            font-size: .75rem;
            font-weight: 800;
            cursor: pointer;
            transition: background-color .15s ease, border-color .15s ease;
        }
        .plan-more:hover { background: #f0fdfa; border-color: var(--teal); }
        .plan-more svg { width: .9rem; height: .9rem; }

        .comparison-table { min-width: 960px; width: 100%; border-collapse: separate; border-spacing: 0; }
        .comparison-table th,
        .comparison-table td { padding: .95rem 1rem; border-bottom: 1px solid #e5e7eb; text-align: center; font-size: .78rem; }
        .comparison-table th:first-child,
        .comparison-table td:first-child { width: 30%; text-align: left; }
        .comparison-table thead th { color: #374151; background: #f8fafc; font-weight: 800; }
        .comparison-table thead th:not(:first-child) { color: #0f766e; }
        .comparison-table tbody tr:last-child td { border-bottom: 0; }
        .comparison-table tbody tr:hover td { background: #f8fffd; }
        .table-check { color: var(--teal); font-weight: 800; }
        .table-dash { color: #cbd5e1; }

        .faq-item { border: 1px solid #e5e7eb; border-radius: .85rem; background: #ffffff; overflow: hidden; }
        .faq-question { display: flex; align-items: center; justify-content: space-between; gap: 1rem; width: 100%; padding: 1.1rem 1.2rem; color: #1f2937; text-align: left; font-size: .9rem; font-weight: 800; }
        .faq-question:hover { color: var(--teal); background: #f0fdfa; }
        .faq-icon { flex: 0 0 auto; width: 1.5rem; height: 1.5rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; color: #0f766e; background: #ccfbf1; transition: transform .2s ease; }
        .faq-answer { max-height: 0; overflow: hidden; transition: max-height .3s ease; }
        .faq-answer p { padding: 0 1.2rem 1.1rem; color: #6b7280; font-size: .82rem; line-height: 1.6; }
        .faq-item.abierto .faq-icon { transform: rotate(45deg); }

        .footer-link { color: rgba(255, 255, 255, .76); transition: color .15s ease; }
        .footer-link:hover { color: #ffffff; }

        @media (max-width: 640px) {
            .plan-card { padding: 1.25rem; }
            .plan-description,
            .plan-ideal,
            .plan-summary { min-height: 0; }
            .hero-orbit--one { right: -15rem; }
            .hero-orbit--two { right: -5rem; }
            .comparison-table th, .comparison-table td { padding: .8rem .7rem; }
        }
    </style>
</head>
<body class="bg-white text-gray-800">

    <!-- Navbar -->
    <header id="site-header" class="site-header w-full">
        <div class="max-w-7xl mx-auto flex items-center px-6 py-4">
            <div class="flex items-center gap-2 flex-shrink-0">
                <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud IPS" class="w-9 h-9 object-contain">
                <div class="leading-tight">
                    <p class="logo-text font-bold">Prisma<span class="brand-accent">Salud</span> IPS</p>
                    <p class="logo-sub text-xs">Ibagué · Tolima</p>
                </div>
            </div>

            <nav class="hidden md:flex items-center gap-5 xl:gap-8 text-sm font-medium ml-10" aria-label="Navegación principal">
                <a href="{{ url('/') }}" class="nav-link">Inicio</a>
                <a href="{{ route('nosotros') }}" class="nav-link">Nosotros</a>
                <a href="{{ route('servicios.index') }}" class="nav-link">Servicios</a>
                <a href="{{ route('blog.index') }}" class="nav-link">Blog</a>
                <a href="{{ route('contacto.index') }}" class="nav-link text-sm">Contacto</a>
                <a href="{{ route('planes.index') }}" class="nav-link text-sm">Planes</a>
            </nav>

            <div class="hidden md:flex items-center gap-3 ml-auto">
                <a href="{{ route('login') }}"
                   class="px-4 xl:px-5 py-2 text-sm font-semibold text-white rounded-md shadow-sm hover:opacity-90 transition"
                   style="background: linear-gradient(120deg, var(--azul-claro), var(--teal));">
                    Inicio sesión
                </a>
            </div>

            <button id="boton-hamburguesa" type="button" class="md:hidden nav-link ml-auto" aria-label="Abrir menú">
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
                    <p class="font-bold text-white text-sm">Prisma<span class="text-teal-300">Salud</span> IPS</p>
                </div>
                <button id="boton-cerrar-drawer" type="button" aria-label="Cerrar menú" class="text-white/80 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto py-2" aria-label="Navegación móvil">
                <a href="{{ url('/') }}" class="drawer-link">
                    <span class="drawer-icono bg-teal-50 text-teal-600"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-8 9 8M5 10v10h14V10"/></svg></span> Inicio
                </a>
                <a href="{{ route('nosotros') }}" class="drawer-link">
                    <span class="drawer-icono bg-blue-50 text-blue-600"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" d="M4 21c0-4 3.5-6 8-6s8 2 8 6"/></svg></span> Nosotros
                </a>
                <a href="{{ route('servicios.index') }}" class="drawer-link">
                    <span class="drawer-icono bg-green-50 text-green-600"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6.5-4.35-9.3-8.4C.8 9.4 1.9 5.6 5.3 4.6c2-.6 4 .1 5.2 1.7l1.5 2 1.5-2c1.2-1.6 3.2-2.3 5.2-1.7 3.4 1 4.5 4.8 2.6 8-2.8 4.05-9.3 8.4-9.3 8.4z"/></svg></span> Servicios
                </a>
                <a href="{{ route('blog.index') }}" class="drawer-link">
                    <span class="drawer-icono bg-purple-50 text-purple-600"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path stroke-linecap="round" d="M7 8h10M7 12h10M7 16h6"/></svg></span> Blog
                </a>
                <a href="{{ route('contacto.index') }}" class="drawer-link">
                    <span class="drawer-icono bg-amber-50 text-amber-600"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg></span> Contacto
                </a>
                <a href="{{ route('planes.index') }}" class="drawer-link" style="color:var(--teal);">
                    <span class="drawer-icono bg-teal-50 text-teal-600"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M17 7.5c0-1.38-2.24-2.5-5-2.5S7 6.12 7 7.5 9.24 10 12 10s5 1.12 5 2.5S14.76 15 12 15s-5 1.12-5 2.5S9.24 20 12 20s5-1.12 5-2.5"/></svg></span> Planes
                </a>
            </nav>
            <div class="p-5 border-t border-gray-100 bg-gray-50 flex flex-col gap-3">
                <a href="{{ route('login') }}" class="px-4 py-2.5 text-sm font-semibold border rounded-md text-center bg-white" style="color:var(--teal); border-color:var(--teal);">Iniciar sesión</a>
            </div>
        </div>
    </header>

    <div class="header-spacer"></div>

    <!-- Hero -->
    <section class="gradiente-salud text-white relative overflow-hidden">
        <span class="hero-orbit hero-orbit--one"></span>
        <span class="hero-orbit hero-orbit--two"></span>
        <div class="max-w-7xl mx-auto px-6 py-16 md:py-20 grid lg:grid-cols-2 gap-12 items-center relative" style="z-index: 1;">
            <div class="reveal">
                <span class="inline-flex items-center gap-2 bg-white/10 text-white text-xs font-semibold px-3 py-1.5 rounded-full mb-5">
                    <span class="w-2 h-2 rounded-full bg-teal-300"></span> Software para la salud
                </span>
                <h1 class="text-4xl md:text-6xl font-extrabold leading-[1.05] mb-5">
                    Planes para hacer <span class="text-teal-300">crecer</span> tu operación
                </h1>
                <p class="text-white/80 text-base md:text-lg leading-relaxed max-w-xl mb-8">
                    Organiza citas, pacientes, historia clínica y equipo en una sola plataforma.
                    Comienza con una prueba gratis de un mes y escala con el plan que necesita tu IPS, consultorio o clínica.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="#planes" class="btn-cta">
                        Ver todos los planes
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="{{ route('contacto.index') }}?plan=asesoria" class="inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-white border border-white/40 rounded-lg hover:bg-white/10 transition">
                        Solicitar una demo
                    </a>
                </div>
                <div class="flex flex-wrap gap-x-6 gap-y-2 mt-7 text-xs text-white/70">
                    <span class="inline-flex items-center gap-2"><span class="text-teal-300">✓</span> Sin tarjeta para empezar</span>
                    <span class="inline-flex items-center gap-2"><span class="text-teal-300">✓</span> Soporte en español</span>
                    <span class="inline-flex items-center gap-2"><span class="text-teal-300">✓</span> Datos en la nube</span>
                </div>
            </div>

            <div class="reveal mini-dashboard rounded-2xl p-5 md:p-6">
                <div class="flex items-center justify-between gap-3 mb-5">
                    <div>
                        <p class="text-white/60 text-xs">Resumen de hoy</p>
                        <p class="text-white font-bold text-lg">Mi centro de salud</p>
                    </div>
                    <span class="rounded-full bg-teal-300/20 px-3 py-1 text-[.65rem] font-bold text-teal-200">EN VIVO</span>
                </div>
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="mini-stat rounded-xl p-3"><p class="text-white/55 text-[.65rem]">Citas</p><p class="text-white text-xl font-extrabold">24</p></div>
                    <div class="mini-stat rounded-xl p-3"><p class="text-white/55 text-[.65rem]">Pacientes</p><p class="text-white text-xl font-extrabold">186</p></div>
                    <div class="mini-stat rounded-xl p-3"><p class="text-white/55 text-[.65rem]">Ocupación</p><p class="text-white text-xl font-extrabold">82%</p></div>
                </div>
                <div class="rounded-xl bg-white/10 p-4">
                    <div class="flex items-center justify-between mb-3"><span class="text-white/70 text-xs">Agenda de mañana</span><span class="text-teal-200 text-xs font-semibold">Ver agenda →</span></div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 rounded-lg bg-white/10 px-3 py-2"><span class="text-teal-200 text-xs font-bold">08:30</span><span class="h-7 w-px bg-white/20"></span><span class="text-white/85 text-xs">Consulta general</span><span class="ml-auto rounded-full bg-emerald-300/20 px-2 py-0.5 text-[.6rem] text-emerald-200">Confirmada</span></div>
                        <div class="flex items-center gap-3 rounded-lg bg-white/10 px-3 py-2"><span class="text-teal-200 text-xs font-bold">10:00</span><span class="h-7 w-px bg-white/20"></span><span class="text-white/85 text-xs">Seguimiento</span><span class="ml-auto rounded-full bg-amber-300/20 px-2 py-0.5 text-[.6rem] text-amber-200">Pendiente</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Confianza / métricas -->
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-8 grid grid-cols-2 md:grid-cols-4 gap-5 text-center">
            <div><p class="text-2xl font-extrabold" style="color:var(--teal);">5</p><p class="text-xs text-gray-500 mt-1">planes disponibles</p></div>
            <div><p class="text-2xl font-extrabold" style="color:var(--teal);">1</p><p class="text-xs text-gray-500 mt-1">mes de prueba gratis</p></div>
            <div><p class="text-2xl font-extrabold" style="color:var(--teal);">24/7</p><p class="text-xs text-gray-500 mt-1">disponibilidad</p></div>
            <div><p class="text-2xl font-extrabold" style="color:var(--teal);">100%</p><p class="text-xs text-gray-500 mt-1">en la nube</p></div>
        </div>
    </section>

    <!-- Planes -->
    <section id="planes" class="bg-gray-50 py-20">
        <div class="max-w-[1500px] mx-auto px-6">
            <div class="reveal text-center max-w-3xl mx-auto mb-10">
                <span class="inline-block text-xs font-extrabold uppercase tracking-[.18em]" style="color:var(--teal);">Elige tu plan</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-3 mb-4">Una opción para cada etapa de tu operación</h2>
                <p class="text-gray-500 leading-relaxed">Empieza con una prueba de un mes o elige una vigencia de 1 o 2 años, con o sin entrega directa a farmacia.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-5 gap-6 items-stretch">

                <!-- Plan Gratis -->
                <article id="plan-gratis" class="plan-card plan-card--free reveal">
                    <span class="plan-badge plan-badge--free">Gratis por 1 mes</span>
                    <span class="plan-icon plan-icon--teal">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3ZM19 16l.8 2.2L22 19l-2.2.8L19 22l-.8-2.2L16 19l2.2-.8L19 16Z"/></svg>
                    </span>
                    <h3 class="plan-title">Gratis</h3>
                    <p class="mt-1 text-[.7rem] font-bold uppercase tracking-wider" style="color:var(--teal);">Prueba de 1 mes</p>
                    <p class="plan-description mt-3">Conoce la plataforma sin inversión inicial y digitaliza tu agenda durante el primer mes.</p>
                    <div class="mt-5">
                        <div class="plan-price" style="color:var(--teal);">Gratis</div>
                        <p class="plan-period mt-2">1 mes · sin tarjeta</p>
                    </div>
                    <button type="button" class="plan-cta plan-cta--primary">
                        Comenzar gratis
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                    <div class="plan-ideal mb-3">
                        <p class="text-[.68rem] text-gray-400">Ideal para</p>
                        <p class="mt-1 text-xs font-semibold text-gray-700 leading-relaxed">Consultorios y EPS pequeñas que quieren probar antes de decidir.</p>
                    </div>
                    <div class="plan-summary">
                        <p class="text-xs font-extrabold text-gray-700 mb-3">Incluye:</p>
                        <ul class="plan-list">
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>Hasta 15 pacientes activos</span></li>
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>3 usuarios y 1 sede</span></li>
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>Agenda de citas y ficha clínica básica</span></li>
                        </ul>
                    </div>
                    <button type="button" class="plan-more">
                        Ver más
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                </article>

                <!-- Plan 1 año -->
                <article id="plan-un-anio" class="plan-card reveal">
                    <span class="plan-icon plan-icon--blue">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8ZM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                    </span>
                    <h3 class="plan-title">1 año</h3>
                    <p class="mt-1 text-[.7rem] font-bold uppercase tracking-wider" style="color:var(--teal);">Plan estándar</p>
                    <p class="plan-description mt-3">Todo lo esencial para gestionar citas, pacientes e historia clínica completa durante 12 meses.</p>
                    <div class="mt-5">
                        <div class="plan-price"><span class="plan-price-currency">$</span><span>1.200.000</span></div>
                        <p class="plan-period mt-2">Pago único · 12 meses</p>
                    </div>
                    <button type="button" class="plan-cta plan-cta--outline">
                        Elegir 1 año
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                    <div class="plan-ideal mb-3">
                        <p class="text-[.68rem] text-gray-400">Ideal para</p>
                        <p class="mt-1 text-xs font-semibold text-gray-700 leading-relaxed">Consultorios e IPS pequeñas con una o dos sedes.</p>
                    </div>
                    <div class="plan-summary">
                        <p class="text-xs font-extrabold text-gray-700 mb-3">Incluye:</p>
                        <ul class="plan-list">
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>Pacientes sin límite</span></li>
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>5 usuarios y 2 sedes</span></li>
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>Recordatorios y fórmula al correo del paciente</span></li>
                        </ul>
                    </div>
                    <button type="button" class="plan-more">
                        Ver más
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                </article>

                <!-- Plan 2 años (recomendado) -->
                <article id="plan-dos-anios" class="plan-card plan-card--featured reveal">
                    <span class="plan-badge plan-badge--featured">Recomendado</span>
                    <span class="plan-icon plan-icon--purple">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5M4 19h16M8 16v-4M12 16V8M16 16v-7M20 16v-3"/></svg>
                    </span>
                    <h3 class="plan-title">2 años</h3>
                    <p class="mt-1 text-[.7rem] font-bold uppercase tracking-wider" style="color:var(--teal);">Plan profesional</p>
                    <p class="plan-description mt-3">Más usuarios, más sedes, facturación, dashboard ejecutivo y API para operaciones en crecimiento.</p>
                    <div class="mt-5">
                        <div class="plan-price"><span class="plan-price-currency">$</span><span>2.100.000</span></div>
                        <p class="plan-period mt-2">Pago único · 24 meses</p>
                    </div>
                    <button type="button" class="plan-cta plan-cta--primary">
                        Elegir 2 años
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                    <div class="plan-ideal mb-3">
                        <p class="text-[.68rem] text-gray-400">Ideal para</p>
                        <p class="mt-1 text-xs font-semibold text-gray-700 leading-relaxed">IPS y clínicas con varias sedes y equipos grandes.</p>
                    </div>
                    <div class="plan-summary">
                        <p class="text-xs font-extrabold text-gray-700 mb-3">Incluye:</p>
                        <ul class="plan-list">
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>20 usuarios y 10 sedes</span></li>
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>Facturación, cartera y dashboard ejecutivo</span></li>
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>API estándar y soporte prioritario</span></li>
                        </ul>
                    </div>
                    <button type="button" class="plan-more">
                        Ver más
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                </article>

                <!-- Plan 1 año + Farmacia -->
                <article id="plan-un-anio-farmacia" class="plan-card reveal">
                    <span class="plan-icon plan-icon--orange">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m10.5 20.5 10-10a4.95 4.95 0 0 0-7-7l-10 10a4.95 4.95 0 0 0 7 7Z"/><path stroke-linecap="round" d="m8.5 8.5 7 7"/></svg>
                    </span>
                    <h3 class="plan-title">1 año + Farmacia</h3>
                    <p class="mt-1 text-[.7rem] font-bold uppercase tracking-wider" style="color:var(--teal);">Estándar con farmacia</p>
                    <p class="plan-description mt-3">El plan de 1 año con envío directo de la fórmula al farmaceuta para que el paciente no haga fila.</p>
                    <div class="mt-5">
                        <div class="plan-price"><span class="plan-price-currency">$</span><span>1.600.000</span></div>
                        <p class="plan-period mt-2">Pago único · 12 meses</p>
                    </div>
                    <button type="button" class="plan-cta plan-cta--outline">
                        Elegir 1 año + Farmacia
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                    <div class="plan-ideal mb-3">
                        <p class="text-[.68rem] text-gray-400">Ideal para</p>
                        <p class="mt-1 text-xs font-semibold text-gray-700 leading-relaxed">IPS pequeñas que trabajan con una farmacia aliada.</p>
                    </div>
                    <div class="plan-summary">
                        <p class="text-xs font-extrabold text-gray-700 mb-3">Incluye:</p>
                        <ul class="plan-list">
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>Todo lo del plan 1 año</span></li>
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>Envío directo de la fórmula a la farmacia</span></li>
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>Aviso de sede y horario de reclamo</span></li>
                        </ul>
                    </div>
                    <button type="button" class="plan-more">
                        Ver más
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                </article>

                <!-- Plan 2 años + Farmacia -->
                <article id="plan-dos-anios-farmacia" class="plan-card reveal">
                    <span class="plan-icon plan-icon--green">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V5l7-2v18M19 21V9l-7-2M8 8h.01M8 12h.01M8 16h.01M15 13h.01M15 17h.01"/></svg>
                    </span>
                    <h3 class="plan-title">2 años + Farmacia</h3>
                    <p class="mt-1 text-[.7rem] font-bold uppercase tracking-wider" style="color:var(--teal);">Profesional con farmacia</p>
                    <p class="plan-description mt-3">La solución más completa: plan de 2 años, farmacia integrada, inventario avanzado y asesor dedicado.</p>
                    <div class="mt-5">
                        <div class="plan-price"><span class="plan-price-currency">$</span><span>2.800.000</span></div>
                        <p class="plan-period mt-2">Pago único · 24 meses</p>
                    </div>
                    <button type="button" class="plan-cta plan-cta--outline">
                        Elegir 2 años + Farmacia
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                    <div class="plan-ideal mb-3">
                        <p class="text-[.68rem] text-gray-400">Ideal para</p>
                        <p class="mt-1 text-xs font-semibold text-gray-700 leading-relaxed">Clínicas y redes de IPS con farmacia propia o aliada.</p>
                    </div>
                    <div class="plan-summary">
                        <p class="text-xs font-extrabold text-gray-700 mb-3">Incluye:</p>
                        <ul class="plan-list">
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>Todo lo del plan 2 años</span></li>
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>Farmacia integrada con inventario avanzado</span></li>
                            <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg><span>Asesor dedicado</span></li>
                        </ul>
                    </div>
                    <button type="button" class="plan-more">
                        Ver más
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                </article>

            </div>

            <p class="text-center text-xs text-gray-400 mt-6">Valores de referencia en pesos colombianos. Las variantes + Farmacia tienen un valor superior por el envío directo y la gestión de medicamentos.</p>
        </div>
    </section>

    <!-- Detalle del plan gratuito -->
    <section id="detalle-plan-gratis" class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-[.9fr_1.1fr] gap-10 items-start">
                <div class="reveal">
                    <span class="inline-block rounded-full bg-teal-50 px-3 py-1 text-xs font-extrabold" style="color:var(--teal);">Prueba gratis · 1 mes</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-4 mb-4">Prueba el plan gratis durante un mes</h2>
                    <p class="text-gray-500 leading-relaxed mb-6">El plan Gratis es ideal para EPS pequeñas y consultorios que quieren digitalizar su agenda sin una inversión inicial durante el primer mes.</p>
                    <div class="rounded-2xl p-5" style="background:linear-gradient(135deg,#ecfdf5,#f0f9ff);">
                        <p class="text-sm font-extrabold text-gray-800 mb-2">¿Qué puedes resolver durante el mes gratis?</p>
                        <p class="text-xs text-gray-600 leading-relaxed">Centraliza hasta 15 pacientes, evita perder citas, mantén una agenda ordenada y conoce la plataforma antes de elegir una vigencia.</p>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="reveal rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5">
                        <div class="flex items-center gap-2 mb-4"><span class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg></span><h3 class="font-extrabold text-emerald-900">Lo que sí incluye</h3></div>
                        <ul class="space-y-3">
                            <li class="flex gap-2 text-sm text-emerald-900/80"><span class="text-emerald-600 font-bold">✓</span><span>Hasta 15 pacientes activos</span></li>
                            <li class="flex gap-2 text-sm text-emerald-900/80"><span class="text-emerald-600 font-bold">✓</span><span>3 usuarios y 1 sede</span></li>
                            <li class="flex gap-2 text-sm text-emerald-900/80"><span class="text-emerald-600 font-bold">✓</span><span>Agenda de citas</span></li>
                            <li class="flex gap-2 text-sm text-emerald-900/80"><span class="text-emerald-600 font-bold">✓</span><span>Ficha e historia clínica básica</span></li>
                            <li class="flex gap-2 text-sm text-emerald-900/80"><span class="text-emerald-600 font-bold">✓</span><span>Soporte de comunidad</span></li>
                        </ul>
                    </div>
                    <div class="reveal rounded-2xl border border-gray-200 bg-gray-50 p-5">
                        <div class="flex items-center gap-2 mb-4"><span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-500"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 12h12"/></svg></span><h3 class="font-extrabold text-gray-800">Lo que no incluye</h3></div>
                        <ul class="space-y-3">
                            <li class="flex gap-2 text-sm text-gray-500"><span class="text-gray-400 font-bold">—</span><span>Recordatorios automáticos</span></li>
                            <li class="flex gap-2 text-sm text-gray-500"><span class="text-gray-400 font-bold">—</span><span>Fórmula al correo del paciente</span></li>
                            <li class="flex gap-2 text-sm text-gray-500"><span class="text-gray-400 font-bold">—</span><span>Envío directo a la farmacia</span></li>
                            <li class="flex gap-2 text-sm text-gray-500"><span class="text-gray-400 font-bold">—</span><span>Facturación y cartera</span></li>
                            <li class="flex gap-2 text-sm text-gray-500"><span class="text-gray-400 font-bold">—</span><span>Dashboard ejecutivo</span></li>
                            <li class="flex gap-2 text-sm text-gray-500"><span class="text-gray-400 font-bold">—</span><span>API e integraciones</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comparativa -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="reveal text-center max-w-3xl mx-auto mb-10">
                <span class="inline-block text-xs font-extrabold uppercase tracking-[.18em]" style="color:var(--teal);">Comparativa</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-3 mb-4">Compara los beneficios de cada plan</h2>
                <p class="text-gray-500 leading-relaxed">Mira de un vistazo qué incluye cada opción y elige la que se ajusta a tu operación.</p>
            </div>
            <div class="reveal rounded-2xl border border-gray-200 bg-white shadow-sm overflow-x-auto">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Funcionalidad</th>
                            <th>Gratis</th>
                            <th>1 año</th>
                            <th>2 años</th>
                            <th>1 año + Farmacia</th>
                            <th>2 años + Farmacia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="font-semibold text-gray-700">Pacientes activos</td><td class="text-gray-600">15</td><td class="text-gray-600">Sin límite</td><td class="text-gray-600">Sin límite</td><td class="text-gray-600">Sin límite</td><td class="text-gray-600">Sin límite</td></tr>
                        <tr><td class="font-semibold text-gray-700">Vigencia</td><td class="text-gray-600">1 mes</td><td class="text-gray-600">12 meses</td><td class="text-gray-600">24 meses</td><td class="text-gray-600">12 meses</td><td class="text-gray-600">24 meses</td></tr>
                        <tr><td class="font-semibold text-gray-700">Usuarios incluidos</td><td class="text-gray-600">3</td><td class="text-gray-600">5</td><td class="text-gray-600">20</td><td class="text-gray-600">5</td><td class="text-gray-600">20</td></tr>
                        <tr><td class="font-semibold text-gray-700">Sedes</td><td class="text-gray-600">1</td><td class="text-gray-600">2</td><td class="text-gray-600">10</td><td class="text-gray-600">2</td><td class="text-gray-600">10</td></tr>
                        <tr><td class="font-semibold text-gray-700">Agenda de citas</td><td class="text-gray-600">Incluida</td><td class="text-gray-600">Incluida</td><td class="text-gray-600">Incluida</td><td class="text-gray-600">Incluida</td><td class="text-gray-600">Incluida</td></tr>
                        <tr><td class="font-semibold text-gray-700">Ficha e historia clínica</td><td class="text-gray-600">Básica</td><td class="text-gray-600">Completa</td><td class="text-gray-600">Completa</td><td class="text-gray-600">Completa</td><td class="text-gray-600">Completa</td></tr>
                        <tr><td class="font-semibold text-gray-700">Recordatorios automáticos</td><td class="table-dash">—</td><td class="table-check">✓</td><td class="table-check">✓</td><td class="table-check">✓</td><td class="table-check">✓</td></tr>
                        <tr><td class="font-semibold text-gray-700">Fórmula al correo del paciente</td><td class="table-dash">—</td><td class="table-check">✓</td><td class="table-check">✓</td><td class="table-check">✓</td><td class="table-check">✓</td></tr>
                        <tr><td class="font-semibold text-gray-700">Envío directo a la farmacia</td><td class="table-dash">—</td><td class="table-dash">—</td><td class="table-dash">—</td><td class="table-check">✓</td><td class="table-check">✓</td></tr>
                        <tr><td class="font-semibold text-gray-700">Aviso de sede y reclamo</td><td class="table-dash">—</td><td class="table-dash">—</td><td class="table-dash">—</td><td class="table-check">✓</td><td class="table-check">✓</td></tr>
                        <tr><td class="font-semibold text-gray-700">Prescripciones e inventario</td><td class="table-dash">—</td><td class="text-gray-600">Básico</td><td class="text-gray-600">Avanzado</td><td class="text-gray-600">Básico + farmacia</td><td class="text-gray-600">Avanzado + farmacia</td></tr>
                        <tr><td class="font-semibold text-gray-700">Facturación y cartera</td><td class="table-dash">—</td><td class="table-dash">—</td><td class="table-check">✓</td><td class="table-dash">—</td><td class="table-check">✓</td></tr>
                        <tr><td class="font-semibold text-gray-700">Roles y auditoría</td><td class="table-dash">—</td><td class="text-gray-600">Básica</td><td class="text-gray-600">Avanzada</td><td class="text-gray-600">Básica</td><td class="text-gray-600">Avanzada</td></tr>
                        <tr><td class="font-semibold text-gray-700">Dashboard ejecutivo</td><td class="table-dash">—</td><td class="table-dash">—</td><td class="table-check">✓</td><td class="table-dash">—</td><td class="table-check">✓</td></tr>
                        <tr><td class="font-semibold text-gray-700">API e integraciones</td><td class="table-dash">—</td><td class="table-dash">—</td><td class="text-gray-600">API estándar</td><td class="table-dash">—</td><td class="text-gray-600">API estándar</td></tr>
                        <tr><td class="font-semibold text-gray-700">Soporte</td><td class="text-gray-600">Comunidad</td><td class="text-gray-600">Correo</td><td class="text-gray-600">Prioritario</td><td class="text-gray-600">Correo</td><td class="text-gray-600">Asesor dedicado</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Cómo funciona -->
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="reveal text-center max-w-3xl mx-auto mb-12">
                <span class="inline-block text-xs font-extrabold uppercase tracking-[.18em]" style="color:var(--teal);">Así de sencillo</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-3 mb-4">Pon tu software en marcha en tres pasos</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="reveal rounded-2xl border border-gray-200 p-6 text-center">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-teal-50 text-lg font-extrabold mb-4" style="color:var(--teal);">01</span>
                    <h3 class="font-extrabold text-gray-900 mb-2">Elige tu plan</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Prueba el plan gratis durante un mes o habla con un asesor para revisar el tamaño de tu operación.</p>
                </div>
                <div class="reveal rounded-2xl border border-gray-200 p-6 text-center">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-50 text-lg font-extrabold mb-4 text-blue-700">02</span>
                    <h3 class="font-extrabold text-gray-900 mb-2">Configura tu espacio</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Carga pacientes, servicios, sedes y usuarios. Nuestro equipo te acompaña en el proceso.</p>
                </div>
                <div class="reveal rounded-2xl border border-gray-200 p-6 text-center">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-purple-50 text-lg font-extrabold mb-4 text-purple-700">03</span>
                    <h3 class="font-extrabold text-gray-900 mb-2">Trabaja y crece</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Consulta indicadores en tiempo real y activa nuevas herramientas cuando las necesites.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-4xl mx-auto px-6">
            <div class="reveal text-center mb-10">
                <span class="inline-block text-xs font-extrabold uppercase tracking-[.18em]" style="color:var(--teal);">Preguntas frecuentes</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-3 mb-4">Resolvemos tus dudas</h2>
            </div>
            <div class="space-y-3">
                <div class="faq-item reveal abierto">
                    <button type="button" class="faq-question" aria-expanded="true"><span>¿Puedo comenzar con el plan Gratis?</span><span class="faq-icon" aria-hidden="true">+</span></button>
                    <div class="faq-answer max-h-40"><p>Sí. Puedes crear tu cuenta sin pagar durante 1 mes y registrar hasta 15 pacientes activos.</p></div>
                </div>
                <div class="faq-item reveal">
                    <button type="button" class="faq-question" aria-expanded="false"><span>¿Qué pasa al terminar el mes gratis?</span><span class="faq-icon" aria-hidden="true">+</span></button>
                    <div class="faq-answer"><p>Puedes elegir el plan de 1 año, 2 años o la variante con entrega a farmacia. El plan Gratis no se renueva automáticamente.</p></div>
                </div>
                <div class="faq-item reveal">
                    <button type="button" class="faq-question" aria-expanded="false"><span>¿Cómo se cuentan los pacientes?</span><span class="faq-icon" aria-hidden="true">+</span></button>
                    <div class="faq-answer"><p>El plan Gratis permite hasta 15 pacientes activos. Los planes de 1 y 2 años no tienen límite de pacientes; su restricción es la vigencia contratada.</p></div>
                </div>
                <div class="faq-item reveal">
                    <button type="button" class="faq-question" aria-expanded="false"><span>¿Cómo se entrega la fórmula?</span><span class="faq-icon" aria-hidden="true">+</span></button>
                    <div class="faq-answer"><p>En los planes de 1 y 2 años, el doctor envía la fórmula al correo del paciente y este debe reclamarla en la sede de farmacia. En los planes de 1 año + Farmacia y 2 años + Farmacia, la fórmula se envía directamente al farmaceuta y el paciente recibe un correo con la sede y el horario para reclamarla, sin hacer fila.</p></div>
                </div>
                <div class="faq-item reveal">
                    <button type="button" class="faq-question" aria-expanded="false"><span>¿Puedo contratar por 1 o 2 años?</span><span class="faq-icon" aria-hidden="true">+</span></button>
                    <div class="faq-answer"><p>Sí. Puedes elegir el plan estándar o la variante con entrega a farmacia, durante 1 o 2 años. El alcance, los módulos y el precio final se confirman con un asesor comercial.</p></div>
                </div>
                <div class="faq-item reveal">
                    <button type="button" class="faq-question" aria-expanded="false"><span>¿Qué incluye la puesta en marcha?</span><span class="faq-icon" aria-hidden="true">+</span></button>
                    <div class="faq-answer"><p>Te ayudamos a definir la configuración inicial de tu cuenta, servicios, sedes y permisos. El nivel de acompañamiento depende del plan contratado y se confirma con el asesor comercial.</p></div>
                </div>
                <div class="faq-item reveal">
                    <button type="button" class="faq-question" aria-expanded="false"><span>¿Puedo cambiar de plan más adelante?</span><span class="faq-icon" aria-hidden="true">+</span></button>
                    <div class="faq-answer"><p>Sí. Puedes crecer o ajustar tu vigencia según cambien tus pacientes, usuarios, sedes y necesidades. El valor final se calcula según los módulos activos.</p></div>
                </div>
                <div class="faq-item reveal">
                    <button type="button" class="faq-question" aria-expanded="false"><span>¿Los datos quedan almacenados en la nube?</span><span class="faq-icon" aria-hidden="true">+</span></button>
                    <div class="faq-answer"><p>Sí. PrismaSalud Software está diseñado para trabajar en la nube, con acceso organizado por roles y medidas de seguridad para proteger la información.</p></div>
                </div>
                <div class="faq-item reveal">
                    <button type="button" class="faq-question" aria-expanded="false"><span>¿Puedo conectar mis sistemas actuales?</span><span class="faq-icon" aria-hidden="true">+</span></button>
                    <div class="faq-answer"><p>Los planes de 2 años incluyen una API estándar y las variantes + Farmacia permiten conectar el flujo de prescripciones con la farmacia. Valida con nuestro equipo qué información necesitas conectar.</p></div>
                </div>
                <div class="faq-item reveal">
                    <button type="button" class="faq-question" aria-expanded="false"><span>¿Cómo solicito una demostración?</span><span class="faq-icon" aria-hidden="true">+</span></button>
                    <div class="faq-answer"><p>Escribe desde la página de Contacto, elige el plan o la vigencia que te interesa y un asesor te contactará para mostrarte el producto y resolver tus preguntas.</p></div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA final -->
    <section class="bg-white py-20">
        <div class="max-w-5xl mx-auto px-6">
            <div class="reveal gradiente-salud text-white rounded-3xl p-8 md:p-12 text-center relative overflow-hidden">
                <span class="hero-orbit hero-orbit--one opacity-50"></span>
                <div class="relative z-10">
                    <p class="text-xs font-extrabold uppercase tracking-[.18em] text-teal-100">Tu próximo paso</p>
                    <h2 class="text-3xl md:text-4xl font-extrabold mt-3 mb-4">Haz que tu equipo trabaje mejor desde mañana</h2>
                    <p class="text-white/80 max-w-2xl mx-auto mb-7">Conoce PrismaSalud Software y descubre cómo puedes ordenar tu operación, mejorar tus servicios y liberar tiempo para tus pacientes.</p>
                    <div class="flex flex-col sm:flex-row justify-center gap-3">
                        <a href="{{ route('login') }}" class="px-5 py-3 text-sm font-extrabold rounded-lg bg-white text-teal-800 hover:bg-teal-50 transition">Probar gratis</a>
                        <a href="https://wa.me/573017880161?text=Hola%2C%20quiero%20agendar%20una%20cita" target="_blank" rel="noopener" class="px-5 py-3 text-sm font-extrabold rounded-lg border border-white/50 text-white hover:bg-white/10 transition">Hablar con un asesor</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white/80" style="background-color:#1A5C3A;">
        <div class="max-w-7xl mx-auto px-6 py-14 grid grid-cols-2 lg:grid-cols-5 gap-x-6 gap-y-10 text-center md:text-left">
            <div class="col-span-2 lg:col-span-1 flex flex-col items-center md:items-start">
                <p class="text-white font-bold text-lg mb-2">Prisma<span class="text-teal-300">Salud</span> IPS</p>
                <p class="text-sm">Software y soluciones para una operación de salud más ordenada, eficiente y cercana.</p>
            </div>
            <div>
                <p class="text-white font-semibold mb-3">Navegación</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('servicios.index') }}" class="footer-link">Servicios</a></li>
                    <li><a href="{{ route('blog.index') }}" class="footer-link">Blog</a></li>
                    <li><a href="{{ route('contacto.index') }}" class="footer-link">Contacto</a></li>
                    <li><a href="{{ route('planes.index') }}" class="footer-link">Planes</a></li>
                </ul>
            </div>
            <div>
                <p class="text-white font-semibold mb-3">Planes</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="#plan-gratis" class="footer-link">Plan Gratis</a></li>
                    <li><a href="#plan-un-anio" class="footer-link">1 año</a></li>
                    <li><a href="#plan-dos-anios" class="footer-link">2 años</a></li>
                    <li><a href="#plan-un-anio-farmacia" class="footer-link">1 año + Farmacia</a></li>
                    <li><a href="#plan-dos-anios-farmacia" class="footer-link">2 años + Farmacia</a></li>
                </ul>
            </div>
            <div>
                <p class="text-white font-semibold mb-3">Marco legal</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('terminos.index') }}" class="footer-link">Términos y condiciones</a></li>
                    <li><a href="{{ route('privacidad.index') }}" class="footer-link">Política de privacidad</a></li>
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
                <p>© {{ date('Y') }} PrismaSalud IPS · Todos los derechos reservados</p>
                <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2">
                    <a href="{{ route('terminos.index') }}" class="footer-link">Términos y Condiciones</a>
                    <span class="opacity-30">•</span>
                    <a href="{{ route('privacidad.index') }}" class="footer-link">Política de Privacidad</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const siteHeader = document.getElementById('site-header');
        function actualizarHeaderPlanes() {
            if (!siteHeader) return;
            siteHeader.classList.toggle('scrolled', window.scrollY > 40);
        }
        window.addEventListener('scroll', actualizarHeaderPlanes);
        document.addEventListener('DOMContentLoaded', actualizarHeaderPlanes);

        const botonHamburguesa = document.getElementById('boton-hamburguesa');
        const botonCerrarDrawer = document.getElementById('boton-cerrar-drawer');
        const drawerMovil = document.getElementById('drawer-movil');
        const overlayMovil = document.getElementById('overlay-movil');
        function abrirMenuPlanes() { drawerMovil.classList.add('abierto'); overlayMovil.classList.add('abierto'); }
        function cerrarMenuPlanes() { drawerMovil.classList.remove('abierto'); overlayMovil.classList.remove('abierto'); }
        botonHamburguesa.addEventListener('click', abrirMenuPlanes);
        botonCerrarDrawer.addEventListener('click', cerrarMenuPlanes);
        overlayMovil.addEventListener('click', cerrarMenuPlanes);
        drawerMovil.querySelectorAll('a').forEach(function (enlace) { enlace.addEventListener('click', cerrarMenuPlanes); });

        document.querySelectorAll('.faq-question').forEach(function (boton) {
            boton.addEventListener('click', function () {
                const item = boton.closest('.faq-item');
                const respuesta = item.querySelector('.faq-answer');
                const abierto = item.classList.toggle('abierto');
                boton.setAttribute('aria-expanded', abierto ? 'true' : 'false');
                respuesta.style.maxHeight = abierto ? respuesta.scrollHeight + 'px' : '0px';
            });
        });

        const observadorPlanes = new IntersectionObserver(function (entradas) {
            entradas.forEach(function (entrada) {
                if (entrada.isIntersecting) {
                    entrada.target.classList.add('visible');
                    observadorPlanes.unobserve(entrada.target);
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('.reveal').forEach(function (elemento) { observadorPlanes.observe(elemento); });
    </script>

</body>
</html>