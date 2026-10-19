@extends('partials.auth')

@section('titulo', 'Primer ingreso - PrismaSalud IPS')
@section('card_titulo', 'Crea tu contrasena')
@section('card_descripcion', 'Es tu primer ingreso a PrismaSalud. Completa los pasos para crear tu contrasena y acceder a tu panel.')

@section('formulario')

    {{-- Pasos del wizard: 1=cédula, 2=correo, 3=código, 4=contraseña --}}
    <div class="space-y-6">

        {{-- Barra de progreso --}}
        <div class="flex items-center justify-between mb-6 px-2">
            @php $pasos = ['cedula' => 1, 'correo' => 2, 'codigo' => 3, 'contrasena' => 4]; @endphp
            @foreach(['cedula' => 'Cedula', 'correo' => 'Correo', 'codigo' => 'Codigo', 'contrasena' => 'Contrasena'] as $clave => $etiqueta)
                @php $numero = $pasos[$clave]; $activo = $paso >= $numero; $actual = $paso === $numero; @endphp
                <div class="flex-1 flex flex-col items-center gap-2 relative">
                    {{-- Línea de progreso entre pasos --}}
                    @if($loop->index > 0)
                        <div class="absolute left-1/2 top-2 w-full h-1 bg-gray-300 z-0 {{ $activo ? 'bg-teal-600' : '' }}"></div>
                    @endif
                    {{-- Círculo del paso --}}
                    <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center
                        {{ $actual ? 'bg-teal-600 text-white border-2 border-teal-400' : ($activo ? 'bg-teal-100 text-teal-700 border-2 border-teal-300' : 'bg-gray-200 text-gray-500 border-2 border-gray-300') }}
                        shadow-sm">
                        <span class="text-sm font-bold">{{ $numero }}</span>
                    </div>
                    <span class="text-xs font-medium text-center w-20
                        {{ $actual ? 'text-teal-700' : 'text-gray-500' }}">{{ $etiqueta }}</span>
                </div>
            @endforeach
        </div>

        {{-- Paso 1: Cédula --}}
        @if($paso === 1)
            <form method="POST" action="{{ route('primer_ingreso.cedula') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="cedula" class="block text-sm font-medium text-gray-700 mb-1.5">Cedula</label>
                    <input type="number" name="cedula" id="cedula" value="{{ old('cedula') }}"
                           placeholder="Tu numero de identificacion" required autofocus
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                    <p class="text-xs text-gray-500 mt-1.5">Solo para cotizantes que aún no han creado su contrasena.</p>
                </div>

                @if($errors->has('cedula'))
                    <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">{{ $errors->first('cedula') }}</div>
                @endif

                <button type="submit"
                        class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold py-2.5 rounded-lg hover:from-green-700 hover:to-teal-600 hover:shadow-lg transition active:scale-[.99]">
                    Continuar
                </button>
            </form>
        @endif

        {{-- Paso 2: Correo --}}
        @if($paso === 2)
            <form method="POST" action="{{ route('primer_ingreso.correo') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="correo" class="block text-sm font-medium text-gray-700 mb-1.5">Correo electronico</label>
                    <input type="email" name="correo" id="correo" value="{{ old('correo', $correo) }}"
                           placeholder="tu@correo.com" required autofocus
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                    <p class="text-xs text-gray-500 mt-1.5">A este correo te enviaremos un codigo de 6 digitos.</p>
                </div>

                <div class="flex items-center justify-between mt-2">
                    <a href="{{ route('login') }}" class="text-sm text-teal-600 hover:underline font-medium">Ya tengo contrasena, iniciar sesion</a>
                </div>

                @if($errors->has('correo'))
                    <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">{{ $errors->first('correo') }}</div>
                @endif

                <button type="submit"
                        class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold py-2.5 rounded-lg hover:from-green-700 hover:to-teal-600 hover:shadow-lg transition active:scale-[.99]">
                    Enviar codigo
                </button>
            </form>
        @endif

        {{-- Paso 3: Código --}}
        @if($paso === 3)
            <form method="POST" action="{{ route('primer_ingreso.codigo') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1.5">Codigo de verificacion</label>
                    <input type="text" name="codigo" id="codigo" value="{{ old('codigo') }}"
                           placeholder="000000" maxlength="6" required autofocus
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition text-center tracking-widest">
                    <p class="text-xs text-gray-500 mt-1.5">El codigo te llego al correo. Válido por 10 minutos.</p>
                </div>

                <div class="flex items-center justify-between mt-2">
                    <button type="button" onclick="location.href='{{ route('primer_ingreso.correo') }}'"
                            class="text-sm text-teal-600 hover:underline font-medium">Reenviar codigo</button>
                </div>

                @if($errors->has('codigo'))
                    <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">{{ $errors->first('codigo') }}</div>
                @endif

                <button type="submit"
                        class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold py-2.5 rounded-lg hover:from-green-700 hover:to-teal-600 hover:shadow-lg transition active:scale-[.99]">
                    Verificar codigo
                </button>
            </form>
        @endif

        {{-- Paso 4: Contraseña --}}
        @if($paso === 4)
            <form method="POST" action="{{ route('primer_ingreso.contrasena') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-1.5">Elige tu contrasena</label>
                    <input type="password" name="contrasena" id="contrasena" autocomplete="new-password" required autofocus
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition">
                    <p class="text-xs text-gray-500 mt-1.5">Minimo 8 caracteres, con letras y numeros.</p>
                </div>

                <div>
                    <label for="contrasena_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Repite tu contrasena</label>
                    <input type="password" name="contrasena_confirmation" id="contrasena_confirmation" autocomplete="new-password" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition">
                </div>

                @if($errors->has('contrasena'))
                    <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">{{ $errors->first('contrasena') }}</div>
                @endif

                <button type="submit"
                        class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold py-2.5 rounded-lg hover:from-green-700 hover:to-teal-600 hover:shadow-lg transition active:scale-[.99]">
                    Guardar y entrar
                </button>
            </form>
        @endif

    </div>

@endsection
