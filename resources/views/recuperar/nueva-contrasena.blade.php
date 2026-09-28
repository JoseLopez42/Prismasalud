{{-- Tercer paso de la recuperación: solo la nueva contraseña. Esta vista
     únicamente se muestra cuando el código del paso anterior ya fue
     validado (el usuario está guardado en la sesión). --}}
@extends('partials.auth')

@section('titulo', 'Nueva contraseña - PrismaSalud IPS')
@section('card_titulo', 'Crea tu nueva contraseña')
@section('card_descripcion', 'Elige una contraseña nueva para acceder a tu cuenta.')

@section('formulario')

    @if ($errors->any())
        <div class="flex items-start gap-2 bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-5">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('recuperar.nueva') }}" class="space-y-5">
        @csrf

        <div>
            <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-1.5">Nueva contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    @include('admin.partials.icono', ['icono' => 'candado', 'clase' => 'w-5 h-5'])
                </div>
                <input type="password" name="contrasena" id="contrasena" autocomplete="new-password"
                       placeholder="Mínimo 6 caracteres"
                       class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
                       required autofocus>
            </div>
        </div>

        <div>
            <label for="contrasena_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar nueva contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    @include('admin.partials.icono', ['icono' => 'candado', 'clase' => 'w-5 h-5'])
                </div>
                <input type="password" name="contrasena_confirmation" id="contrasena_confirmation" autocomplete="new-password"
                       placeholder="Repite tu nueva contraseña"
                       class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
                       required>
            </div>
        </div>

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
