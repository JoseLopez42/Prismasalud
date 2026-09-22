@extends('admin.layout')

@section('titulo', 'Nueva sede')
@section('encabezado', 'Ubicaciones · Nueva sede')

@push('estilos')
<style>
    /* Animación de entrada para las alertas de error */
    @keyframes aparecer {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .alerta-animada { animation: aparecer .25s ease; }

    /* Pequeño "shake" cuando un campo requerido queda vacío al enviar */
    @keyframes sacudir {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }
    .campo-invalido { animation: sacudir .3s ease; border-color: #ef4444 !important; }

    /* Spinner del botón mientras se envía el formulario */
    .spinner {
        width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: girar .6s linear infinite;
    }
    @keyframes girar { to { transform: rotate(360deg); } }
</style>
@endpush

@section('contenido')

    <a href="{{ route('sedes.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a sedes
    </a>

    <div class="max-w-lg mt-4">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center text-xl">🏥</div>
                    <div>
                        <p class="font-bold text-lg leading-tight">Nueva sede</p>
                        <p class="text-xs text-white/70">Ubicación física de PrismaSalud IPS</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="form-sede" method="POST" action="{{ route('sedes.store') }}" class="space-y-5" novalidate>
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre de la sede</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">🏥</span>
                            <input type="text" name="nombre_sede" value="{{ old('nombre_sede') }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   placeholder="Ej: Sede Norte" required autofocus>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Dirección</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">📌</span>
                                <input type="text" name="direccion" value="{{ old('direccion') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                       placeholder="Calle 15 # 3-45" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">📞</span>
                                {{-- inputmode + JS: solo deja escribir números --}}
                                <input type="text" name="telefono" id="campo-telefono" value="{{ old('telefono') }}"
                                       inputmode="numeric" maxlength="10"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                       placeholder="6081234567" required>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Ciudad</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">🏙️</span>
                            <select name="id_ciudad"
                                    class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition"
                                    required>
                                <option value="" disabled {{ old('id_ciudad') ? '' : 'selected' }}>Selecciona una ciudad...</option>
                                @foreach ($ciudades as $ciudad)
                                    <option value="{{ $ciudad->id_ciudad }}" {{ old('id_ciudad') == $ciudad->id_ciudad ? 'selected' : '' }}>
                                        {{ $ciudad->ciudad }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('sedes.index') }}"
                           class="flex-1 text-center border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar-sede"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton-sede">+ Crear sede</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // El teléfono solo deja escribir dígitos, quitando cualquier
        // otro carácter que el usuario pegue o teclee.
        const campoTelefono = document.getElementById('campo-telefono');
        campoTelefono.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, ''); // \D = todo lo que NO es dígito
        });

        // Validación visual: si el usuario envía el formulario con un
        // campo requerido vacío, se lo marcamos con un "shake" en vez
        // de dejar que el navegador lo maneje en silencio.
        const formSede = document.getElementById('form-sede');
        const botonGuardarSede = document.getElementById('boton-guardar-sede');
        const textoBotonSede = document.getElementById('texto-boton-sede');

        formSede.addEventListener('submit', function (evento) {
            const camposInvalidos = Array.from(formSede.querySelectorAll('.campo-form'))
                .filter(campo => !campo.checkValidity());

            if (camposInvalidos.length > 0) {
                evento.preventDefault(); // no se envía hasta que estén completos
                camposInvalidos.forEach(campo => {
                    campo.classList.add('campo-invalido');
                    campo.addEventListener('animationend', () => campo.classList.remove('campo-invalido'), { once: true });
                });
                camposInvalidos[0].focus();
                return;
            }

            // Todo válido: deshabilita el botón y muestra el spinner,
            // para que el usuario no le dé doble clic mientras se guarda.
            botonGuardarSede.disabled = true;
            textoBotonSede.innerHTML = '<span class="spinner"></span> Guardando...';
        });
    </script>

@endsection