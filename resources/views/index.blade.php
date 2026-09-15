{{--
    =====================================================================
    VISTA: index.blade.php
    PROYECTO: Prisma Salud IPS - Sistema de gestión de citas médicas
    DESCRIPCIÓN: Página de inicio (pública), visible para cualquier
    visitante sin necesidad de iniciar sesión.

    ESTRUCTURA DE LA PÁGINA:
      1. Navbar (logo + menú + botones de sesión/agendar)
      2. Hero (encabezado principal con tarjetas de servicios destacados)
      3. Barra de estadísticas (números de la IPS)
      4. Sección "Nuestros servicios" (6 especialidades)
      5. Sección "Testimonios" (opiniones de pacientes)
      6. Banner CTA (llamado a la acción para agendar cita)
      7. Footer (pie de página con navegación y contacto)

    NOTA: se usa Tailwind CSS vía CDN para no complicar el build de
    Laravel con Vite/NPM mientras se define el flujo de trabajo. Si
    luego se decide compilar Tailwind con Vite, este mismo HTML se
    puede reutilizar casi sin cambios.
    =====================================================================
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrismaSalud IPS - Tu salud es nuestra razón de ser</title>

    {{-- Tailwind CSS vía CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Fuente Inter, similar a la usada en el diseño original de Figma --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* Fuente base de todo el documento */
        body { font-family: 'Inter', sans-serif; }

        /* Degradado principal del hero y del banner CTA:
           va de un verde-esmeralda oscuro a un teal más claro,
           tal como se ve en el diseño (de #14532d aprox a #0d9488) */
        .gradiente-salud {
            background: linear-gradient(120deg, #14532d 0%, #0f766e 60%, #0d9488 100%);
        }

        /* =====================================================
           HEADER "TRANSPARENTOSO" AL HACER SCROLL
           Estado inicial: fijo en la parte de arriba, con fondo
           blanco sólido (igual que el diseño original). Cuando el
           usuario baja la página, se le agrega la clase "scrolled"
           (con JS, ver más abajo) y el header pasa a un blanco
           semi-transparente con efecto de desenfoque (blur) detrás,
           dejando ver un poco lo que hay debajo al bajar.
           ===================================================== */
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

        /* El logo, el menú y el botón de "Inicio sesión" se quedan
           siempre en tonos oscuros/teal, porque el header siempre
           es claro (blanco u opaco), nunca oscuro. */
        .site-header .logo-text { color: #111827; }     /* gray-900 */
        .site-header .logo-sub { color: #9ca3af; }       /* gray-400 */
        .site-header .brand-accent { color: #0d9488; }   /* teal-600 */
        .site-header .nav-link { color: #4b5563; }       /* gray-600 */
        .site-header .nav-link:hover { color: #0d9488; }

        .site-header .btn-login {
            color: #0d9488;
            border-color: #0d9488;
        }
        .site-header .btn-login:hover { background-color: #f0fdfa; }

        /* Como el header ahora es "fixed" (fuera del flujo normal
           del documento), el contenido de abajo necesita un espacio
           reservado arriba para que el hero no quede tapado por el
           header. Ese espacio lo da esta clase, usada justo después
           del </header>. */
        .header-spacer { height: 80px; }
    </style>
</head>
<body class="bg-white text-gray-800">

    {{-- =========================================================
         1. NAVBAR
         Header fijo (position: fixed) con fondo blanco, que se
         vuelve semi-transparente con blur al hacer scroll (ver
         clase "scrolled" agregada por el script al final).
    ========================================================= --}}
    <header id="site-header" class="site-header w-full">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">

            {{-- Logo + nombre de la IPS.
                 IMPORTANTE - dónde poner tu ícono/imagen:
                 1) Guarda el archivo de tu ícono (ej: logo.png) dentro de
                    la carpeta "public/img/" de tu proyecto Laravel.
                    Ruta completa: prisma-salud/public/img/logo.png
                 2) Si la carpeta "img" no existe dentro de "public",
                    créala tú mismo y pon la imagen ahí.
                 3) La referenciamos con el helper asset(), que arma la
                    ruta pública automáticamente: asset('img/logo.png')
                    (NUNCA se debe usar la ruta física de Windows,
                    solo la ruta relativa dentro de "public"). --}}
            <div class="flex items-center gap-2">
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

            {{-- Menú de navegación principal --}}
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="{{ url('/') }}" class="nav-link">Inicio</a>
                <a href="#nosotros" class="nav-link">Nosotros</a>
                <a href="#servicios" class="nav-link">Servicios</a>
                <a href="#blog" class="nav-link">Blog</a>
                <a href="#contacto" class="nav-link">Contacto</a>
            </nav>

            {{-- Botones de acción:
                 - Inicio sesión: lleva al formulario de login (con session_start)
                 - Agendar cita: acceso directo para pacientes a la solicitud de citas --}}
            <div class="flex items-center gap-3">
                <a href="{{ url('/login') }}"
                   class="btn-login px-4 py-2 text-sm font-semibold border rounded-md">
                    Inicio sesión
                </a>
                <a href="{{ url('/citas/agendar') }}"
                   class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-md hover:bg-green-700">
                    Agendar cita
                </a>
            </div>
        </div>
    </header>

    {{-- Espaciador: como el header es "fixed", este div reserva el
         espacio que el header ocupaba en el flujo normal, para que
         el hero no quede escondido debajo de él. --}}
    <div class="header-spacer"></div>

    {{-- =========================================================
         2. HERO
         Encabezado principal con el mensaje de bienvenida a la
         izquierda y las 4 tarjetas de servicios destacados a la
         derecha, sobre un fondo con degradado verde/teal.
    ========================================================= --}}
    <section class="gradiente-salud text-white">
        <div class="max-w-7xl mx-auto px-6 py-16 grid md:grid-cols-2 gap-12 items-center">

            {{-- Columna izquierda: texto principal --}}
            <div>
                {{-- Insignia / badge de certificación --}}
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
                    <a href="{{ url('/citas/agendar') }}"
                       class="px-5 py-3 bg-green-600 rounded-md font-semibold hover:bg-green-700 transition">
                        Agendar cita →
                    </a>
                </div>
            </div>

            {{-- Columna derecha: tarjetas de servicios destacados.
                 Se recorren con un array de PHP para no repetir HTML
                 (más fácil de mantener si luego se agrega/quita un servicio). --}}
            @php
                // Lista de servicios destacados que se muestran en el hero.
                // icono: se usa un emoji simple como placeholder; se puede
                // cambiar por un ícono SVG o de una librería (heroicons, etc.)
                $serviciosDestacados = [
                    ['icono' => '❤️', 'titulo' => 'Consulta médica',  'texto' => 'Medicina general y especializada'],
                    ['icono' => '💊', 'titulo' => 'Farmacia interna', 'texto' => 'Medicamentos con descuento EPS'],
                    ['icono' => '🧪', 'titulo' => 'Laboratorio clínico', 'texto' => 'Resultados en 24 horas'],
                    ['icono' => '📅', 'titulo' => 'Citas en línea',   'texto' => 'Agenda fácil desde tu celular'],
                ];
            @endphp

            <div class="space-y-4">
                @foreach ($serviciosDestacados as $servicio)
                    <div class="flex items-center gap-4 bg-white/10 rounded-lg px-5 py-4">
                        <div class="w-10 h-10 flex items-center justify-center bg-white/20 rounded-md text-lg">
                            {{ $servicio['icono'] }}
                        </div>
                        <div>
                            <p class="font-semibold">{{ $servicio['titulo'] }}</p>
                            <p class="text-sm text-white/70">{{ $servicio['texto'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- =====================================================
             3. BARRA DE ESTADÍSTICAS
             Franja inferior del hero con las cifras institucionales.
        ===================================================== --}}
        <div class="bg-green-950/40 border-t border-white/10">
            @php
                // Cifras institucionales mostradas como prueba social.
                $estadisticas = [
                    ['numero' => '+18K', 'texto' => 'Pacientes atendidos'],
                    ['numero' => '35+',  'texto' => 'Profesionales de salud'],
                    ['numero' => '12',   'texto' => 'Especialidades médicas'],
                    ['numero' => '99%',  'texto' => 'Satisfacción del paciente'],
                    ['numero' => '26',   'texto' => 'Años de experiencia'],
                ];
            @endphp
            <div class="max-w-7xl mx-auto px-6 py-8 grid grid-cols-2 md:grid-cols-5 gap-6 text-center">
                @foreach ($estadisticas as $stat)
                    <div>
                        <p class="text-3xl font-extrabold">{{ $stat['numero'] }}</p>
                        <p class="text-sm text-white/70">{{ $stat['texto'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =========================================================
         4. SECCIÓN "NUESTROS SERVICIOS"
         Listado de las 6 especialidades médicas que ofrece la IPS.
    ========================================================= --}}
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
            // Catálogo de especialidades mostradas en la vista pública.
            // Esta misma información, más adelante, puede venir de una
            // tabla "especialidades" en la base de datos en vez de un
            // arreglo fijo aquí en la vista.
            $especialidades = [
                ['icono' => '📈', 'titulo' => 'Medicina General', 'texto' => 'Consulta preventiva, diagnóstico y tratamiento de enfermedades comunes para pacientes de todas las edades.'],
                ['icono' => '❤️', 'titulo' => 'Cardiología',      'texto' => 'Evaluación cardiovascular con equipos de última generación. Electrocardiogramas, ecocardiogramas y más.'],
                ['icono' => '👶', 'titulo' => 'Pediatría',        'texto' => 'Cuidado especializado para niños desde el nacimiento hasta la adolescencia. Controles de crecimiento y vacunación.'],
                ['icono' => '🧪', 'titulo' => 'Laboratorio Clínico', 'texto' => 'Análisis de sangre, orina, cultivos y más. Resultados rápidos y entrega digital desde nuestra plataforma.'],
                ['icono' => '🙂', 'titulo' => 'Odontología',      'texto' => 'Salud oral completa: limpieza, ortodoncia, endodoncia y cirugía oral en un ambiente cómodo y moderno.'],
                ['icono' => '🧠', 'titulo' => 'Psicología',       'texto' => 'Acompañamiento psicológico para el manejo del estrés, ansiedad, depresión y bienestar emocional.'],
            ];
        @endphp

        <div class="grid md:grid-cols-3 gap-6">
            @foreach ($especialidades as $esp)
                <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition">
                    <div class="text-2xl mb-3">{{ $esp['icono'] }}</div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $esp['titulo'] }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $esp['texto'] }}</p>
                    <a href="#" class="text-teal-600 text-sm font-semibold hover:underline">Conocer más →</a>
                </div>
            @endforeach
        </div>
    </section>

    {{-- =========================================================
         5. SECCIÓN "TESTIMONIOS"
         Opiniones de pacientes con calificación en estrellas.
    ========================================================= --}}
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                Testimonios
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-10">
                Lo que dicen nuestros pacientes
            </h2>

            @php
                // Testimonios de pacientes. "estrellas" va de 1 a 5.
                $testimonios = [
                    ['estrellas' => 5, 'texto' => 'Excelente atención desde la recepción hasta el médico. El proceso de agendar cita fue muy sencillo y rápido. Totalmente recomendado.', 'iniciales' => 'MP', 'nombre' => 'María Pérez', 'desde' => 'Paciente desde 2021'],
                    ['estrellas' => 5, 'texto' => 'Los médicos son muy humanos y profesionales. Me siento en buenas manos cada vez que vengo. Las instalaciones son limpias y modernas.', 'iniciales' => 'JR', 'nombre' => 'Jose Rodríguez', 'desde' => 'Paciente desde 2019'],
                    ['estrellas' => 4, 'texto' => 'El servicio de pediatría es excepcional. Mi hijo siempre sale tranquilo después de sus controles. Los tiempos de espera son muy cortos.', 'iniciales' => 'CG', 'nombre' => 'Carolina Gómez', 'desde' => 'Paciente desde 2022'],
                ];
            @endphp

            <div class="grid md:grid-cols-3 gap-6 text-left">
                @foreach ($testimonios as $t)
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        {{-- Estrellas: se pintan tantas como indique "estrellas" --}}
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

    {{-- =========================================================
         6. BANNER CTA (llamado a la acción)
         Franja con degradado que invita a agendar una cita.
    ========================================================= --}}
    <section class="gradiente-salud text-white">
        <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <h2 class="text-2xl md:text-3xl font-bold text-center md:text-left">
                ¿Listo para cuidar tu <span class="text-teal-300">salud</span> hoy?
            </h2>
            <a href="{{ url('/citas/agendar') }}"
               class="px-6 py-3 bg-white text-teal-700 font-semibold rounded-md hover:bg-gray-100 transition whitespace-nowrap">
                Agendar tu cita ahora →
            </a>
        </div>
    </section>

    {{-- =========================================================
         7. FOOTER
         Pie de página con datos de la IPS, navegación, servicios
         y datos de contacto.
    ========================================================= --}}
    <footer class="bg-green-950 text-white/80">
        <div class="max-w-7xl mx-auto px-6 py-14 grid md:grid-cols-4 gap-10">

            {{-- Columna 1: nombre + descripción + redes sociales --}}
            <div>
                <p class="text-white font-bold text-lg mb-2">
                    Prisma<span class="text-teal-300">Salud</span> IPS
                </p>
                <p class="text-sm mb-4">
                    Institución Prestadora de Servicios de Salud comprometida con el
                    bienestar integral de las familias del Tolima desde el 2000.
                </p>
                <p class="text-white font-semibold text-sm mb-2">Redes Sociales</p>
                <div class="flex gap-3">
                    <a href="#" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">f</a>
                    <a href="#" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">ig</a>
                    <a href="#" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">wa</a>
                </div>
            </div>

            {{-- Columna 2: navegación --}}
            <div>
                <p class="text-white font-semibold mb-3">Navegación</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/') }}" class="hover:text-white">Inicio</a></li>
                    <li><a href="#nosotros" class="hover:text-white">Nosotros</a></li>
                    <li><a href="#servicios" class="hover:text-white">Servicios</a></li>
                    <li><a href="#blog" class="hover:text-white">Blog</a></li>
                    <li><a href="#contacto" class="hover:text-white">Contacto</a></li>
                </ul>
            </div>

            {{-- Columna 3: servicios (mismas especialidades del catálogo) --}}
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

            {{-- Columna 4: datos de contacto --}}
            <div id="contacto">
                <p class="text-white font-semibold mb-3">Contacto</p>
                <ul class="space-y-2 text-sm">
                    <li>📍 Calle 42 #5-28, Ibagué</li>
                    <li>📞 (601) 123-4667</li>
                    <li>✉️ info@prismasaludips.com</li>
                    <li>🕐 Lun - Vie: 7am - 7pm</li>
                </ul>
            </div>
        </div>
    </footer>

    {{-- =========================================================
         SCRIPT: header transparente -> header con fondo al hacer scroll
         Escucha el evento "scroll" de la ventana. Si el usuario ya
         bajó más de 40px, le agrega la clase "scrolled" al header
         (con eso entran a jugar los estilos .site-header.scrolled
         definidos arriba en el <style>). Si vuelve a subir, se la quita.
    ========================================================= --}}
    <script>
        const siteHeader = document.getElementById('site-header');

        function actualizarHeaderSegunScroll() {
            if (window.scrollY > 40) {
                siteHeader.classList.add('scrolled');
            } else {
                siteHeader.classList.remove('scrolled');
            }
        }

        // Se ejecuta cada vez que el usuario hace scroll...
        window.addEventListener('scroll', actualizarHeaderSegunScroll);
        // ...y también una vez al cargar, por si la página abre con scroll restaurado.
        document.addEventListener('DOMContentLoaded', actualizarHeaderSegunScroll);
    </script>

</body>
</html>