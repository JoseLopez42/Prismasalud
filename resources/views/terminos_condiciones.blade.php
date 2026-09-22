<!-- terminos_condiciones.blade.php - Marco legal: Terminos y Condiciones -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones - PrismaSalud IPS</title>

    <meta name="description" content="Términos y Condiciones de uso del portal de PrismaSalud IPS, conforme a la normativa colombiana del sector salud.">

    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }

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
        .site-header .nav-link { color: #4b5563; position: relative; }
        .site-header .nav-link:hover { color: #0d9488; }
        .nav-link::after {
            content: '';
            position: absolute;
            left: 0; bottom: -4px;
            width: 0;
            height: 2px;
            background-color: #0d9488;
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
        .drawer-movil .drawer-link:hover { background-color: #f0fdfa; color: #0f766e; }
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
        .btn-cta:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.18); }

        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .6s ease, transform .6s ease;
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        .legal-toc {
            position: sticky;
            top: 96px;
            align-self: start;
        }
        .legal-seccion h2 {
            display: flex;
            align-items: baseline;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f766e;
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

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium ml-10">
                <a href="{{ url('/') }}" class="nav-link">Inicio</a>
                <a href="{{ route('nosotros') }}" class="nav-link">Nosotros</a>
                <a href="{{ url('/#servicios') }}" class="nav-link">Servicios</a>
                <a href="{{ url('/#contacto') }}" class="nav-link">Contacto</a>
            </nav>

            <div class="hidden md:flex items-center gap-3 ml-auto">
                <a href="{{ url('/login') }}" class="px-5 py-2 text-sm font-semibold text-white rounded-md shadow-sm hover:opacity-90 transition"
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
                <a href="{{ url('/#servicios') }}" class="drawer-link">
                    <span class="drawer-icono bg-green-50 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6.5-4.35-9.3-8.4C.8 9.4 1.9 5.6 5.3 4.6c2-.6 4 .1 5.2 1.7l1.5 2 1.5-2c1.2-1.6 3.2-2.3 5.2-1.7 3.4 1 4.5 4.8 2.6 8-2.8 4.05-9.3 8.4-9.3 8.4z"/></svg>
                    </span> Servicios
                </a>
                <a href="{{ url('/#contacto') }}" class="drawer-link">
                    <span class="drawer-icono bg-amber-50 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    </span> Contacto
                </a>
                <a href="{{ route('terminos.index') }}" class="drawer-link" style="color:#0f766e;">
                    <span class="drawer-icono bg-teal-50 text-teal-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6a2 2 0 011.4.6l3.4 3.4a2 2 0 01.6 1.4V19a2 2 0 01-2 2z"/></svg>
                    </span> Términos y Condiciones
                </a>
            </nav>
            <div class="p-5 border-t border-gray-100 bg-gray-50">
                <a href="{{ url('/login') }}" class="px-4 py-2.5 text-sm font-semibold border rounded-md text-center bg-white block"
                   style="color:#0d9488; border-color:#0d9488;">
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
                Marco legal · Versión 1.0
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                Términos y <span class="text-teal-300">Condiciones</span>
            </h1>
            <p class="text-white/80 max-w-2xl mx-auto">
                Normas de uso de la plataforma y de los servicios de PrismaSalud IPS,
                en concordancia con el ordenamiento jurídico colombiano en salud.
            </p>
        </div>
    </section>

    <!-- Contenido -->
    <div class="max-w-7xl mx-auto px-6 py-16 grid lg:grid-cols-[260px_1fr] gap-10">

        <!-- Indice -->
        <aside class="legal-toc hidden lg:block">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Contenido</p>
            <nav class="space-y-1 text-sm">
                <a href="#objeto" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">1. Objeto</a>
                <a href="#definiciones" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">2. Definiciones</a>
                <a href="#aceptacion" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">3. Aceptación</a>
                <a href="#uso-portal" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">4. Uso del portal</a>
                <a href="#derechos-paciente" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">5. Derechos del paciente</a>
                <a href="#deberes-paciente" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">6. Deberes del paciente</a>
                <a href="#historia-clinica" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">7. Historia clínica</a>
                <a href="#habeas-data" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">8. Datos personales</a>
                <a href="#responsabilidad" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">9. Responsabilidad</a>
                <a href="#pqrs" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">10. PQRS</a>
                <a href="#modificaciones" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">11. Modificaciones</a>
                <a href="#ley-aplicable" class="block px-3 py-1.5 rounded-md hover:bg-teal-50 text-gray-600 hover:text-teal-700">12. Ley aplicable</a>
            </nav>
            <div class="mt-6 rounded-xl border border-gray-200 p-4 bg-gray-50">
                <p class="text-xs text-gray-500 mb-2">Última actualización: septiembre de {{ date('Y') }}</p>
                <a href="{{ route('privacidad.index') }}" class="text-xs font-semibold text-teal-700 hover:underline">Ver Política de Privacidad →</a>
            </div>
        </aside>

        <!-- Articulos -->
        <article class="prose-space-y-6 space-y-8">

            <section id="objeto" class="legal-seccion">
                <h2><span>01</span> Objeto</h2>
                <p class="text-gray-600 leading-relaxed">
                    Los presentes Términos y Condiciones regulan el acceso y uso del portal web y de la
                    plataforma institucional de <strong>PrismaSalud IPS</strong>, Institución Prestadora de
                    Servicios de Salud con domicilio en Ibagué, Tolima, así como las condiciones generales
                    de prestación de los servicios de salud ofrecidos, de conformidad con la
                    <strong>Ley 100 de 1993</strong>, la <strong>Ley 1751 de 2015</strong> (Ley Estatutaria de
                    Salud) y demás normas que las modifiquen o sustituyan.
                </p>
            </section>

            <section id="definiciones" class="legal-seccion">
                <h2><span>02</span> Definiciones</h2>
                <ul class="space-y-3 text-gray-600 leading-relaxed">
                    <li><strong>IPS:</strong> Institución Prestadora de Servicios de Salud debidamente habilitada.</li>
                    <li><strong>Paciente o Usuario:</strong> persona natural que solicita o recibe servicios de salud.</li>
                    <li><strong>Historia clínica:</strong> documento privado, sometido a reserva, donde se registra la atención del paciente conforme a la Resolución 1995 de 1999.</li>
                    <li><strong>Dato personal:</strong> cualquier información vinculada o susceptible de asociarse a una persona natural (Ley 1581 de 2012).</li>
                    <li><strong>PQRS:</strong> Peticiones, Quejas, Reclamos, Sugerencias y Denuncias.</li>
                </ul>
            </section>

            <section id="aceptacion" class="legal-seccion">
                <h2><span>03</span> Aceptación de los términos</h2>
                <p class="text-gray-600 leading-relaxed">
                    El ingreso al portal, la creación de una cuenta de usuario y la utilización de los
                    servicios implica la aceptación expresa e irrevocable de estos Términos y Condiciones
                    y de la Política de Privacidad. Si el usuario no está de acuerdo, deberá abstenerse
                    de usar la plataforma.
                </p>
            </section>

            <section id="uso-portal" class="legal-seccion">
                <h2><span>04</span> Uso del portal</h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    El usuario se compromete a:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-600 leading-relaxed">
                    <li>Suministrar información veraz, completa y actualizada en el momento del registro.</li>
                    <li>Mantener la confidencialidad de sus credenciales de acceso.</li>
                    <li>No suplantar la identidad de terceros ni acceder a cuentas ajenas.</li>
                    <li>Usar la plataforma únicamente para fines legítimos relacionados con su atención en salud.</li>
                    <li>No realizar conductas que afecten la disponibilidad, integridad o seguridad del sistema.</li>
                </ul>
            </section>

            <section id="derechos-paciente" class="legal-seccion">
                <h2><span>05</span> Derechos del paciente</h2>
                <p class="text-gray-600 leading-relaxed">
                    De acuerdo con la <strong>Resolución 13437 de 1991</strong> (Carta de Derechos y Deberes
                    del Paciente), el <strong>Decreto 1011 de 2006</strong> y la <strong>Ley 1751 de 2015</strong>,
                    todo paciente tiene derecho a:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-600 leading-relaxed mt-3">
                    <li>Recibir atención integral, oportuna, humanizada y libre de discriminación.</li>
                    <li>Ser informado sobre su diagnóstico, tratamiento, riesgos y alternativas.</li>
                    <li>Otorgar o negar su consentimiento informado de forma libre y voluntaria (Resolución 8430 de 1993).</li>
                    <li>La reserva y confidencialidad de la información contenida en su historia clínica.</li>
                    <li>Contar con una segunda opinión médica cuando lo estime necesario.</li>
                    <li>Recibir atención de urgencias cuando su vida o salud estén en riesgo.</li>
                    <li>Presentar reclamaciones y obtener respuesta oportuna.</li>
                </ul>
            </section>

            <section id="deberes-paciente" class="legal-seccion">
                <h2><span>06</span> Deberes del paciente</h2>
                <ul class="list-disc list-inside space-y-2 text-gray-600 leading-relaxed">
                    <li>Suministrar información veraz sobre su estado de salud.</li>
                    <li>Seguir las indicaciones y tratamientos prescritos por el personal de salud.</li>
                    <li>Asistir puntualmente a las citas programadas y notificar su cancelación anticipadamente.</li>
                    <li>Tratar con respeto al personal de la institución y a otros pacientes.</li>
                    <li>Cumplir las normas internas de la institución.</li>
                </ul>
            </section>

            <section id="historia-clinica" class="legal-seccion">
                <h2><span>07</span> Historia clínica</h2>
                <p class="text-gray-600 leading-relaxed">
                    La historia clínica es un documento <strong>privado, reservado y de uso exclusivo del
                    sector salud</strong> (Resolución 1995 de 1999). Su titular, sus representantes o las
                    autoridades competentes pueden solicitar copia de la misma conforme a la ley. PrismaSalud
                    IPS garantiza su custodia, confidencialidad y trazabilidad por un término mínimo de
                    20 años, de acuerdo con la normativa vigente.
                </p>
            </section>

            <section id="habeas-data" class="legal-seccion">
                <h2><span>08</span> Tratamiento de datos personales</h2>
                <p class="text-gray-600 leading-relaxed">
                    El tratamiento de los datos personales de los usuarios se realiza conforme a la
                    <strong>Ley 1581 de 2012</strong>, el <strong>Decreto 1377 de 2013</strong> y la
                    <strong>Sentencia C-748 de 2011</strong>. Para conocer las finalidades, los derechos
                    de los titulares (consulta, actualización, rectificación, supresión y revocación de la
                    autorización) y el procedimiento ante la Superintendencia de Industria y Comercio,
                    consulte nuestra
                    <a href="{{ route('privacidad.index') }}" class="text-teal-700 underline hover:text-teal-800">
                        Política de Privacidad</a>.
                </p>
            </section>

            <section id="responsabilidad" class="legal-seccion">
                <h2><span>09</span> Responsabilidad</h2>
                <p class="text-gray-600 leading-relaxed">
                    PrismaSalud IPS responderá por la calidad y seguridad de los servicios de salud que
                    presta, conforme a la normativa de habilitación y los estándares de acreditación.
                    La información publicada en el portal es de carácter informativo y no sustituye la
                    valoración médica directa. La institución no se hace responsable por el uso indebido
                    de la plataforma o por los daños derivados del incumplimiento de estos términos por
                    parte del usuario.
                </p>
            </section>

            <section id="pqrs" class="legal-seccion">
                <h2><span>10</span> PQRS</h2>
                <p class="text-gray-600 leading-relaxed">
                    Los usuarios y sus familias pueden presentar Peticiones, Quejas, Reclamos,
                    Sugerencias y Denuncias a través de los canales institucionales (presencial en la
                    sede de Calle 42 #5-28, telefónico al (601) 123-4667 o al correo
                    info@prismasaludips.com). PrismaSalud IPS responderá en los términos y dentro de
                    los plazos establecidos por la <strong>Ley 1755 de 2015</strong>.
                </p>
            </section>

            <section id="modificaciones" class="legal-seccion">
                <h2><span>11</span> Modificaciones</h2>
                <p class="text-gray-600 leading-relaxed">
                    PrismaSalud IPS podrá actualizar estos Términos y Condiciones en cualquier momento
                    para ajustarlos a modificaciones normativas o mejoras de sus servicios. Los cambios
                    serán publicados en esta página y se indicará la fecha de la última versión.
                </p>
            </section>

            <section id="ley-aplicable" class="legal-seccion">
                <h2><span>12</span> Ley aplicable y jurisdicción</h2>
                <p class="text-gray-600 leading-relaxed">
                    Estos términos se rigen por las leyes de la República de Colombia y cuentan con el
                    siguiente marco normativo de referencia:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-600 leading-relaxed mt-3 marker:text-teal-600">
                    <li>Ley 100 de 1993 — Sistema General de Seguridad Social en Salud.</li>
                    <li>Ley 1751 de 2015 — Ley Estatutaria de Salud.</li>
                    <li>Ley 1581 de 2012 y Decreto 1377 de 2013 — Protección de Datos Personales.</li>
                    <li>Resolución 1995 de 1999 — Normas de la Historia Clínica.</li>
                    <li>Resolución 13437 de 1991 — Carta de Derechos y Deberes del Paciente.</li>
                    <li>Resolución 8430 de 1993 — Normas científicas, técnicas y administrativas para la investigación en salud.</li>
                    <li>Decreto 780 de 2016 — Decreto Único Reglamentario del Sector Salud.</li>
                    <li>Ley 23 de 1981 — Ética del ejercicio profesional de la medicina.</li>
                    <li>Ley 1755 de 2015 — Derecho de petición.</li>
                </ul>
                <p class="text-gray-600 leading-relaxed mt-3">
                    Las controversias que surjan se someterán a los jueces de la República de Colombia,
                    con competencia en la ciudad de Ibagué, Tolima.
                </p>
            </section>

        </article>
    </div>

    <!-- Footer -->
    <footer class="text-white/80" style="background-color: #1A5C3A;">
        <div class="max-w-7xl mx-auto px-6 py-14 grid grid-cols-2 lg:grid-cols-5 gap-x-6 gap-y-10 md:gap-10 text-center md:text-left">
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
                    <li><a href="{{ url('/#servicios') }}" class="hover:text-white">Medicina General</a></li>
                    <li><a href="{{ url('/#servicios') }}" class="hover:text-white">Cardiología</a></li>
                    <li><a href="{{ url('/#servicios') }}" class="hover:text-white">Pediatría</a></li>
                </ul>
            </div>
            <div>
                <p class="text-white font-semibold mb-3">Navegación</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/') }}" class="hover:text-white">Inicio</a></li>
                    <li><a href="{{ route('nosotros') }}" class="hover:text-white">Nosotros</a></li>
                    <li><a href="{{ url('/#servicios') }}" class="hover:text-white">Servicios</a></li>
                    <li><a href="{{ url('/#contacto') }}" class="hover:text-white">Contacto</a></li>
                </ul>
            </div>
            <div>
                <p class="text-white font-semibold mb-3">Marco legal</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('terminos.index') }}" class="hover:text-white">Términos y Condiciones</a></li>
                    <li><a href="{{ route('privacidad.index') }}" class="hover:text-white">Política de Privacidad</a></li>
                    <li><a href="{{ url('/#contacto') }}" class="hover:text-white">PQRS</a></li>
                </ul>
            </div>
            <div class="col-span-2 lg:col-span-1">
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
                    <a href="{{ url('/#contacto') }}" class="hover:text-white transition">PQRS</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Header con sombra al hacer scroll
        const header = document.getElementById('site-header');
        const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 10);
        window.addEventListener('scroll', onScroll);
        onScroll();

        // Menu hamburguesa
        const botonHamburguesa = document.getElementById('boton-hamburguesa');
        const botonCerrarDrawer = document.getElementById('boton-cerrar-drawer');
        const overlayMovil = document.getElementById('overlay-movil');
        const drawerMovil = document.getElementById('drawer-movil');
        const abrirDrawer = () => { drawerMovil.classList.add('abierto'); overlayMovil.classList.add('abierto'); };
        const cerrarDrawer = () => { drawerMovil.classList.remove('abierto'); overlayMovil.classList.remove('abierto'); };
        botonHamburguesa.addEventListener('click', abrirDrawer);
        botonCerrarDrawer.addEventListener('click', cerrarDrawer);
        overlayMovil.addEventListener('click', cerrarDrawer);

        // Animacion reveal
        const observadorReveal = new IntersectionObserver((entradas) => {
            entradas.forEach((e) => { if (e.isIntersecting) { e.target.classList.add('visible'); observadorReveal.unobserve(e.target); } });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach((el) => observadorReveal.observe(el));
    </script>

</body>
</html>