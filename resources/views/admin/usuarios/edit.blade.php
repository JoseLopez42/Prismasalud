@extends('admin.layout')

@section('titulo', 'Editar usuario')
@section('encabezado', 'Usuarios y Roles · Editar usuario')

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
                    @if ($usuario->foto)
                        <img src="{{ asset('storage/' . $usuario->foto) }}" alt="Foto de {{ $usuario->nombres }}"
                             class="w-11 h-11 rounded-full object-cover border-2 border-white/40">
                    @else
                        <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center text-xl">👤</div>
                    @endif
                    <div>
                        <p class="font-bold text-lg leading-tight">Editar usuario</p>
                        <p class="text-xs text-white/70">{{ $usuario->cedula }} · {{ $usuario->nombres }} {{ $usuario->apellidos }}</p>
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

                <form id="form-usuario" method="POST" action="{{ route('usuarios.update', $usuario->cedula) }}"
                      class="space-y-5" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Foto: opcional, solo se cambia si suben una nueva --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto</label>
                        <input type="file" name="foto" accept="image/*"
                               class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-teal-50 file:text-teal-700 file:font-semibold hover:file:bg-teal-100">
                        <p class="text-xs text-gray-400 mt-1">Déjalo vacío para conservar la foto actual.</p>
                    </div>

                    {{-- Cédula: no se puede editar, es la PK --}}
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Cédula</label>
                            <input type="text" value="{{ $usuario->cedula }}" disabled
                                   class="w-full border border-gray-200 bg-gray-100 text-gray-500 rounded-lg px-3 py-2.5 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de documento</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">📄</span>
                                <select name="id_tipo_documento"
                                        class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition">
                                    <option value="">Selecciona...</option>
                                    @foreach ($tiposDocumento as $tipo)
                                        <option value="{{ $tipo->id_tipo_documento }}"
                                            {{ old('id_tipo_documento', $usuario->id_tipo_documento) == $tipo->id_tipo_documento ? 'selected' : '' }}>
                                            {{ $tipo->tipo_documento }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Rol / Tipo de usuario</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">🏷️</span>
                            <select name="tipo_usuario_id" id="campo-rol"
                                    class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition"
                                    required>
                                @foreach ($tiposUsuario as $tipo)
                                    <option value="{{ $tipo->id }}"
                                        {{ old('tipo_usuario_id', $usuario->tipo_usuario_id) == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="bloque-eps">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">NIT de la EPS</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">🗂️</span>
                            <input type="text" name="nit_eps" id="campo-nit-eps"
                                   value="{{ old('nit_eps', $usuario->nit_eps) }}"
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
                                <input type="text" name="nombres" value="{{ old('nombres', $usuario->nombres) }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                       required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellidos</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">✍️</span>
                                <input type="text" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}"
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
                                <input type="text" name="edad" id="campo-edad" value="{{ old('edad', $usuario->edad) }}"
                                       inputmode="numeric" maxlength="3"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">📞</span>
                                <input type="text" name="telefono" id="campo-telefono" value="{{ old('telefono', $usuario->telefono) }}"
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
                                    <option value="{{ $ciudad->id_ciudad }}"
                                        {{ old('id_ciudad', $usuario->id_ciudad) == $ciudad->id_ciudad ? 'selected' : '' }}>
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
                            <input type="email" name="correo" value="{{ old('correo', $usuario->correo) }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nueva contraseña</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">🔒</span>
                            <input type="password" name="contrasena"
                                   class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   minlength="6">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Déjalo vacío para conservar la contraseña actual.</p>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('usuarios.index') }}"
                           class="flex-1 text-center border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar-usuario"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton-usuario">Guardar cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const ID_PACIENTE = '2';

        ['campo-edad', 'campo-telefono'].forEach(function (idCampo) {
            document.getElementById(idCampo).addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '');
            });
        });

        const campoRol = document.getElementById('campo-rol');
        const bloqueEps = document.getElementById('bloque-eps');
        const campoNitEps = document.getElementById('campo-nit-eps');

        function actualizarBloqueEps() {
            const esPaciente = campoRol.value === ID_PACIENTE;
            bloqueEps.classList.toggle('visible', esPaciente);
            campoNitEps.required = esPaciente;
            if (!esPaciente) {
                campoNitEps.value = '';
            }
        }

        campoRol.addEventListener('change', actualizarBloqueEps);
        actualizarBloqueEps(); // se ejecuta ya al cargar, según el rol actual del usuario

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