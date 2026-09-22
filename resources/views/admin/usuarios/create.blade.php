@extends('admin.layout')

@section('titulo', 'Nuevo usuario')
@section('encabezado', 'Usuarios y Roles · Nuevo usuario')

@push('estilos')
<style>
    @keyframes aparecer {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .alerta-animada { animation: aparecer .25s ease; }

    @keyframes sacudir {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }
    .campo-invalido { animation: sacudir .3s ease; border-color: #ef4444 !important; }

    .spinner {
        width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: girar .6s linear infinite;
    }
    @keyframes girar { to { transform: rotate(360deg); } }

    /* Bloque de EPS: aparece/desaparece suavemente según el rol elegido */
    #bloque-eps {
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        transition: max-height .25s ease, opacity .25s ease;
    }
    #bloque-eps.visible {
        max-height: 200px;
        opacity: 1;
    }
</style>
@endpush

@section('contenido')

    <a href="{{ route('usuarios.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a usuarios
    </a>

    <div class="max-w-2xl mt-4">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center text-xl">👤</div>
                    <div>
                        <p class="font-bold text-lg leading-tight">Nuevo usuario</p>
                        <p class="text-xs text-white/70">Regístralo con su cédula, datos y rol</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- enctype obligatorio para poder subir el archivo de la foto --}}
                <form id="form-usuario" method="POST" action="{{ route('usuarios.store') }}"
                      class="space-y-5" enctype="multipart/form-data" novalidate>
                    @csrf

                    {{-- Foto (opcional) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto (opcional)</label>
                        <input type="file" name="foto" accept="image/*"
                               class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-teal-50 file:text-teal-700 file:font-semibold hover:file:bg-teal-100">
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        {{-- Cédula --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Cédula</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">🪪</span>
                                <input type="text" name="cedula" id="campo-cedula" value="{{ old('cedula') }}"
                                       inputmode="numeric"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                       placeholder="Ej: 1105465437" required>
                            </div>
                        </div>

                        {{-- Tipo de documento --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de documento</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">📄</span>
                                <select name="id_tipo_documento"
                                        class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition">
                                    <option value="">Selecciona...</option>
                                    @foreach ($tiposDocumento as $tipo)
                                        <option value="{{ $tipo->id_tipo_documento }}" {{ old('id_tipo_documento') == $tipo->id_tipo_documento ? 'selected' : '' }}>
                                            {{ $tipo->tipo_documento }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Rol: dispara el bloque de EPS si es Paciente --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Rol / Tipo de usuario</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">🏷️</span>
                            <select name="tipo_usuario_id" id="campo-rol"
                                    class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition"
                                    required>
                                <option value="" disabled {{ old('tipo_usuario_id') ? '' : 'selected' }}>Selecciona un rol...</option>
                                @foreach ($tiposUsuario as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_usuario_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- NIT de EPS: solo se muestra y se exige si el rol es Paciente.
                         El "2" es el id de Paciente según el seeder. --}}
                    <div id="bloque-eps">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">NIT de la EPS</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">🗂️</span>
                            <input type="text" name="nit_eps" id="campo-nit-eps" value="{{ old('nit_eps') }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   placeholder="Ej: 900123456">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Obligatorio para pacientes.</p>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombres</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">✍️</span>
                                <input type="text" name="nombres" value="{{ old('nombres') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                       required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellidos</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">✍️</span>
                                <input type="text" name="apellidos" value="{{ old('apellidos') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Edad</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">🎂</span>
                                <input type="text" name="edad" id="campo-edad" value="{{ old('edad') }}"
                                       inputmode="numeric" maxlength="3"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">📞</span>
                                <input type="text" name="telefono" id="campo-telefono" value="{{ old('telefono') }}"
                                       inputmode="numeric" maxlength="10"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Ciudad</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">🏙️</span>
                            <select name="id_ciudad"
                                    class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition">
                                <option value="">Selecciona...</option>
                                @foreach ($ciudades as $ciudad)
                                    <option value="{{ $ciudad->id_ciudad }}" {{ old('id_ciudad') == $ciudad->id_ciudad ? 'selected' : '' }}>
                                        {{ $ciudad->ciudad }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Correo</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">✉️</span>
                            <input type="email" name="correo" value="{{ old('correo') }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">🔒</span>
                            <input type="password" name="contrasena"
                                   class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required minlength="6">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Mínimo 6 caracteres. Se guarda encriptada.</p>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('usuarios.index') }}"
                           class="flex-1 text-center border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar-usuario"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton-usuario">+ Crear usuario</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ID de Paciente según el seeder de tipos_usuario
        const ID_PACIENTE = '2';

        // Cédula, edad y teléfono: solo dígitos
        ['campo-cedula', 'campo-edad', 'campo-telefono'].forEach(function (idCampo) {
            document.getElementById(idCampo).addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '');
            });
        });

        // Muestra/oculta el bloque de EPS según el rol elegido, y hace
        // que el campo NIT sea obligatorio solo cuando está visible.
        const campoRol = document.getElementById('campo-rol');
        const bloqueEps = document.getElementById('bloque-eps');
        const campoNitEps = document.getElementById('campo-nit-eps');

        function actualizarBloqueEps() {
            const esPaciente = campoRol.value === ID_PACIENTE;
            bloqueEps.classList.toggle('visible', esPaciente);
            campoNitEps.required = esPaciente;
            if (!esPaciente) {
                campoNitEps.value = ''; // limpia el NIT si cambian de rol
            }
        }

        campoRol.addEventListener('change', actualizarBloqueEps);
        actualizarBloqueEps(); // por si vuelve con "old()" tras un error de validación

        // Validación visual + spinner al enviar
        const formUsuario = document.getElementById('form-usuario');
        const botonGuardarUsuario = document.getElementById('boton-guardar-usuario');
        const textoBotonUsuario = document.getElementById('texto-boton-usuario');

        formUsuario.addEventListener('submit', function (evento) {
            const camposInvalidos = Array.from(formUsuario.querySelectorAll('.campo-form'))
                .filter(campo => !campo.checkValidity());

            if (camposInvalidos.length > 0) {
                evento.preventDefault();
                camposInvalidos.forEach(campo => {
                    campo.classList.add('campo-invalido');
                    campo.addEventListener('animationend', () => campo.classList.remove('campo-invalido'), { once: true });
                });
                camposInvalidos[0].focus();
                return;
            }

            botonGuardarUsuario.disabled = true;
            textoBotonUsuario.innerHTML = '<span class="spinner"></span> Guardando...';
        });
    </script>

@endsection