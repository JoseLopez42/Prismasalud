<!-- planes/detalle.blade.php - Vista detallada de un plan -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan {{ $plan['nombre'] }} - PrismaSalud IPS</title>
    <meta name="description" content="Conoce a fondo el plan {{ $plan['nombre'] }} de PrismaSalud: beneficios, límites, flujo de prescripciones y preguntas frecuentes.">

    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --azul: #1B4D6B;
            --azul-claro: #2E86AB;
            --naranja: #0C7B93;
            --teal: #12A594;
            --verde-oscuro: #14532D;
        }

        body { font-family: 'Inter', sans-serif; }
        .detalle-hero { background: linear-gradient(120deg, var(--azul) 0%, var(--teal) 58%, var(--naranja) 100%); }
        .plan-icon--teal { color: #0f766e; background: #ccfbf1; }
        .plan-icon--blue { color: #1d4ed8; background: #dbeafe; }
        .plan-icon--purple { color: #7e22ce; background: #f3e8ff; }
        .plan-icon--pink { color: #be185d; background: #fce7f3; }
        .plan-icon--green { color: #15803d; background: #dcfce7; }
        .plan-icon { display: inline-flex; align-items: center; justify-content: center; width: 3.4rem; height: 3.4rem; border-radius: 1rem; }
        .plan-icon svg { width: 1.65rem; height: 1.65rem; }
        .price { color: #111827; font-size: clamp(2.2rem, 5vw, 3.4rem); font-weight: 800; line-height: 1; letter-spacing: -.05em; }
        .currency { color: var(--teal); font-size: 1.2rem; vertical-align: top; margin-right: .15rem; }
        .check-list li { display: flex; align-items: flex-start; gap: .7rem; color: #4b5563; font-size: .9rem; line-height: 1.55; }
        .check-list li svg { width: 1.05rem; height: 1.05rem; flex: 0 0 auto; margin-top: .15rem; color: var(--teal); }
        .cross-list li { display: flex; align-items: flex-start; gap: .7rem; color: #6b7280; font-size: .88rem; line-height: 1.55; }
        .cross-list li svg { width: 1.05rem; height: 1.05rem; flex: 0 0 auto; margin-top: .15rem; color: #cbd5e1; }
        .faq-detail { border: 1px solid #e5e7eb; border-radius: .9rem; background: #ffffff; overflow: hidden; }
        .faq-detail summary { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.1rem 1.2rem; color: #1f2937; font-size: .92rem; font-weight: 800; cursor: pointer; list-style: none; }
        .faq-detail summary::-webkit-details-marker { display: none; }
        .faq-detail summary:hover { color: var(--teal); background: #f0fdfa; }
        .faq-detail[open] summary { color: var(--teal); }
        .faq-detail summary svg { width: 1.1rem; height: 1.1rem; flex: 0 0 auto; transition: transform .2s ease; }
        .faq-detail[open] summary svg { transform: rotate(45deg); }
        .faq-answer { padding: 0 1.2rem 1.15rem; color: #6b7280; font-size: .88rem; line-height: 1.65; }
        .nav-link { color: #4b5563; }
        .nav-link:hover { color: var(--teal); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

<header class="bg-white border-b border-gray-100 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto flex items-center gap-6 px-6 py-4">
        <a href="{{ route('planes.index') }}" class="flex items-center gap-2 flex-shrink-0">
            <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud IPS" class="w-9 h-9 object-contain">
            <div class="leading-tight">
                <p class="font-bold text-gray-900">Prisma<span style="color:var(--teal)">Salud</span> IPS</p>
                <p class="text-xs text-gray-400">Ibagué · Tolima</p>
            </div>
        </a>
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium ml-auto" aria-label="Navegación principal">
            <a href="{{ url('/') }}" class="nav-link">Inicio</a>
            <a href="{{ route('nosotros') }}" class="nav-link">Nosotros</a>
            <a href="{{ route('servicios.index') }}" class="nav-link">Servicios</a>
            <a href="{{ route('blog.index') }}" class="nav-link">Blog</a>
            <a href="{{ route('contacto.index') }}" class="nav-link">Contacto</a>
        </nav>
        <a href="{{ url('/login') }}" class="ml-auto md:ml-0 px-4 py-2 text-sm font-semibold text-white rounded-md" style="background:linear-gradient(120deg,var(--azul-claro),var(--teal))">Inicio sesión</a>
    </div>
</header>

<main>
    <section class="detalle-hero text-white">
        <div class="max-w-6xl mx-auto px-6 py-14 md:py-20">
            <a href="{{ route('planes.index') }}" class="inline-flex items-center gap-2 text-sm text-white/75 hover:text-white mb-7">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                Volver a todos los planes
            </a>
            <div class="grid lg:grid-cols-[1fr_auto] gap-10 items-end">
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <span class="plan-icon {{ $plan['clase_icono'] }}">
                            @if ($plan['icono'] === 'sparkles')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3ZM19 16l.8 2.2L22 19l-2.2.8L19 22l-.8-2.2L16 19l2.2-.8L19 16Z"/></svg>
                            @elseif ($plan['icono'] === 'users')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            @elseif ($plan['icono'] === 'chart')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5M4 19h16M8 16v-4M12 16V8M16 16v-7M20 16v-3"/></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m10.5 20.5 10-10a4.95 4.95 0 0 0-7-7l-10 10a4.95 4.95 0 0 0 7 7Z"/><path stroke-linecap="round" d="m8.5 8.5 7 7"/></svg>
                            @endif
                        </span>
                        <div>
                            <span class="inline-block rounded-full bg-white/15 px-3 py-1 text-xs font-extrabold uppercase tracking-wider">Plan detallado</span>
                            <p class="text-white/75 text-sm mt-2">{{ $plan['etiqueta'] }}</p>
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">{{ $plan['nombre'] }}</h1>
                    <p class="mt-5 text-white/85 text-base md:text-lg leading-relaxed max-w-3xl">{{ $plan['descripcion'] }}</p>
                </div>
                <div class="min-w-[220px] rounded-2xl bg-white/10 border border-white/20 p-6 backdrop-blur-sm">
                    @if ($plan['precio_total'] === 0)
                        <p class="text-4xl font-extrabold" style="color:#ccfbf1">Gratis</p>
                    @else
                        <p class="price text-white"><span class="currency" style="color:#99f6e4">$</span>{{ number_format($plan['precio_total'], 0, ',', '.') }}</p>
                    @endif
                    <p class="text-white/70 text-sm mt-2">{{ $plan['periodo'] }}</p>
                    <a href="{{ $plan['cta_url'] }}" class="mt-5 inline-flex items-center justify-center w-full px-4 py-3 text-sm font-extrabold rounded-lg bg-white text-teal-800 hover:bg-teal-50 transition">
                        {{ $plan['cta'] }} →
                    </a>
                </div>
            </div>
        </div>
    </section>

    @php
        $esFarmacia = in_array($plan['id'], ['un-anio-farmacia', 'dos-anios-farmacia'], true);
        $resumen = [
            'Vigencia' => $plan['periodo'],
            'Pacientes activos' => $plan['id'] === 'gratis' ? 'Hasta 15' : 'Sin límite',
            'Usuarios incluidos' => match ($plan['id']) {
                'gratis' => '3 usuarios',
                'un-anio', 'un-anio-farmacia' => '5 usuarios',
                default => '20 usuarios',
            },
            'Sedes' => match ($plan['id']) {
                'gratis' => '1 sede',
                'un-anio', 'un-anio-farmacia' => '2 sedes',
                default => '10 sedes',
            },
            'Fórmula' => $esFarmacia ? 'Directa a farmacia' : 'Correo del paciente',
        ];
        $pasos = match ($plan['id']) {
            'gratis' => [
                ['01', 'Crea tu cuenta', 'Activa la prueba gratuita durante 1 mes.'],
                ['02', 'Carga tu información', 'Registra hasta 15 pacientes activos y organiza tu agenda.'],
                ['03', 'Conoce el sistema', 'Revisa las módulos disponibles antes de elegir tu plan.'],
            ],
            'un-anio', 'dos-anios' => [
                ['01', 'El doctor prescribe', 'El doctor envía la fórmula al correo del paciente.'],
                ['02', 'El paciente recibe el correo', 'Consulta la fórmula y las indicaciones desde su correo.'],
                ['03', 'Reclama en farmacia', 'El paciente se acerca a la sede indicada para reclamarla.'],
            ],
            default => [
                ['01', 'El doctor prescribe', 'La fórmula se envía directamente a la farmacia.'],
                ['02', 'El farmaceuta recibe', 'La farmacia recibe la fórmula de inmediato y la deja preparada.'],
                ['03', 'El paciente recibe el aviso', 'Por correo conoce la sede, el horario y las indicaciones para reclamarla sin fila.'],
            ],
        };
    @endphp

    <section class="max-w-6xl mx-auto px-6 py-14 md:py-20">
        <div class="grid lg:grid-cols-[1.15fr_.85fr] gap-8 items-start">
            <div class="space-y-6">
                <section class="rounded-2xl border border-gray-200 bg-white p-6 md:p-8 shadow-sm">
                    <span class="inline-block rounded-full bg-teal-50 px-3 py-1 text-xs font-extrabold" style="color:var(--teal)">Todos los beneficios</span>
                    <h2 class="text-2xl font-extrabold text-gray-900 mt-4 mb-6">Qué incluye el plan {{ $plan['nombre'] }}</h2>
                    <ul class="check-list grid sm:grid-cols-2 gap-x-6 gap-y-4">
                        @foreach ($plan['beneficios'] as $beneficio)
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg>
                                <span>{{ $beneficio }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-white p-6 md:p-8 shadow-sm">
                    <span class="inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-extrabold text-gray-600">Alcance del plan</span>
                    <h2 class="text-2xl font-extrabold text-gray-900 mt-4 mb-5">Cómo funciona en la práctica</h2>
                    <div class="grid gap-4">
                        @foreach ($pasos as $paso)
                            <div class="flex gap-4 rounded-xl bg-gray-50 p-4">
                                <span class="flex items-center justify-center flex-shrink-0 w-9 h-9 rounded-lg text-sm font-extrabold" style="color:var(--teal);background:#ccfbf1">{{ $paso[0] }}</span>
                                <div>
                                    <h3 class="font-extrabold text-gray-800 text-sm">{{ $paso[1] }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $paso[2] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-white p-6 md:p-8 shadow-sm">
                    <span class="inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-extrabold text-gray-600">Lo que no incluye</span>
                    <h2 class="text-2xl font-extrabold text-gray-900 mt-4 mb-5">Fuera de este plan</h2>
                    <ul class="cross-list space-y-3">
                        @foreach ($plan['no_incluye'] as $limitacion)
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 12h12"/></svg>
                                <span>{{ $limitacion }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            </div>

            <aside class="space-y-6 lg:sticky lg:top-24">
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-extrabold text-gray-900">Resumen del plan</h2>
                    <dl class="mt-5 divide-y divide-gray-100">
                        @foreach ($resumen as $clave => $valor)
                            <div class="flex items-center justify-between gap-4 py-3 text-sm">
                                <dt class="text-gray-500">{{ $clave }}</dt>
                                <dd class="font-bold text-gray-800 text-right">{{ $valor }}</dd>
                            </div>
                        @endforeach
                    </dl>
                    <a href="{{ $plan['cta_url'] }}" class="mt-5 inline-flex items-center justify-center w-full px-4 py-3 text-sm font-extrabold text-white rounded-lg hover:opacity-90 transition" style="background:var(--teal)">
                        {{ $plan['cta'] }} →
                    </a>
                </section>

                <section class="rounded-2xl border border-teal-100 bg-teal-50/60 p-6">
                    <h2 class="font-extrabold text-gray-900">¿Tienes dudas?</h2>
                    <p class="text-sm text-gray-600 mt-2">Un asesor puede explicarte la vigencia, los límites y el flujo de tu EPS.</p>
                    <a href="{{ route('contacto.index') }}?plan={{ $plan['id'] }}" class="mt-4 inline-flex items-center font-extrabold text-sm" style="color:var(--teal)">Hablar con un asesor →</a>
                </section>
            </aside>
        </div>
    </section>

    <section class="bg-white border-y border-gray-100">
        <div class="max-w-4xl mx-auto px-6 py-14 md:py-20">
            <div class="text-center mb-9">
                <span class="inline-block text-xs font-extrabold uppercase tracking-[.18em]" style="color:var(--teal)">Preguntas frecuentes</span>
                <h2 class="text-3xl font-extrabold text-gray-900 mt-3">Resolvemos tus dudas sobre este plan</h2>
            </div>
            <div class="space-y-3">
                @foreach ($faqs as $indice => $faq)
                    <details class="faq-detail" @if ($indice === 0) open @endif>
                        <summary>
                            <span>{{ $faq['pregunta'] }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                        </summary>
                        <div class="faq-answer">{{ $faq['respuesta'] }}</div>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-14">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold" style="color:var(--teal)">¿Quieres comparar otra opción?</p>
                <h2 class="text-2xl font-extrabold text-gray-900 mt-1">Conoce los demás planes</h2>
            </div>
            <a href="{{ route('planes.index') }}" class="inline-flex items-center justify-center px-5 py-3 text-sm font-extrabold text-white rounded-lg hover:opacity-90 transition" style="background:var(--teal)">Ver todos los planes →</a>
        </div>
    </section>
</main>

<footer class="text-white/80" style="background:#1A5C3A">
    <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col sm:flex-row items-center justify-between gap-5 text-center sm:text-left">
        <div>
            <p class="text-white font-bold">Prisma<span class="text-teal-300">Salud</span> IPS</p>
            <p class="text-sm mt-2">Software y soluciones para una operación de salud más ordenada.</p>
        </div>
        <div class="flex flex-wrap justify-center gap-5 text-sm">
            <a href="{{ route('contacto.index') }}" class="hover:text-white">Contacto</a>
            <a href="{{ route('terminos.index') }}" class="hover:text-white">Términos</a>
        </div>
    </div>
</footer>

@include('partials.cookies')

</body>
</html>