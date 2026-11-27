<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demasiados intentos - PrismaSalud IPS</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-8 text-center">
        <div class="mx-auto mb-4 w-16 h-16 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.93 5h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.27 17a2 2 0 001.74 3z" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-900">Demasiados intentos</h2>

        <p class="mt-3 text-sm text-gray-600">Por seguridad bloqueamos temporalmente el acceso. Espera un minuto e inténtalo de nuevo.</p>

        <a href="{{ route('login') }}"
           class="mt-6 w-full inline-flex items-center justify-center px-6 py-2.5 bg-[#1A5C3A] text-white text-sm font-semibold rounded-lg hover:bg-[#14472d] transition">
            Ir al inicio de sesión
        </a>
    </div>
</body>
</html>
