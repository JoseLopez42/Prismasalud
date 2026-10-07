@extends('superadmin.layout')
@section('titulo', 'Mi cuenta')

@php
    $input = 'w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500';
    $borde = fn ($campo) => $errors->has($campo) ? 'border-red-500' : 'border-gray-300';
@endphp

@section('contenido')
    <h1 class="text-2xl font-extrabold text-gray-900">Mi cuenta</h1>
    <p class="text-sm text-gray-500 mt-1 mb-6">Tus datos de acceso al panel Superadministrador. Si no quieres cambiar la contraseña, deja esos campos vacíos.</p>

    <form method="POST" action="{{ route('superadmin.cuenta.update') }}" novalidate data-validar autocomplete="off"
          class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col gap-8 max-w-3xl">
        @csrf @method('PUT')

        <section>
            <h2 class="text-sm font-extrabold text-prisma-azul uppercase tracking-wide mb-4">Mis datos</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-700 mb-1">Nombre *</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $cuenta->name) }}" autofocus
                           data-v data-requerido="Escribe tu nombre." data-max="100"
                           data-patron="^[\p{L}][\p{L}\s'.\-]*$" data-patron-flags="u" data-patron-msg="El nombre solo puede tener letras."
                           class="{{ $input }} {{ $borde('name') }}">
                    <p data-error-for="name" class="text-xs text-red-600 mt-1 {{ $errors->has('name') ? '' : 'hidden' }}" role="alert">{{ $errors->first('name') }}</p>
                </div>
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-700 mb-1">Correo *</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $cuenta->email) }}"
                           data-v data-requerido="Escribe tu correo." data-email data-max="150"
                           class="{{ $input }} {{ $borde('email') }}">
                    <p class="text-[11px] text-gray-400 mt-1">Es el correo con el que entras al panel.</p>
                    <p data-error-for="email" class="text-xs text-red-600 mt-1 {{ $errors->has('email') ? '' : 'hidden' }}" role="alert">{{ $errors->first('email') }}</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-sm font-extrabold text-prisma-azul uppercase tracking-wide mb-4">Cambiar contraseña (opcional)</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2 sm:max-w-sm">
                    <label for="password_actual" class="block text-xs font-bold text-gray-700 mb-1">Contraseña actual</label>
                    <input id="password_actual" name="password_actual" type="password" autocomplete="current-password" data-v
                           class="{{ $input }} {{ $borde('password_actual') }}">
                    <p data-error-for="password_actual" class="text-xs text-red-600 mt-1 {{ $errors->has('password_actual') ? '' : 'hidden' }}" role="alert">{{ $errors->first('password_actual') }}</p>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-gray-700 mb-1">Contraseña nueva</label>
                    <input id="password" name="password" type="password" autocomplete="new-password"
                           data-v data-clave data-max="100" data-revalida="#password_confirmation"
                           class="{{ $input }} {{ $borde('password') }}">
                    <p data-error-for="password" class="text-xs text-red-600 mt-1 {{ $errors->has('password') ? '' : 'hidden' }}" role="alert">{{ $errors->first('password') }}</p>
                    <ul class="mt-2 space-y-0.5 text-xs text-gray-400" id="requisitos">
                        <li data-req="largo">• Mínimo 8 caracteres</li>
                        <li data-req="mayuscula">• Una letra mayúscula</li>
                        <li data-req="minuscula">• Una letra minúscula</li>
                        <li data-req="numero">• Un número</li>
                        <li data-req="especial">• Un carácter especial (* # ! $ ...)</li>
                    </ul>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-1">Repite la contraseña nueva</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                           data-v data-igual="#password" data-igual-msg="Las dos contraseñas no coinciden."
                           class="{{ $input }} {{ $borde('password_confirmation') }}">
                    <p data-error-for="password_confirmation" class="text-xs text-red-600 mt-1 {{ $errors->has('password_confirmation') ? '' : 'hidden' }}" role="alert">{{ $errors->first('password_confirmation') }}</p>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
            <button type="submit" class="rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-5 py-2.5 transition disabled:opacity-60">Guardar cambios</button>
            <a href="{{ route('superadmin.dashboard') }}" class="rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700 text-sm font-bold px-5 py-2.5 transition">Cancelar</a>
        </div>
    </form>

    @include('superadmin._validador')

    @push('scripts')
    <script>
    // La contraseña actual solo es obligatoria si estás escribiendo una nueva
    (function () {
        var nueva = document.getElementById('password'), actual = document.getElementById('password_actual');
        function ajustar() {
            if (nueva.value !== '') { actual.setAttribute('data-requerido', 'Escribe tu contraseña actual para poder cambiarla.'); }
            else { actual.removeAttribute('data-requerido'); }
        }
        nueva.addEventListener('input', ajustar); ajustar();
    })();
    </script>
    @endpush
@endsection