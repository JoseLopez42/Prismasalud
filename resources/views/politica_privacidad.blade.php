<!-- politica_privacidad.blade.php - Marco legal: Politica de Privacidad / Habeas Data -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad - PrismaSalud IPS</title>

    <meta name="description" content="Política de Tratamiento de Datos Personales de PrismaSalud IPS, conforme a la Ley 1581 de 2012 y demás normas del régimen de protección de datos en Colombia.">

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
        .drawer-movil .drawer-link:hover { background-color: #f0fdfa; color: var(--teal); }
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
        .reveal.visible { opacity: 1; transform: translateY(0); }

        .legal-toc { position: sticky; top: 96px; align-self: start; }
        .legal-seccion h2 {
            display: flex;
            align-items: baseline;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--teal);
            margin-bottom: 0.75rem;
        }
        .legal-seccion h2 span { color: #94a3b8; font-size: 0.9rem; font-weight: 600; }
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
                    <p class="font-bold text-white text-sm">Prisma<span class="text-teal-300">Salud</span> IPS</p>
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
                <a href="{{ route('privacidad.index') }}" class="drawer-link" style="color:var(--teal);">
                    <span class="drawer-icono bg-teal-50 text-teal-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11l2-2a3.5 3.5 0 015 5l-3 3a3.5 3.5 0 01-5 0M12 13l-2 2a3.5 3.5 0 01-5 0l-3-3a3.5 3.5 0 015-5l3 2"/></svg>
                    </span> Política de Privacidad
                </a>
            </nav>
            <div class="p-5 border-t border-gray-100 bg-gray-50">
                <a href="{{ url('/login') }}" class="px-4 py-2.5 text-sm font-semibold border rounded-md text-center bg-white block"
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
            <span class="inline-block bg-white/10 text-white text-xs px-3 py-1 rounded-full mb-4 uppercase tracking-wider">
                Habeas Data · Versión 1.0
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                Política de <span class="text-teal-300">Privacidad</span>
            </h1>
            <p class="text-white/80 max-w-2xl mx-auto">
                Política de Tratamiento de Datos Personales de PrismaSalud IPS, de conformidad
                con la Ley 1581 de 2012, el Decreto 1377 de 2013 y la jurisprudencia constitucional.
            </p>
        </div>
    </section>

    <!-- Contenido -->
    <div class="max-w-7xl mx-auto px-6 py-16 grid lg:grid-cols-[260px_1fr] gap-10">

        <!-- Indice -->
        <aside class="legal-toc hidden lg:block">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Contenido</p>
            <nav class="space-y-1 text-sm">
                <a href="#intro" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">1. Introducción</a>
                <a href="#responsable" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">2. Responsable del tratamiento</a>
                <a href="#datos" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">3. Datos tratados</a>
                <a href="#finalidades" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">4. Finalidades</a>
                <a href="#autorizacion" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">5. Autorización</a>
                <a href="#derechos" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">6. Derechos del titular</a>
                <a href="#consulta-reclamo" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">7. Consultas y reclamos</a>
                <a href="#seguridad" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">8. Seguridad y confidencialidad</a>
                <a href="#historia-clinica" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">9. Historia clínica</a>
                <a href="#autoridad" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">10. Autoridad de control</a>
            </nav>
            <div class="mt-6 rounded-xl border border-gray-200 p-4 bg-gray-50">
                <p class="text-xs text-gray-500 mb-2">Última actualización: septiembre de {{ date('Y') }}</p>
                <a href="{{ route('terminos.index') }}" class="text-xs font-semibold text-teal-700 hover:underline">Ver Términos y Condiciones →</a>
            </div>
        </aside>

        <!-- Articulos -->
        <article class="space-y-8">

            <section id="intro" class="legal-seccion">
                <h2><span>01</span> Introducción</h2>
                <p class="text-gray-600 leading-relaxed">
                    De conformidad con la <strong>Ley 1581 de 2012</strong>, el <strong>Decreto 1377 de
                    2013</strong> y la <strong>Sentencia C-748 de 2011</strong>, PrismaSalud IPS — como
                    institución prestadora de servicios de salud — es responsable del tratamiento de los
                    datos personales que recopila de sus usuarios, pacientes, familiares y demás personas
                    que se relacionan con la institución. Esta política desarrolla los principios de
                    legalidad, finalidad, libertad, veracidad, transparencia, acceso y circulación
                    restringida, seguridad y confidencialidad.
                </p>
            </section>

            <section id="responsable" class="legal-seccion">
                <h2><span>02</span> Responsable del tratamiento</h2>
                <p class="text-gray-600 leading-relaxed">
                    <strong>Razón social:</strong> PrismaSalud IPS.<br>
                    <strong>NIT:</strong> 900.000.000-0.<br>
                    <strong>Domicilio:</strong> Calle 42 #5-28, Ibagué, Tolima.<br>
                    <strong>Canales:</strong> (601) 123-4667 · info@prismasaludips.com
                </p>
                <p class="text-gray-600 leading-relaxed mt-3">
                    El responsable del tratamiento garantizará que la información sea manejada conforme a
                    la ley, con deberes de secreto y confidencialidad, incluso después de finalizada la
                    relación contractual o asistencial.
                </p>
            </section>

            <section id="datos" class="legal-seccion">
                <h2><span>03</span> Datos personales tratados</h2>
                <ul class="list-disc list-inside space-y-2 text-gray-600 leading-relaxed">
                    <li><strong>Datos de identificación:</strong> nombre, documento de identidad, fecha de nacimiento, edad, sexo.</li>
                    <li><strong>Datos de contacto:</strong> teléfono, correo electrónico, dirección de residencia.</li>
                    <li><strong>Datos de salud (sensibles):</strong> estado de salud, antecedentes clínicos, historia clínica, resultados de laboratorio e imágenes diagnósticas. Estos datos reciben protección especial y solo se tratarán con autorización expresa del titular o por mandato legal.</li>
                    <li><strong>Datos académicos y laborales:</strong> para efectos de vinculación de talento humano.</li>
                    <li><strong>Datos de afiliación:</strong> entidad aseguradora (EPS) y régimen de afiliación.</li>
                </ul>
            </section>

            <section id="finalidades" class="legal-seccion">
                <h2><span>04</span> Finalidades del tratamiento</h2>
                <ul class="list-disc list-inside space-y-2 text-gray-600 leading-relaxed">
                    <li>Prestar, coordinar y controlar los servicios de salud ofertados por la institución.</li>
                    <li>Gestionar citas, autorizaciones, facturación y trámites frente a las EPS.</li>
                    <li>Elaborar y custodiar la historia clínica conforme a la Resolución 1995 de 1999.</li>
                    <li>Diseñar programas de promoción y prevención (PYP) y salud pública.</li>
                    <li>Enviar comunicaciones sobre servicios, recordatorios de citas y campañas de salud.</li>
                    <li>Cumplir obligaciones legales, contractuales, tributarias y administrativas.</li>
                    <li>Atender y tramitar PQRS y dar respuesta al derecho de petición (Ley 1755 de 2015).</li>
                </ul>
            </section>

            <section id="autorizacion" class="legal-seccion">
                <h2><span>05</span> Autorización</h2>
                <p class="text-gray-600 leading-relaxed">
                    El tratamiento de datos personales requiere la <strong>autorización previa, expresa e
                    informada</strong> del titular. Para los <strong>datos sensibles</strong> — incluyendo los de
                    salud — la autorización debe ser particular, expresa y se advertirá expresamente su
                    carácter sensible. La autorización podrá revocarse o supender el tratamiento en cualquier
                    momento, salvo las limitaciones impuestas por la ley, manteniéndose la prestación de los
                    servicios de salud que resulten indispensables.
                </p>
            </section>

            <section id="derechos" class="legal-seccion">
                <h2><span>06</span> Derechos del titular</h2>
                <ul class="list-disc list-inside space-y-2 text-gray-600 leading-relaxed">
                    <li>Conocer, actualizar y rectificar sus datos personales.</li>
                    <li>Solicitar prueba de la autorización otorgada.</li>
                    <li>Ser informado sobre el uso que se ha dado a sus datos.</li>
                    <li>Presentar consultas y reclamos ante el responsable.</li>
                    <li>Revocar la autorización o solicitar la supresión de los datos cuando no exista deber legal o contractual de conservarlos.</li>
                    <li>Acceder gratuitamente a sus datos personales.</li>
                </ul>
                <p class="text-gray-600 leading-relaxed mt-3">
                    Adicionalmente, el titular del dato de salud ejerce el derecho de acceso, actualización,
                    rectificación y solicitud de copia de su historia clínica conforme a la Resolución 1995
                    de 1999, en la forma y con las restricciones allí previstas.
                </p>
            </section>

            <section id="consulta-reclamo" class="legal-seccion">
                <h2><span>07</span> Consultas y reclamos</h2>
                <p class="text-gray-600 leading-relaxed mb-3">
                    El titular o sus causahabientes podrán presentar consultas y reclamos sobre sus datos
                    personales a través de los siguientes canales:
                </p>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-1 text-sm text-gray-600">
                    <p><strong>Correo:</strong> info@prismasaludips.com</p>
                    <p><strong>Teléfono:</strong> (601) 123-4667</p>
                    <p><strong>Dirección:</strong> Calle 42 #5-28, Ibagué, Tolima.</p>
                </div>
                <p class="text-gray-600 leading-relaxed mt-4">
                    La consulta será respondida en un término máximo de <strong>diez (10) días hábiles</strong>;
                    si no es posible, se informará la fecha oportuna. El <strong>reclamo</strong> completo será
                    respondido en un término máximo de <strong>quince (15) días hábiles</strong>, prorrogable por
                    otros quince cuando las circunstancias lo exijan, dando aviso al interesado.
                </p>
            </section>

            <section id="seguridad" class="legal-seccion">
                <h2><span>08</span> Seguridad y confidencialidad</h2>
                <p class="text-gray-600 leading-relaxed">
                    PrismaSalud IPS adopta medidas técnicas, humanas y administrativas razonables para
                    proteger los datos personales contra pérdida, adulteración, uso indebido o acceso no
                    autorizado. Todos los colaboradores se encuentran obligados a guardar reserva y
                    confidencialidad sobre la información de los usuarios, obligación que persiste incluso
                    después de terminar su relación con la institución.
                </p>
            </section>

            <section id="historia-clinica" class="legal-seccion">
                <h2><span>09</span> Reserva de la historia clínica</h2>
                <p class="text-gray-600 leading-relaxed">
                    La historia clínica es un documento privado, sometido a reserva, que solo puede ser
                    conocido por el paciente, su equipo de salud, y las entidades, autoridades o terceros
                    autorizados por ley (Resolución 1995 de 1999, Ley 23 de 1981). Su custodia, acceso y
                    disposición final se rigen por la normativa vigente aplicable a las historias clínicas.
                </p>
            </section>

            <section id="autoridad" class="legal-seccion">
                <h2><span>10</span> Autoridad de control</h2>
                <p class="text-gray-600 leading-relaxed">
                    Ante el incumplimiento de la Política de Tratamiento de Datos, el titular podrá acudir a la
                    <strong>Superintendencia de Industria y Comercio (SIC)</strong>, autoridad nacional de
                    protección de datos personales, o a la <strong>Superintendencia Nacional de Salud</strong>
                    cuando el asunto verse sobre la prestación de servicios de salud.
                </p>
            </section>

        </article>
    </div>

    <!-- Footer -->
    <footer class="text-white/80" style="background-color: #1A5C3A;">
        <div class="max-w-7xl mx-auto px-6 py-14 grid grid-cols-2 lg:grid-cols-5 gap-x-6 gap-y-10 md:gap-10 text-center md:text-left break-words">
            <div class="col-span-2 lg:col-span-1 flex flex-col items-center md:items-start">
                <p class="text-white font-bold text-lg mb-2">Prisma<span class="text-teal-300">Salud</span> IPS</p>
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
        const header = document.getElementById('site-header');
        const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 10);
        window.addEventListener('scroll', onScroll);
        onScroll();

        const botonHamburguesa = document.getElementById('boton-hamburguesa');
        const botonCerrarDrawer = document.getElementById('boton-cerrar-drawer');
        const overlayMovil = document.getElementById('overlay-movil');
        const drawerMovil = document.getElementById('drawer-movil');
        const abrirDrawer = () => { drawerMovil.classList.add('abierto'); overlayMovil.classList.add('abierto'); };
        const cerrarDrawer = () => { drawerMovil.classList.remove('abierto'); overlayMovil.classList.remove('abierto'); };
        botonHamburguesa.addEventListener('click', abrirDrawer);
        botonCerrarDrawer.addEventListener('click', cerrarDrawer);
        overlayMovil.addEventListener('click', cerrarDrawer);

        const observadorReveal = new IntersectionObserver((entradas) => {
            entradas.forEach((e) => { if (e.isIntersecting) { e.target.classList.add('visible'); observadorReveal.unobserve(e.target); } });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach((el) => observadorReveal.observe(el));
    </script>

    @include('partials.cookies')

</body>
</html>