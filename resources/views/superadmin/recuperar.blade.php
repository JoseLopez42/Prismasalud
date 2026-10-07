<!-- resources/views/superadmin/recuperar.blade.php - Recuperar contraseña del Superadministrador (3 pasos) -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Recuperar contraseña - PrismaSalud</title>

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
                <h1 class="text-xl font-extrabold text-gray-900">Recuperar contraseña</h1>
                <p class="text-xs font-semibold text-teal-700 mt-1">Paso {{ $paso }} de 3</p>
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

            {{-- ================= PASO 1: CORREO ================= --}}
            @if ($paso === 1)
                <p class="text-sm text-gray-500 mb-4">Escribe el correo del Superadministrador. Te enviaremos un código de 6 números.</p>

                <form id="form-recuperar" method="POST" action="{{ route('superadmin.recuperar.enviar') }}" class="space-y-4" novalidate>
                    @csrf
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 mb-1">Correo</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autofocus autocomplete="username"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <p id="error-email" class="text-xs text-red-600 mt-1 hidden" role="alert"></p>
                    </div>
                    <button type="submit" id="btn-enviar"
                            class="w-full rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm py-3 transition disabled:opacity-60">
                        Enviar código
                    </button>
                </form>
            @endif

            {{-- ================= PASO 2: CÓDIGO ================= --}}
            @if ($paso === 2)
                <p class="text-sm text-gray-500 mb-4">Escribe el código de 6 números que enviamos a <strong class="text-gray-800">{{ $correoOculto }}</strong>. Vale 10 minutos.</p>

                <form id="form-codigo" method="POST" action="{{ route('superadmin.recuperar.codigo.verificar') }}" class="space-y-4" novalidate>
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
                <p class="text-center text-xs text-gray-500 mt-4">
                    ¿No te llegó? <a href="{{ route('superadmin.recuperar') }}" class="font-bold text-teal-700 hover:text-teal-900">Pedir otro código</a>
                </p>
            @endif

            {{-- ================= PASO 3: CONTRASEÑA NUEVA ================= --}}
            @if ($paso === 3)
                <p class="text-sm text-gray-500 mb-4">Código correcto. Escribe tu contraseña nueva.</p>

                <form id="form-nueva" method="POST" action="{{ route('superadmin.recuperar.nueva.guardar') }}" class="space-y-4" novalidate>
                    @csrf
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-700 mb-1">Contraseña nueva</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" autofocus autocomplete="new-password"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 pr-16 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <button type="button" id="ver-password"
                                    class="absolute inset-y-0 right-0 px-3 text-xs font-bold text-teal-700 hover:text-teal-900">Ver</button>
                        </div>
                        <p id="error-password" class="text-xs text-red-600 mt-1 hidden" role="alert"></p>

                        <ul class="mt-2 space-y-0.5 text-xs text-gray-400" id="requisitos">
                            <li data-req="largo">• Mínimo 8 caracteres</li>
                            <li data-req="mayuscula">• Una letra mayúscula</li>
                            <li data-req="minuscula">• Una letra minúscula</li>
                            <li data-req="numero">• Un número</li>
                            <li data-req="especial">• Un carácter especial (* # ! $ ...)</li>
                        </ul>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-1">Repite la contraseña</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <p id="error-confirmar" class="text-xs text-red-600 mt-1 hidden" role="alert"></p>
                    </div>

                    <button type="submit" id="btn-nueva"
                            class="w-full rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm py-3 transition disabled:opacity-60">
                        Guardar contraseña
                    </button>
                </form>
            @endif

            <p class="text-center text-xs text-gray-500 mt-5">
                <a href="{{ route('superadmin.login') }}" class="font-bold text-teal-700 hover:text-teal-900">← Volver al inicio de sesión</a>
            </p>
        </div>

        <p class="text-center text-xs text-white/70 mt-6">© PrismaSalud · Uso interno</p>
    </main>

    <script>
        // Validación del navegador (JavaScript). El servidor vuelve a validar lo mismo.
        (function () {
            function mostrar(campo, caja, mensaje) {
                caja.textContent = mensaje;
                caja.classList.toggle('hidden', mensaje === '');
                campo.classList.toggle('border-red-500', mensaje !== '');
            }

            // ---------- Paso 1 ----------
            var f1 = document.getElementById('form-recuperar');
            if (f1) {
                var email = document.getElementById('email');
                var errEmail = document.getElementById('error-email');
                var patronCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
                var tocadoCorreo = false;

                function validarCorreo() {
                    var v = email.value.trim(), m = '';
                    if (v === '') m = 'Ingresa tu correo.';
                    else if (v.length > 150) m = 'El correo no puede tener más de 150 caracteres.';
                    else if (!patronCorreo.test(v)) m = 'Ingresa un correo válido, por ejemplo nombre@dominio.com.';
                    mostrar(email, errEmail, m);
                    return m === '';
                }
                email.addEventListener('blur', function () { tocadoCorreo = true; validarCorreo(); });
                email.addEventListener('input', function () { if (tocadoCorreo) validarCorreo(); });
                f1.addEventListener('submit', function (e) {
                    tocadoCorreo = true;
                    if (!validarCorreo()) { e.preventDefault(); email.focus(); return; }
                    var b = document.getElementById('btn-enviar'); b.disabled = true; b.textContent = 'Enviando...';
                });
            }

            // ---------- Paso 2 ----------
            var f2 = document.getElementById('form-codigo');
            if (f2) {
                var codigo = document.getElementById('codigo');
                var errCodigo = document.getElementById('error-codigo');

                // Solo deja escribir números
                codigo.addEventListener('input', function () {
                    codigo.value = codigo.value.replace(/\D/g, '').slice(0, 6);
                    if (errCodigo.textContent !== '') validarCodigo();
                });
                function validarCodigo() {
                    var v = codigo.value, m = '';
                    if (v === '') m = 'Ingresa el código que te llegó al correo.';
                    else if (!/^[0-9]{6}$/.test(v)) m = 'El código tiene 6 números.';
                    mostrar(codigo, errCodigo, m);
                    return m === '';
                }
                f2.addEventListener('submit', function (e) {
                    if (!validarCodigo()) { e.preventDefault(); codigo.focus(); return; }
                    var b = document.getElementById('btn-codigo'); b.disabled = true; b.textContent = 'Verificando...';
                });
            }

            // ---------- Paso 3 ----------
            var f3 = document.getElementById('form-nueva');
            if (f3) {
                var pass = document.getElementById('password');
                var conf = document.getElementById('password_confirmation');
                var errPass = document.getElementById('error-password');
                var errConf = document.getElementById('error-confirmar');
                var items = document.querySelectorAll('#requisitos li');
                var tocadoPass = false, tocadoConf = false;

                function cumple(v) {
                    return {
                        largo: v.length >= 8,
                        mayuscula: /[A-Z]/.test(v),
                        minuscula: /[a-z]/.test(v),
                        numero: /[0-9]/.test(v),
                        especial: /[^A-Za-z0-9]/.test(v)
                    };
                }
                function pintarRequisitos() {
                    var r = cumple(pass.value);
                    items.forEach(function (li) {
                        var ok = r[li.getAttribute('data-req')];
                        li.classList.toggle('text-green-600', ok);
                        li.classList.toggle('font-semibold', ok);
                        li.classList.toggle('text-gray-400', !ok);
                    });
                }
                function validarPass() {
                    var v = pass.value, r = cumple(v), m = '';
                    if (v === '') m = 'Ingresa la contraseña nueva.';
                    else if (v.length > 100) m = 'La contraseña no puede tener más de 100 caracteres.';
                    else if (!(r.largo && r.mayuscula && r.minuscula && r.numero && r.especial))
                        m = 'Falta cumplir alguno de los requisitos de la lista.';
                    mostrar(pass, errPass, m);
                    return m === '';
                }
                function validarConf() {
                    var m = '';
                    if (conf.value === '') m = 'Repite la contraseña.';
                    else if (conf.value !== pass.value) m = 'Las dos contraseñas no coinciden.';
                    mostrar(conf, errConf, m);
                    return m === '';
                }

                pass.addEventListener('input', function () {
                    pintarRequisitos();
                    if (tocadoPass) validarPass();
                    if (tocadoConf) validarConf();
                });
                pass.addEventListener('blur', function () { tocadoPass = true; validarPass(); });
                conf.addEventListener('input', function () { if (tocadoConf) validarConf(); });
                conf.addEventListener('blur', function () { tocadoConf = true; validarConf(); });

                f3.addEventListener('submit', function (e) {
                    tocadoPass = tocadoConf = true;
                    var a = validarPass(), b = validarConf();
                    if (!a || !b) { e.preventDefault(); (a ? conf : pass).focus(); return; }
                    var btn = document.getElementById('btn-nueva'); btn.disabled = true; btn.textContent = 'Guardando...';
                });

                document.getElementById('ver-password').addEventListener('click', function () {
                    var oculto = pass.type === 'password';
                    pass.type = oculto ? 'text' : 'password';
                    conf.type = pass.type;
                    this.textContent = oculto ? 'Ocultar' : 'Ver';
                });
            }
        })();
    </script>

</body>
</html>