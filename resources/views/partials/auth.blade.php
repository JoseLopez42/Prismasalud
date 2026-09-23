{{-- Layout compartido por las páginas de autenticación (login y
     recuperación de contraseña). Tarjeta central a dos columnas:
     panel de marca (solo escritorio) + formulario. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'PrismaSalud IPS')</title>
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
        /* Degradado institucional PrismaSalud (azul -> teal -> naranja) */
        .gradiente-salud {
            background: linear-gradient(135deg, var(--azul) 0%, var(--teal) 55%, var(--naranja) 100%);
        }
        .panel-marca {
            background: linear-gradient(160deg, var(--azul) 0%, var(--naranja) 70%, var(--teal) 100%);
        }
        input[type="checkbox"]:focus {
            outline: none;
        }
    </style>
    @stack('estilos')
</head>
<body class="gradiente-salud min-h-screen flex items-center justify-center px-4 py-8">

    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-4xl grid lg:grid-cols-2">

        {{-- Panel de marca: invisible en móvil, se ve a la izquierda en escritorio --}}
        <div class="panel-marca text-white hidden lg:flex flex-col justify-between p-10">

            <div class="flex items-center gap-2">
                <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud IPS" class="w-10 h-10 object-contain">
                <p class="font-bold text-lg">
                    Prisma<span class="text-teal-300">Salud</span> IPS
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold mb-3">Cuidamos tu salud, cerca de ti</h2>
                <p class="text-white/70 text-sm mb-6">
                    Accede a todos los servicios de nuestra IPS desde un solo lugar.
                </p>

                <ul class="space-y-3 text-sm text-white/85">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-teal-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Agenda tus citas médicas en línea
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-teal-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Acceso seguro a tu historia clínica
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-teal-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Consulta tus medicamentos y entregas
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-teal-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Atención en múltiples sedes
                    </li>
                </ul>
            </div>

            <p class="text-xs text-white/50">© {{ date('Y') }} PrismaSalud IPS · Todos los derechos reservados</p>
        </div>

        {{-- Lado del formulario --}}
        <div class="p-8 md:p-12">

            {{-- Logo/marca solo en móvil --}}
            <div class="flex items-center justify-center gap-2 mb-8 lg:hidden">
                <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud IPS" class="w-9 h-9 object-contain">
                <p class="font-bold text-gray-900">
                    Prisma<span class="text-teal-600">Salud</span> IPS
                </p>
            </div>

            <h1 class="text-2xl font-extrabold text-gray-900 mb-1">@yield('card_titulo')</h1>
            <p class="text-sm text-gray-500 mb-8">@yield('card_descripcion')</p>

            @yield('formulario')
        </div>
    </div>

    @stack('scripts')

    @include('partials.cookies')
</body>
</html>