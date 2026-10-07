<!-- resources/views/superadmin/login.blade.php - Login exclusivo del Superadministrador -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Acceso Superadministrador - PrismaSalud</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .fondo { background: linear-gradient(135deg, #1B4D6B 0%, #0C7B93 55%, #12A594 100%); }
    </style>
</head>
<body class="fondo min-h-screen flex items-center justify-center px-4">

    <main class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl p-8">

            <div class="text-center mb-6">
                <img src="{{ asset('img/logo.png') }}" alt="PrismaSalud" class="h-14 mx-auto mb-3">
                <h1 class="text-xl font-extrabold text-gray-900">Panel Superadministrador</h1>
                <p class="text-sm text-gray-500 mt-1">Acceso exclusivo para la administración de PrismaSalud</p>
            </div>

            @if (session('error'))
                <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('exito'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3">
                    {{ session('exito') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form id="form-login" method="POST" action="{{ route('superadmin.login.post') }}" class="space-y-4" novalidate>
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-gray-700 mb-1">Correo</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autofocus
                           autocomplete="username" aria-describedby="error-email"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <p id="error-email" class="text-xs text-red-600 mt-1 hidden" role="alert"></p>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-gray-700 mb-1">Contraseña</label>
                    <div class="relative">
                        <input id="password" name="password" type="password"
                               autocomplete="current-password" aria-describedby="error-password"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <p id="error-password" class="text-xs text-red-600 mt-1 hidden" role="alert"></p>
                </div>

                <button type="submit" id="btn-login"
                        class="w-full rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm py-3 transition disabled:opacity-60">
                    Iniciar sesión
                </button>

                <p class="text-center text-xs">
                    <a href="{{ route('superadmin.recuperar') }}" class="font-bold text-teal-700 hover:text-teal-900">¿Olvidaste tu contraseña?</a>
                </p>
            </form>
        </div>

        <p class="text-center text-xs text-white/70 mt-6">© PrismaSalud · Uso interno</p>
    </main>

    <script>
        // Toda la validación del navegador se hace aquí, con JavaScript.
        // (El servidor vuelve a validar lo mismo: esta parte es para dar respuesta rápida al usuario.)
        (function () {
            const REGLAS = {
                correoMax: 150,
                passwordMin: 8,
                passwordMax: 100,
                patronCorreo: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/,
            };

            const form = document.getElementById('form-login');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const errEmail = document.getElementById('error-email');
            const errPassword = document.getElementById('error-password');
            const btn = document.getElementById('btn-login');
            const tocado = { email: false, password: false };

            function mostrar(campo, caja, mensaje) {
                caja.textContent = mensaje;
                caja.classList.toggle('hidden', mensaje === '');
                campo.classList.toggle('border-red-500', mensaje !== '');
                campo.setAttribute('aria-invalid', mensaje !== '' ? 'true' : 'false');
            }

            function validarCorreo() {
                const v = email.value.trim();
                let m = '';
                if (v === '') m = 'Ingresa tu correo.';
                else if (v.length > REGLAS.correoMax) m = 'El correo no puede tener más de ' + REGLAS.correoMax + ' caracteres.';
                else if (!REGLAS.patronCorreo.test(v)) m = 'Ingresa un correo válido, por ejemplo nombre@dominio.com.';
                mostrar(email, errEmail, m);
                return m === '';
            }

            function validarPassword() {
                const v = password.value;
                let m = '';
                if (v === '') m = 'Ingresa tu contraseña.';
                else if (v.length < REGLAS.passwordMin) m = 'La contraseña debe tener mínimo ' + REGLAS.passwordMin + ' caracteres.';
                else if (v.length > REGLAS.passwordMax) m = 'La contraseña no puede tener más de ' + REGLAS.passwordMax + ' caracteres.';
                mostrar(password, errPassword, m);
                return m === '';
            }

            // Valida al salir del campo y, una vez tocado, mientras se escribe.
            email.addEventListener('blur', function () { tocado.email = true; validarCorreo(); });
            email.addEventListener('input', function () { if (tocado.email) validarCorreo(); });
            password.addEventListener('blur', function () { tocado.password = true; validarPassword(); });
            password.addEventListener('input', function () { if (tocado.password) validarPassword(); });

            form.addEventListener('submit', function (e) {
                tocado.email = tocado.password = true;
                const correoOk = validarCorreo();
                const passwordOk = validarPassword();
                if (!correoOk || !passwordOk) {
                    e.preventDefault();
                    (correoOk ? password : email).focus();
                    return;
                }
                btn.disabled = true; // evita enviar el formulario dos veces
                btn.textContent = 'Ingresando...';
            });
        })();
    </script>

</body>
</html>