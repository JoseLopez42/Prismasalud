{{--
    =====================================================================
    VISTA: recuperar/solicitar.blade.php
    Primer paso de la recuperación de contraseña: el usuario escribe
    su correo y se le envía un código de 6 dígitos (si el correo
    existe en la tabla usuarios).
    =====================================================================
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - PrismaSalud IPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradiente-salud {
            background: linear-gradient(120deg, #14532d 0%, #0f766e 60%, #0d9488 100%);
        }
    </style>
</head>
<body class="gradiente-salud min-h-screen flex items-center justify-center px-4">

    <div class="bg-white rounded-xl shadow-lg w-full max-w-sm p-8">

        <div class="flex items-center gap-2 justify-center mb-6">
            <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud IPS" class="w-9 h-9 object-contain">
            <p class="font-bold text-gray-900">
                Prisma<span class="text-teal-600">Salud</span> IPS
            </p>
        </div>

        <h1 class="text-xl font-bold text-gray-900 text-center mb-1">Recuperar contraseña</h1>
        <p class="text-sm text-gray-500 text-center mb-6">
            Ingresa tu correo y te enviaremos un código para restablecerla.
        </p>

        {{-- Mensaje de éxito enviado desde el controlador con ->with('exito', ...) --}}
        @if (session('exito'))
            <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">
                {{ session('exito') }}
            </div>
        @endif

        {{-- Errores de validación (ej: correo con formato inválido) --}}
        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('recuperar.solicitar') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                <input type="email" name="correo" value="{{ old('correo') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                       required autofocus>
            </div>

            <button type="submit"
                    class="w-full bg-green-600 text-white font-semibold py-2 rounded-md hover:bg-green-700 transition">
                Enviar código
            </button>
        </form>

        <a href="{{ route('login') }}" class="block text-center text-sm text-teal-600 hover:underline mt-6">
            ← Volver a iniciar sesión
        </a>
    </div>

</body>
</html>