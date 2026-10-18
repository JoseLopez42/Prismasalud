<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago aceptado - PrismaSalud IPS</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        #modal-pago { opacity: 0; visibility: hidden; transition: opacity .2s ease, visibility .2s ease; }
        #modal-pago.abierto { opacity: 1; visibility: visible; }
        #modal-pago .tarjeta { transform: scale(.95) translateY(8px); transition: transform .2s ease; }
        #modal-pago.abierto .tarjeta { transform: scale(1) translateY(0); }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
    <div id="modal-pago" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="tarjeta bg-white rounded-2xl shadow-xl max-w-md w-full p-8 text-center">
            <div class="mx-auto mb-4 w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Pago aceptado</h2>
            <p class="mt-3 text-sm text-gray-600">
                Tu pago fue recibido correctamente. Tu cuenta sigue <strong>inactiva</strong>
                hasta que el superadministrador apruebe tu licencia; por ahora no puedes
                realizar acciones en el panel.
            </p>
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center justify-center gap-2 mt-6 px-6 py-2.5 bg-[#1A5C3A] text-white text-sm font-semibold rounded-lg hover:bg-[#14472d] transition">
                Continuar
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                document.getElementById('modal-pago').classList.add('abierto');
            }, 100);
        });
    </script>
</body>
</html>