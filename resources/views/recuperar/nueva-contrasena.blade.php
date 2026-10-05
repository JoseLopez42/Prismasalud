{{-- Tercer paso de la recuperación: solo la nueva contraseña. Esta vista
     únicamente se muestra cuando el código del paso anterior ya fue
     validado (el usuario está guardado en la sesión). --}}
@extends('partials.auth')

@section('titulo', 'Nueva contraseña - PrismaSalud IPS')
@section('card_titulo', 'Crea tu nueva contraseña')
@section('card_descripcion', 'Elige una contraseña nueva para acceder a tu cuenta.')

@section('formulario')

    @php
        // Los errores de los dos campos de contraseña se muestran debajo
        // de cada input, así que aquí solo se listan los generales.
        $erroresGenerales = collect($errors->all())->reject(fn ($m) => str_contains($m, 'contraseña'));
    @endphp

    @if ($erroresGenerales->isNotEmpty())
        <div class="flex items-start gap-2 bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-5">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $erroresGenerales->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('recuperar.nueva') }}" class="space-y-5" id="form-nueva-contrasena" novalidate>
        @csrf

        <div>
            <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-1.5">Nueva contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    @include('admin.partials.icono', ['icono' => 'candado', 'clase' => 'w-5 h-5'])
                </div>
                <input type="password" name="contrasena" id="contrasena" autocomplete="new-password"
                       placeholder="Mínimo 8 caracteres"
                       class="w-full border border-gray-300 rounded-lg pl-10 pr-10 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
                       required autofocus>
                <button type="button" onclick="toggleVerContrasena('contrasena', this)"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-teal-600 transition"
                        aria-label="Ver contraseña">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <p class="text-xs text-red-600 mt-1.5 hidden" id="error-contrasena"></p>
        </div>

        <div>
            <label for="contrasena_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar nueva contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    @include('admin.partials.icono', ['icono' => 'candado', 'clase' => 'w-5 h-5'])
                </div>
                <input type="password" name="contrasena_confirmation" id="contrasena_confirmation" autocomplete="new-password"
                       placeholder="Repite tu nueva contraseña"
                       class="w-full border border-gray-300 rounded-lg pl-10 pr-10 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
                       required>
                <button type="button" onclick="toggleVerContrasena('contrasena_confirmation', this)"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-teal-600 transition"
                        aria-label="Ver contraseña">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <p class="text-xs text-red-600 mt-1.5 hidden" id="error-confirmacion"></p>
        </div>

        {{-- Checklist de requisitos: se marca en verde a medida que el usuario escribe --}}
        <ul class="space-y-1.5 bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
            <li class="flex items-center gap-2 text-xs text-gray-500" data-requerimiento="longitud">
                <span class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center flex-shrink-0"></span>
                Mínimo 8 caracteres
            </li>
            <li class="flex items-center gap-2 text-xs text-gray-500" data-requerimiento="mayuscula">
                <span class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center flex-shrink-0"></span>
                Al menos una letra mayúscula
            </li>
            <li class="flex items-center gap-2 text-xs text-gray-500" data-requerimiento="minuscula">
                <span class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center flex-shrink-0"></span>
                Al menos una letra minúscula
            </li>
            <li class="flex items-center gap-2 text-xs text-gray-500" data-requerimiento="numero">
                <span class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center flex-shrink-0"></span>
                Al menos un número
            </li>
            <li class="flex items-center gap-2 text-xs text-gray-500" data-requerimiento="simbolo">
                <span class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center flex-shrink-0"></span>
                Al menos un signo especial (! @ # $ % &amp; * ? ...)
            </li>
        </ul>

        <button type="submit"
                class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold py-2.5 rounded-lg hover:from-green-700 hover:to-teal-700 hover:shadow-lg transition active:scale-[.99]">
            Guardar nueva contraseña
        </button>
    </form>

    <div class="mt-6 pt-5 border-t border-gray-100 text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-teal-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver a iniciar sesión
        </a>
    </div>

@endsection

@push('scripts')
<script>
    (function () {
        const formulario   = document.getElementById('form-nueva-contrasena');
        const contrasena   = document.getElementById('contrasena');
        const confirmacion = document.getElementById('contrasena_confirmation');
        const errorContra  = document.getElementById('error-contrasena');
        const errorConfir  = document.getElementById('error-confirmacion');

        // Mismas reglas que valida el backend (Password::min(8)->mixedCase()
        // ->numbers()->symbols()), replicadas aquí para dar feedback inmediato.
        const REQUISITOS = {
            longitud:  (v) => v.length >= 8,
            mayuscula: (v) => /\p{Lu}/u.test(v),
            minuscula: (v) => /\p{Ll}/u.test(v),
            numero:    (v) => /\p{N}/u.test(v),
            simbolo:   (v) => /[\p{Z}\p{S}\p{P}]/u.test(v),
        };

        function requisitosFaltantes(valor) {
            return Object.keys(REQUISITOS).filter((clave) => !REQUISITOS[clave](valor));
        }

        function pintarChecklist(valor) {
            document.querySelectorAll('[data-requerimiento]').forEach((li) => {
                const cumplido = REQUISITOS[li.dataset.requerimiento](valor);
                const circulo  = li.querySelector('span');

                circulo.classList.toggle('bg-teal-600', cumplido);
                circulo.classList.toggle('border-teal-600', cumplido);
                circulo.classList.toggle('border-gray-300', !cumplido);
                circulo.innerHTML = cumplido
                    ? '<svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
                    : '';

                li.classList.toggle('text-gray-500', !cumplido);
                li.classList.toggle('text-teal-700', cumplido);
            });
        }

        function mostrarError(elemento, mensaje) {
            elemento.textContent = mensaje || '';
            elemento.classList.toggle('hidden', !mensaje);
        }

        function validarContrasena() {
            const valor = contrasena.value;
            pintarChecklist(valor);

            if (!valor) {
                mostrarError(errorContra, '');
                return false;
            }

            const faltan = requisitosFaltantes(valor);

            if (faltan.length) {
                mostrarError(errorContra, 'Completa todos los requisitos de la contraseña.');
                return false;
            }

            mostrarError(errorContra, '');
            return true;
        }

        function validarConfirmacion() {
            if (!confirmacion.value) {
                mostrarError(errorConfir, '');
                return true; // aún no la escribió
            }

            const coinciden = confirmacion.value === contrasena.value;

            mostrarError(errorConfir, coinciden ? '' : 'Las contraseñas no coinciden.');
            return coinciden;
        }

        contrasena.addEventListener('input', function () {
            validarContrasena();
            if (confirmacion.value) {
                validarConfirmacion();
            }
        });

        confirmacion.addEventListener('input', validarConfirmacion);

        formulario.addEventListener('submit', function (evento) {
            const ok = validarContrasena() && validarConfirmacion();

            if (!ok) {
                evento.preventDefault();
                contrasena.focus();
            }
        });

        pintarChecklist('');
    })();

    function toggleVerContrasena(idInput, boton) {
        const campo = document.getElementById(idInput);
        const ver   = campo.type === 'password';
        campo.type = ver ? 'text' : 'password';
        boton.innerHTML = ver
            ? '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
    }
</script>
@endpush
