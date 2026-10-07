<!-- resources/views/superadmin/login-codigo.blade.php - Código de verificación para entrar al panel -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Verificación - PrismaSalud</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .fondo { background: linear-gradient(135deg, #1B4D6B 0%, #0C7B93 55%, #12A594 100%); }
    </style>
</head>
<body class="fondo min-h-screen flex items-center justify-center px-4 py-8">

    <main class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl p-8">

            <div class="text-center mb-6">
                <img src="{{ asset('img/logo.png') }}" alt="PrismaSalud" class="h-14 mx-auto mb-3">
                <h1 class="text-xl font-extrabold text-gray-900">Código de verificación</h1>
                <p class="text-xs font-semibold text-teal-700 mt-1">Seguridad adicional</p>
            </div>

            @if (session('info'))
                <div class="mb-4 rounded-lg bg-teal-50 border border-teal-200 text-teal-800 text-sm px-4 py-3">
                    {{ session('info') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <p class="text-sm text-gray-500 mb-4">Escribe el código de 6 números que enviamos a <strong class="text-gray-800">{{ $correoOculto }}</strong>. Vale 10 minutos.</p>

            <form id="form-codigo" method="POST" action="{{ route('superadmin.login.codigo.verificar') }}" class="space-y-4" novalidate>
                @csrf
                <div>
                    <label for="codigo" class="block text-xs font-bold text-gray-700 mb-1">Código</label>
                    <input id="codigo" name="codigo" type="text" inputmode="numeric" maxlength="6" autofocus autocomplete="one-time-code"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-center text-xl tracking-[0.5em] font-bold focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <p id="error-codigo" class="text-xs text-red-600 mt-1 hidden" role="alert"></p>
                </div>
                <button type="submit" id="btn-codigo"
                        class="w-full rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm py-3 transition disabled:opacity-60">
                    Verificar código
                </button>
            </form>

            <form method="POST" action="{{ route('superadmin.login.codigo.reenviar') }}" class="text-center mt-4">
                @csrf
                <p class="text-xs text-gray-500">
                    ¿No te llegó?
                    <button type="submit" class="font-bold text-teal-700 hover:text-teal-900">Reenviar código</button>
                </p>
            </form>

            <p class="text-center text-xs text-gray-500 mt-5">
                <a href="{{ route('superadmin.login') }}" class="font-bold text-teal-700 hover:text-teal-900">← Volver al inicio de sesión</a>
            </p>
        </div>

        <p class="text-center text-xs text-white/70 mt-6">© PrismaSalud · Uso interno</p>
    </main>

    <script>
        (function () {
            var f2 = document.getElementById('form-codigo');
            var codigo = document.getElementById('codigo');
            var errCodigo = document.getElementById('error-codigo');

            codigo.addEventListener('input', function () {
                codigo.value = codigo.value.replace(/\D/g, '').slice(0, 6);
                if (errCodigo.textContent !== '') validarCodigo();
            });
            function validarCodigo() {
                var v = codigo.value, m = '';
                if (v === '') m = 'Ingresa el código que te llegó al correo.';
                else if (!/^[0-9]{6}$/.test(v)) m = 'El código tiene 6 números.';
                errCodigo.textContent = m;
                errCodigo.classList.toggle('hidden', m === '');
                codigo.classList.toggle('border-red-500', m !== '');
                return m === '';
            }
            f2.addEventListener('submit', function (e) {
                if (!validarCodigo()) { e.preventDefault(); codigo.focus(); return; }
                var b = document.getElementById('btn-codigo'); b.disabled = true; b.textContent = 'Verificando...';
            });
        })();
    </script>

</body>
</html>
