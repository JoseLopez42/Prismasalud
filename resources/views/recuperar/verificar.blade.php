{{-- Segundo paso de la recuperación: solo el código de 6 dígitos.
     La contraseña se escribe en el paso siguiente (/nueva-contrasena),
     únicamente si este código resulta válido. --}}
@extends('partials.auth')

@section('titulo', 'Verificar código - PrismaSalud IPS')
@section('card_titulo', 'Verifica tu código')
@section('card_descripcion', 'Escribe el código de 6 dígitos que te enviamos por correo.')

@push('estilos')
<style>
    input.codigo {
        letter-spacing: .6em;
    }
</style>
@endpush

@section('formulario')

    @if (session('exito'))
        <div class="flex items-start gap-2 bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-5">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('exito') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex items-start gap-2 bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-5">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('recuperar.verificar') }}" class="space-y-5">
        @csrf

        <div>
            <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1.5">Código de 6 dígitos</label>
            <input type="text" name="codigo" id="codigo" maxlength="6" value="{{ old('codigo') }}" inputmode="numeric"
                   autocomplete="one-time-code" pattern="[0-9]{6}" placeholder="000000"
                   class="codigo w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-center font-semibold bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
                   required autofocus>
        </div>

        <button type="submit"
                class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold py-2.5 rounded-lg hover:from-green-700 hover:to-teal-700 hover:shadow-lg transition active:scale-[.99]">
            Verificar código
        </button>
    </form>

    <div class="mt-6 pt-5 border-t border-gray-100 text-center space-y-2">
        <p class="text-sm text-gray-500">
            ¿No recibiste el código?
            <a href="{{ route('recuperar.solicitar.form') }}" class="text-teal-600 hover:underline font-medium">
                Enviarlo de nuevo
            </a>
        </p>
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-teal-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver a iniciar sesión
        </a>
    </div>

@endsection
