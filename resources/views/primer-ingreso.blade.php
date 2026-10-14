@extends('partials.auth')

@section('titulo', 'Crea tu contrasena - PrismaSalud IPS')
@section('card_titulo', 'Crea tu contrasena')
@section('card_descripcion', 'Es tu primer ingreso: confirma tus datos y elige tu propia contrasena')

@section('formulario')

    {{-- Modal de bienvenida / primer ingreso --}}
    <div id="modalPrimerIngreso"
         class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 text-center">
            <div class="mx-auto w-14 h-14 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0-1.657 1.343-3 3-3s3 1.343 3 3-1.343 3-3 3-3-1.343-3-3zm-7 9a7 7 0 0114 0" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Ahora crea tu contrasena</h3>
            <p class="text-sm text-gray-600 mb-5">
                Es tu primer ingreso a PrismaSalud. Antes de entrar, elige una contrasena propia
                para proteger tu cuenta.
            </p>
            <button type="button" onclick="cerrarModalPrimerIngreso()"
                    class="w-full bg-teal-600 text-white font-semibold py-2.5 rounded-lg hover:bg-teal-700 transition">
                Entendido, crear mi contrasena
            </button>
        </div>
    </div>

    <script>
        function cerrarModalPrimerIngreso() {
            var modal = document.getElementById('modalPrimerIngreso');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById('modalPrimerIngreso');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    </script>

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
            <label for="cedula" class="block text-sm font-medium text-gray-700 mb-1.5">Cedula</label>
            <input type="number" name="cedula" id="cedula" value="{{ old('cedula', $cedula) }}"
                   placeholder="Tu numero de identificacion" required readonly
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-100 cursor-not-allowed focus:outline-none transition">
        </div>

        <div>
            <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-1.5">Elige tu contrasena</label>
            <input type="password" name="contrasena" id="contrasena" autocomplete="new-password" required {{ $cedula ? 'autofocus' : '' }}
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition">
            <p class="text-xs text-gray-500 mt-1.5">
                Cotizante: minimo 8 caracteres, con letras y numeros.<br>
                Beneficiario: entre 4 y 12 caracteres, corta y facil de recordar.
            </p>
        </div>

        <div>
            <label for="contrasena_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Repite tu contrasena</label>
            <input type="password" name="contrasena_confirmation" id="contrasena_confirmation" autocomplete="new-password" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition">
        </div>

        <button type="submit"
                class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold py-2.5 rounded-lg hover:from-green-700 hover:to-teal-600 hover:shadow-lg transition active:scale-[.99]">
            Guardar y entrar
        </button>

        <a href="{{ route('login') }}" class="block text-center text-sm text-teal-600 hover:underline font-medium">
            Ya tengo contrasena, iniciar sesion
        </a>
    </form>

@endsection