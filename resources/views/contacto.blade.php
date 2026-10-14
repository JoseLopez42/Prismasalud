<!-- contacto.blade.php - Pagina de contacto -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - PrismaSalud IPS</title>

    <meta name="description" content="Contacta a PrismaSalud IPS en Ibagué: dirección, teléfono, correo, horarios, formulario de contacto y canal de PQRS.">

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

        /* Tarjetas de datos de contacto */
        .dato-contacto {
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .dato-contacto:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        }

        /* Formulario */
        .campo-form {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.7rem 0.9rem;
            font-size: 0.95rem;
            color: #111827;
            background-color: #fff;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .campo-form::placeholder { color: #9ca3af; }
        .campo-form:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(18, 165, 148, 0.15);
        }
        .campo-form.invalido {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12);
        }
        .msg-error {
            display: none;
            color: #dc2626;
            font-size: 0.78rem;
            margin-top: 0.35rem;
        }
        .msg-error.visible { display: block; }

        .aviso-exito { display: none; }
        .aviso-exito.visible { display: flex; }

        /* Mapa embebido */
        .mapa-wrap {
            border-radius: 0.75rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .mapa-wrap iframe {
            display: block;
            width: 100%;
            height: 100%;
            min-height: 300px;
            border: 0;
        }

        /* Acordeon de preguntas frecuentes */
        .faq-contacto-respuesta {
            max-height: 0;
            overflow: hidden;
            transition: max-height .3s ease;
        }
        .faq-contacto-icono { transition: transform .3s ease; }
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
                <a href="{{ route('servicios.index') }}" class="nav-link">Servicios</a>
                <a href="{{ route('blog.index') }}" class="nav-link">Blog</a>
                <a href="{{ route('contacto.index') }}" class="nav-link text-sm" style="color:var(--teal);">Contacto</a>
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
                <a href="{{ route('contacto.index') }}" class="drawer-link" style="color:var(--teal);">
                    <span class="drawer-icono bg-amber-50 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    </span> Contacto
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
                Contáctanos
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                Estamos para <span class="text-teal-300">atenderte</span>
            </h1>
            <p class="text-white/80 max-w-2xl mx-auto">
                Escríbenos, llámanos o visítanos en nuestra sede de Ibagué. Nuestro equipo
                responde tus dudas, sugerencias y peticiones de forma oportuna.
            </p>
        </div>
    </section>

    <!-- Datos de contacto rápidos -->
    <section class="max-w-7xl mx-auto px-6 -mt-8 relative z-10">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="reveal dato-contacto bg-white border border-gray-200 rounded-xl p-6 text-center">
                <div class="w-11 h-11 mx-auto flex items-center justify-center bg-teal-50 text-teal-600 rounded-full mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                </div>
                <h2 class="font-semibold text-gray-900 mb-1">Dirección</h2>
                <a href="https://www.google.com/maps?q=Calle+42+%235-28,+Ibagué,+Tolima"
                   target="_blank" rel="noopener"
                   class="text-sm text-gray-500 hover:text-teal-600 transition break-words">
                    Calle 42 #5-28, Ibagué, Tolima
                </a>
            </div>

            <div class="reveal dato-contacto bg-white border border-gray-200 rounded-xl p-6 text-center">
                <div class="w-11 h-11 mx-auto flex items-center justify-center bg-teal-50 text-teal-600 rounded-full mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5c0 9 7 16 16 16l3-4-6-3-2 2c-2-1-4-3-5-5l2-2-3-6z"/></svg>
                </div>
                <h2 class="font-semibold text-gray-900 mb-1">Teléfono</h2>
                <a href="tel:+573017880161" class="text-sm text-gray-500 hover:text-teal-600 transition">
                    (601) 123-4667
                </a>
            </div>

            <div class="reveal dato-contacto bg-white border border-gray-200 rounded-xl p-6 text-center">
                <div class="w-11 h-11 mx-auto flex items-center justify-center bg-teal-50 text-teal-600 rounded-full mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" d="M3 7l9 6 9-6"/></svg>
                </div>
                <h2 class="font-semibold text-gray-900 mb-1">Correo</h2>
                <a href="mailto:info@prismasaludips.com" class="text-sm text-gray-500 hover:text-teal-600 transition break-words">
                    info@prismasaludips.com
                </a>
            </div>

            <div class="reveal dato-contacto bg-white border border-gray-200 rounded-xl p-6 text-center">
                <div class="w-11 h-11 mx-auto flex items-center justify-center bg-teal-50 text-teal-600 rounded-full mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/></svg>
                </div>
                <h2 class="font-semibold text-gray-900 mb-1">Horario</h2>
                <p class="text-sm text-gray-500">Lun a Vie 7am–7pm · Sáb 8am–2pm</p>
            </div>
        </div>
    </section>

    <!-- Formulario + información -->
    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid lg:grid-cols-3 gap-8 items-start">

            <!-- Formulario de contacto -->
            <div class="lg:col-span-2 reveal">
                <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                    Escríbenos
                </span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                    Envíanos un mensaje
                </h2>
                <p class="text-gray-500 mb-6">
                    Completa el formulario y te respondemos al correo que nos dejes.
                    Los campos con <span class="text-red-500">*</span> son obligatorios.
                </p>

                <!-- Aviso de envío exitoso -->
                <div id="aviso-exito" class="aviso-exito items-start gap-3 bg-teal-50 border border-teal-200 rounded-xl p-5 mb-6" role="status" aria-live="polite">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-shrink-0 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="font-semibold text-teal-800 mb-1">Mensaje enviado correctamente</p>
                        <p class="text-sm text-teal-700">
                            Gracias por escribirnos. Nuestro equipo te responderá lo antes posible
                            a tu correo electrónico.
                        </p>
                    </div>
                </div>

                <form id="form-contacto" novalidate class="bg-gray-50 border border-gray-200 rounded-xl p-6 md:p-8">
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Nombre completo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nombre" name="nombre" class="campo-form" placeholder="Ej. María Pérez" autocomplete="name">
                            <p class="msg-error" data-error="nombre">Escribe tu nombre completo (mínimo 3 letras).</p>
                        </div>

                        <div>
                            <label for="correo" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Correo electrónico <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="correo" name="correo" class="campo-form" placeholder="tucorreo@ejemplo.com" autocomplete="email">
                            <p class="msg-error" data-error="correo">Escribe un correo electrónico válido.</p>
                        </div>

                        <div>
                            <label for="telefono" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Teléfono <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="telefono" name="telefono" class="campo-form" placeholder="Ej. 300 123 4567" autocomplete="tel">
                            <p class="msg-error" data-error="telefono">Escribe un número de teléfono válido (mínimo 7 dígitos).</p>
                        </div>

                        <div>
                            <label for="asunto" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Asunto <span class="text-red-500">*</span>
                            </label>
                            <select id="asunto" name="asunto" class="campo-form">
                                <option value="">Selecciona una opción</option>
                                <option value="consulta">Consulta general</option>
                                <option value="cita">Agendar una cita</option>
                                <option value="pqrs">PQRS (peticiones, quejas, reclamos)</option>
                                <option value="servicios">Información sobre servicios</option>
                                <option value="facturacion">Facturación y pagos</option>
                                <option value="otro">Otro</option>
                            </select>
                            <p class="msg-error" data-error="asunto">Selecciona el asunto de tu mensaje.</p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label for="mensaje" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Mensaje <span class="text-red-500">*</span>
                        </label>
                        <textarea id="mensaje" name="mensaje" rows="5" class="campo-form resize-y" placeholder="Cuéntanos en qué podemos ayudarte..."></textarea>
                        <p class="msg-error" data-error="mensaje">Escribe tu mensaje (mínimo 10 caracteres).</p>
                    </div>

                    <div class="mt-5">
                        <label class="flex items-start gap-3 text-sm text-gray-600">
                            <input type="checkbox" id="consentimiento" name="consentimiento" class="mt-1 w-4 h-4 flex-shrink-0" style="accent-color: var(--teal);">
                            <span>
                                Autorizo el tratamiento de mis datos personales conforme a la
                                <a href="{{ route('privacidad.index') }}" class="underline hover:text-teal-600">Política de Privacidad</a>.
                                <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <p class="msg-error" data-error="consentimiento">Debes autorizar el tratamiento de tus datos.</p>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row sm:items-center gap-4">
                        <button type="submit"
                                class="btn-cta justify-center"
                                id="boton-enviar">
                            Enviar mensaje
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 12h15"/></svg>
                        </button>
                        <p class="text-xs text-gray-400">
                            Respondemos en un máximo de 24 horas hábiles.
                        </p>
                    </div>
                </form>
            </div>

            <!-- Columna lateral: mapa, canales y horario -->
            <aside class="space-y-6">
                <div class="reveal mapa-wrap">
                    <iframe
                        title="Ubicación de PrismaSalud IPS en Ibagué"
                        src="https://www.google.com/maps?q=Calle+42+%235-28,+Ibagué,+Tolima&output=embed"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                <div class="reveal bg-gray-50 border border-gray-200 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Otros canales</h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--teal);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5c0 9 7 16 16 16l3-4-6-3-2 2c-2-1-4-3-5-5l2-2-3-6z"/></svg>
                            <span>Citas y consultas: <strong>(601) 123-4667</strong></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--teal);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" d="M3 7l9 6 9-6"/></svg>
                            <span class="break-words">Correo: <strong>info@prismasaludips.com</strong></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--teal);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Atención al usuario / PQRS: en recepción o por el formulario</span>
                        </li>
                    </ul>
                </div>

                <div class="reveal bg-white border border-gray-200 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Horario de atención</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center justify-between gap-3">
                            <span>Lunes a viernes</span>
                            <strong class="text-gray-900 whitespace-nowrap">7:00 a.m. – 7:00 p.m.</strong>
                        </li>
                        <li class="flex items-center justify-between gap-3">
                            <span>Sábados</span>
                            <strong class="text-gray-900 whitespace-nowrap">8:00 a.m. – 2:00 p.m.</strong>
                        </li>
                        <li class="flex items-center justify-between gap-3">
                            <span>Domingos y festivos</span>
                            <strong class="text-gray-900 whitespace-nowrap">Cerrado</strong>
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>

    <!-- PQRS -->
    <section id="pqrs" class="bg-gray-50 py-20 scroll-mt-24">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-12">
                <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                    Atención al usuario
                </span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Peticiones, quejas, reclamos y sugerencias
                </h2>
                <p class="text-gray-500 mt-3 max-w-2xl mx-auto">
                    Tu opinión nos ayuda a mejorar. Elige el formulario con asunto
                    <strong>PQRS</strong> o escríbenos directamente a nuestro correo.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="reveal bg-white border border-gray-200 rounded-xl p-6">
                    <div class="w-11 h-11 flex items-center justify-center bg-teal-50 text-teal-600 rounded-lg mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5M21 12a9 9 0 11-3.4-7L21 4v6.5"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">¿Qué puedes radicar?</h3>
                    <p class="text-sm text-gray-500">
                        Peticiones, quejas, reclamos, sugerencias y felicitaciones sobre
                        nuestro servicio y atención.
                    </p>
                </div>

                <div class="reveal bg-white border border-gray-200 rounded-xl p-6">
                    <div class="w-11 h-11 flex items-center justify-center bg-teal-50 text-teal-600 rounded-lg mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">¿Dónde radicarlos?</h3>
                    <p class="text-sm text-gray-500">
                        Por este sitio (asunto <strong>PQRS</strong>), al correo
                        <span class="break-words">info@prismasaludips.com</span> o de forma
                        presencial en recepción.
                    </p>
                </div>

                <div class="reveal bg-white border border-gray-200 rounded-xl p-6">
                    <div class="w-11 h-11 flex items-center justify-center bg-teal-50 text-teal-600 rounded-lg mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Tiempos de respuesta</h3>
                    <p class="text-sm text-gray-500">
                        Peticiones de interés particular: máximo 15 días hábiles.
                        Quejas y reclamos: se atienden con prioridad según la normatividad
                        vigente en salud.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Derechos del paciente -->
    <section id="derechos" class="max-w-6xl mx-auto px-6 py-20 scroll-mt-24">
        <div class="text-center mb-12">
            <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                Tu bienestar primero
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                Derechos del paciente
            </h2>
            <p class="text-gray-500 mt-3 max-w-2xl mx-auto">
                Como paciente de PrismaSalud IPS tienes estos derechos en todo momento
                de tu atención.
            </p>
        </div>

        @php
            $derechos = [
                ['titulo' => 'Atención con dignidad', 'texto' => 'Ser tratado con respeto, calidez y discriminación cero en cada servicio.'],
                ['titulo' => 'Información clara', 'texto' => 'Conocer tu estado de salud, diagnóstico, tratamientos y costos de forma oportuna.'],
                ['titulo' => 'Confidencialidad', 'texto' => 'Tu historia clínica y datos personales son protegidos y de acceso restringido.'],
                ['titulo' => 'Decidir libremente', 'texto' => 'Aceptar o rechazar tratamientos después de recibir información completa (consentimiento informado).'],
                ['titulo' => 'Peticiones sin represalias', 'texto' => 'Presentar quejas, reclamos y peticiones sin que ello afecte tu atención.'],
                ['titulo' => 'Acceso a tus documentos', 'texto' => 'Solicitar copias de tu historia clínica y demás documentos que te corresponden.'],
            ];
        @endphp

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($derechos as $derecho)
                <div class="reveal border border-gray-200 rounded-xl p-6 hover:shadow-md transition">
                    <div class="w-9 h-9 flex items-center justify-center bg-teal-50 text-teal-600 rounded-full mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ $derecho['titulo'] }}</h3>
                    <p class="text-sm text-gray-500">{{ $derecho['texto'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Preguntas frecuentes de contacto -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-3xl mx-auto px-6">
            <div class="text-center mb-10">
                <span class="inline-block border border-teal-500 text-teal-600 text-xs px-3 py-1 rounded-full mb-4">
                    Ayuda rápida
                </span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Preguntas frecuentes de contacto
                </h2>
            </div>

            @php
                $preguntasContacto = [
                    ['pregunta' => '¿Cuál es el horario de atención?', 'respuesta' => 'Atendemos de lunes a viernes de 7:00 a.m. a 7:00 p.m. y los sábados de 8:00 a.m. a 2:00 p.m. Domingos y festivos cerrado.'],
                    ['pregunta' => '¿Cómo agendo una cita?', 'respuesta' => 'Puedes agendar en línea desde tu cuenta de usuario o llamando al (601) 123-4667. También puedes acercarte a recepción.'],
                    ['pregunta' => '¿Atienden urgencias?', 'respuesta' => 'En horario de atención manejamos urgencias y consulta espontánea. Si es una emergencia vital, llama de inmediato al 123.'],
                    ['pregunta' => '¿En cuánto tiempo responden el formulario?', 'respuesta' => 'Normalmente en un máximo de 24 horas hábiles. Los PQRS se responden dentro de los términos que marca la normatividad.'],
                    ['pregunta' => '¿Dónde están ubicados?', 'respuesta' => 'En la Calle 42 #5-28, Ibagué, Tolima. Puedes ver la ruta exacta en el mapa de esta página.'],
                ];
            @endphp

            <div class="space-y-3">
                @foreach ($preguntasContacto as $p)
                    <div class="faq-contacto-item border border-gray-200 rounded-lg bg-white overflow-hidden">
                        <button type="button"
                                class="faq-contacto-pregunta w-full flex items-center justify-between text-left px-5 py-4 font-semibold text-gray-800"
                                aria-expanded="false">
                            <span>{{ $p['pregunta'] }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="faq-contacto-icono w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="faq-contacto-respuesta px-5 text-sm text-gray-500">
                            <p class="pb-4">{{ $p['respuesta'] }}</p>
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

        // Acordeon de preguntas frecuentes
        document.querySelectorAll('.faq-contacto-item').forEach(function (item) {
            const boton = item.querySelector('.faq-contacto-pregunta');
            const respuesta = item.querySelector('.faq-contacto-respuesta');
            const icono = item.querySelector('.faq-contacto-icono');

            boton.addEventListener('click', function () {
                const abierta = boton.getAttribute('aria-expanded') === 'true';

                document.querySelectorAll('.faq-contacto-pregunta[aria-expanded="true"]').forEach(function (otroBoton) {
                    if (otroBoton !== boton) {
                        otroBoton.setAttribute('aria-expanded', 'false');
                        otroBoton.closest('.faq-contacto-item').querySelector('.faq-contacto-respuesta').style.maxHeight = '0px';
                        otroBoton.closest('.faq-contacto-item').querySelector('.faq-contacto-icono').style.transform = 'rotate(0deg)';
                    }
                });

                boton.setAttribute('aria-expanded', String(!abierta));
                respuesta.style.maxHeight = abierta ? '0px' : respuesta.scrollHeight + 'px';
                icono.style.transform = abierta ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        });

        // Validacion del formulario de contacto
        const formContacto = document.getElementById('form-contacto');
        const avisoExito = document.getElementById('aviso-exito');

        function marcarError(campo, hayError) {
            const msg = document.querySelector('.msg-error[data-error="' + campo.id + '"]');
            if (hayError) {
                campo.classList.add('invalido');
                if (msg) msg.classList.add('visible');
            } else {
                campo.classList.remove('invalido');
                if (msg) msg.classList.remove('visible');
            }
            return !hayError;
        }

        function validarCampo(campo) {
            const valor = (campo.value || '').trim();

            if (campo.id === 'nombre') {
                return marcarError(campo, valor.length < 3 || !/[a-zA-ZáéíóúñÁÉÍÓÚÑ]/.test(valor));
            }
            if (campo.id === 'correo') {
                return marcarError(campo, !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(valor));
            }
            if (campo.id === 'telefono') {
                const digitos = valor.replace(/\D/g, '');
                return marcarError(campo, digitos.length < 7 || digitos.length > 15);
            }
            if (campo.id === 'asunto') {
                return marcarError(campo, valor === '');
            }
            if (campo.id === 'mensaje') {
                return marcarError(campo, valor.length < 10);
            }
            if (campo.id === 'consentimiento') {
                return marcarError(campo, !campo.checked);
            }
            return true;
        }

        ['nombre', 'correo', 'telefono', 'asunto', 'mensaje', 'consentimiento'].forEach(function (id) {
            const campo = document.getElementById(id);
            if (!campo) return;
            const evento = (campo.tagName === 'SELECT' || campo.type === 'checkbox') ? 'change' : 'blur';
            campo.addEventListener(evento, function () { validarCampo(campo); });
            campo.addEventListener('input', function () {
                if (campo.classList.contains('invalido')) validarCampo(campo);
            });
        });

        formContacto.addEventListener('submit', function (e) {
            e.preventDefault();

            const campos = ['nombre', 'correo', 'telefono', 'asunto', 'mensaje', 'consentimiento']
                .map(function (id) { return document.getElementById(id); })
                .filter(Boolean);

            let todoValido = true;
            let primerInvalido = null;

            campos.forEach(function (campo) {
                const ok = validarCampo(campo);
                if (!ok && !primerInvalido) primerInvalido = campo;
                todoValido = todoValido && ok;
            });

            if (!todoValido) {
                avisoExito.classList.remove('visible');
                if (primerInvalido) {
                    primerInvalido.focus({ preventScroll: false });
                    primerInvalido.scrollIntoView({ block: 'center', behavior: 'smooth' });
                }
                return;
            }

            formContacto.reset();
            campos.forEach(function (campo) { marcarError(campo, false); });
            avisoExito.classList.add('visible');
            avisoExito.scrollIntoView({ block: 'center', behavior: 'smooth' });
        });
    </script>

    @include('partials.cookies')

    <script src="{{ asset('js/validacion-live.js') }}"></script>
</body>
</html>
