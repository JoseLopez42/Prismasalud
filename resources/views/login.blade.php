{{--
    =====================================================================
    VISTA: login.blade.php
    Formulario de inicio de sesión. Pide cédula y contraseña, los
    envía por POST a LoginController@iniciarSesion.
    Muestra el mensaje de error si las credenciales son incorrectas,
    o si el middleware de UsuarioController redirigió aquí por falta
    de permisos.
    =====================================================================
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - PrismaSalud IPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Mismo degradado usado en el hero del index, para mantener
           la identidad visual de PrismaSalud en toda la app. */
        .gradiente-salud {
            background: linear-gradient(120deg, #14532d 0%, #0f766e 60%, #0d9488 100%);
        }
    </style>
</head>
<body class="gradiente-salud min-h-screen flex items-center justify-center px-4">

    <div class="bg-white rounded-xl shadow-lg w-full max-w-sm p-8">

        {{-- Logo / nombre, igual que en el header del index --}}
        <div class="flex items-center gap-2 justify-center mb-6">
            <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud IPS" class="w-9 h-9 object-contain">
            <p class="font-bold text-gray-900">
                Prisma<span class="text-teal-600">Salud</span> IPS
            </p>
        </div>

        <h1 class="text-xl font-bold text-gray-900 text-center mb-1">Iniciar sesión</h1>
        <p class="text-sm text-gray-500 text-center mb-6">Ingresa con tu cédula y contraseña</p>

        {{-- Mensaje de error: viene de withErrors(['login' => ...])
             en el controlador (cédula/contraseña incorrectas, o sin
             permiso para entrar a una sección protegida). --}}
        @if ($errors->has('login'))
            <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-4">
                {{ $errors->first('login') }}
            </div>
        @endif

        {{-- Mensaje de error enviado con ->with('error', ...) desde
             el middleware de UsuarioController --}}
        @if (session('error'))
            <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf {{-- Token de seguridad obligatorio en todo formulario POST de Laravel --}}

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                <input type="number" name="cedula" value="{{ old('cedula') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                       required autofocus>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="contrasena"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                       required>
            </div>

            <a href="{{ route('recuperar.solicitar.form') }}" class="block text-center text-sm text-teal-600 hover:underline mt-3">
                ¿Olvidaste tu contraseña?
            </a>

            <button type="submit"
                    class="w-full bg-green-600 text-white font-semibold py-2 rounded-md hover:bg-green-700 transition">
                Iniciar sesión
            </button>
        </form>

        <a href="{{ url('/') }}" class="block text-center text-sm text-teal-600 hover:underline mt-6">
            ← Volver al inicio
        </a>
    </div>

</body>
</html>