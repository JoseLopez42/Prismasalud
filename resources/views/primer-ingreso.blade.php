@extends('partials.auth')

@section('titulo', 'Crea tu contraseña - PrismaSalud IPS')
@section('card_titulo', 'Crea tu contraseña')
@section('card_descripcion', 'Es tu primer ingreso: confirma tus datos y elige tu propia contraseña')

@section('formulario')

    @if ($errors->any())
        <div class="flex items-start gap-2 bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-5">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('primer_ingreso.guardar') }}" class="space-y-5">
        @csrf

        <div>
            <label for="cedula" class="block text-sm font-medium text-gray-700 mb-1.5">Cédula</label>
            <input type="number" name="cedula" id="cedula" value="{{ old('cedula', $cedula) }}"
                   placeholder="Tu número de identificación" required {{ $cedula ? '' : 'autofocus' }}
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition">
        </div>

        <div>
            <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de nacimiento</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required
                   max="{{ date('Y-m-d') }}" {{ $cedula ? 'autofocus' : '' }}
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition">
            <p class="text-xs text-gray-500 mt-1.5">Es la misma que registraron al afiliarte.</p>
        </div>

        <div>
            <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-1.5">Elige tu contraseña</label>
            <input type="password" name="contrasena" id="contrasena" autocomplete="new-password" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition">
            <p class="text-xs text-gray-500 mt-1.5">
                Cotizante: mínimo 8 caracteres, con letras y números.<br>
                Beneficiario: entre 4 y 12 caracteres, corta y fácil de recordar.
            </p>
        </div>

        <div>
            <label for="contrasena_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Repite tu contraseña</label>
            <input type="password" name="contrasena_confirmation" id="contrasena_confirmation" autocomplete="new-password" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition">
        </div>

        <button type="submit"
                class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold py-2.5 rounded-lg hover:from-green-700 hover:to-teal-700 hover:shadow-lg transition active:scale-[.99]">
            Guardar y entrar
        </button>

        <a href="{{ route('login') }}" class="block text-center text-sm text-teal-600 hover:underline font-medium">
            Ya tengo contraseña, iniciar sesión
        </a>
    </form>

@endsection