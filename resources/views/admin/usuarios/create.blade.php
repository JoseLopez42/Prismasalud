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
                    <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
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
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="12" r="2.5"/><path d="M14 10h4M14 14h4M5 16.5c.6-1.3 1.7-2 3.5-2s2.9.7 3.5 2"/></svg></span>
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
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span>
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
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/></svg></span>
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
                        <p id="info-sub-tipo-paciente" class="text-xs text-gray-400 mt-1 hidden">
                            Los pacientes creados aquí se registran como <strong>Paciente Cotizante</strong> (titular de la afiliación).
                            Los <strong>Paciente Beneficiario</strong> se registran desde el módulo de Afiliación.
                        </p>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombres</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></span>
                                <input type="text" name="nombres" value="{{ old('nombres') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                       required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellidos</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></span>
                                <input type="text" name="apellidos" value="{{ old('apellidos') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                       required>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></span>
                                <input type="text" name="telefono" id="campo-telefono" value="{{ old('telefono') }}"
                                       inputmode="numeric" maxlength="10"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Ciudad</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></span>
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
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
                            <input type="email" name="correo" value="{{ old('correo') }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></span>
                            <input type="password" name="contrasena"
                                   class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required minlength="6">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Mínimo 6 caracteres. Se guarda encriptada.</p>
                    </div>

                    {{-- Datos adicionales del Paciente (se muestran solo si el rol es Paciente) --}}
                    <div id="datos-paciente" class="hidden bg-teal-50 border border-teal-100 rounded-2xl shadow-sm overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-5 text-white">
                            <p class="font-bold text-lg leading-tight">Datos adicionales del paciente</p>
                            <p class="text-xs text-white/70">Datos que solicita la EPS/IPS al momento de la afiliación.</p>
                        </div>

                        <div class="p-6 space-y-5">
                        {{-- El "Tipo de documento" ya se pide arriba en el formulario principal,
                             por eso aquí no se repite (quedaría duplicado). --}}
                        {{-- La edad ya no se escribe: se calcula desde esta fecha. --}}
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de nacimiento <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
                                   min="1900-01-01" max="{{ now()->toDateString() }}" required
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>

                        <div class="grid md:grid-cols-2 gap-4 mt-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">País de expedición</label>
                                    <select name="id_pais_expedicion" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($paises as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ (string) old('id_pais_expedicion') === (string) $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Género</label>
                                    <select name="id_genero" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($generos as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ (string) old('id_genero') === (string) $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado civil</label>
                                    <select name="id_estado_civil" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($estadosCiviles as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ (string) old('id_estado_civil') === (string) $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Grupo étnico</label>
                                    <select name="grupo_etnico" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($gruposEtnicos as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ old('grupo_etnico') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">¿Tiene discapacidad?</label>
                                    <select name="tiene_discapacidad" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="0" {{ old('tiene_discapacidad', false) ? '' : 'selected' }}>No</option>
                                        <option value="1" {{ old('tiene_discapacidad') ? 'selected' : '' }}>Sí</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de discapacidad</label>
                                    <input type="text" name="tipo_discapacidad" id="tipo-discapacidad-campo-usuario"
                                           placeholder="Solo si tiene discapacidad"
                                           value="{{ old('tipo_discapacidad') }}"
                                           class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition {{ old('tiene_discapacidad') ? '' : 'hidden' }}"
                                           {{ old('tiene_discapacidad') ? '' : 'disabled' }}>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nacionalidad</label>
                                    <select name="id_pais_nacionalidad" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($paises as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ (string) old('id_pais_nacionalidad') === (string) $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">País de nacimiento</label>
                                    <select name="id_pais_nacimiento" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($paises as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ (string) old('id_pais_nacimiento') === (string) $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Zona de residencia</label>
                                    <select name="id_zona_residencia" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($zonasResidencia as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ (string) old('id_zona_residencia') === (string) $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono fijo</label>
                                    <input type="text" name="telefono_fijo" value="{{ old('telefono_fijo') }}"
                                           class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Estrato</label>
                                    <select name="estrato" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($estratos as $e)
                                            <option value="{{ $e }}" {{ old('estrato') == $e ? 'selected' : '' }}>Estrato {{ $e }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nivel educativo</label>
                                    <select name="id_nivel_educativo" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($nivelesEducativos as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ (string) old('id_nivel_educativo') === (string) $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ocupación / Profesión</label>
                                    <select name="id_ocupacion" id="ocupacion-select-usuario" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($ocupaciones as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ (string) old('id_ocupacion') === (string) $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="ocupacion_otra" id="ocupacion-otra-usuario"
                                           placeholder="Escribe tu ocupación"
                                           value="{{ old('ocupacion_otra') }}"
                                           class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition hidden"
                                           {{ (string) old('id_ocupacion') === (string) $ocupacionOtroId ? '' : 'disabled' }}>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de sangre</label>
                                    <select name="tipo_sangre" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($tiposSangre as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ old('tipo_sangre') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">¿Víctima del conflicto armado?</label>
                                    <select name="victima_conflicto_armado" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="0" {{ old('victima_conflicto_armado', false) ? '' : 'selected' }}>No</option>
                                        <option value="1" {{ old('victima_conflicto_armado') ? 'selected' : '' }}>Sí</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Número de afiliación anterior</label>
                                <input type="text" name="numero_afiliacion_anterior" value="{{ old('numero_afiliacion_anterior') }}"
                                       placeholder="Si se transfiere de otra EPS"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('usuarios.index') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar-usuario"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton-usuario" class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Crear usuario</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Cédula y teléfono: solo dígitos. La fecha se elige en un calendario.
        ['campo-cedula', 'campo-telefono'].forEach(function (idCampo) {
            document.getElementById(idCampo).addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '');
            });
        });

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

        // Mostrar/ocultar bloque de datos del paciente según el rol seleccionado
        const campoRol = document.getElementById('campo-rol');
        const bloquePaciente = document.getElementById('datos-paciente');

        if (campoRol && bloquePaciente) {
            const infoSubTipo = document.getElementById('info-sub-tipo-paciente');

            function toggleDatosPaciente() {
                const rolSeleccionado = campoRol.value;
                const textoRol = campoRol.options[campoRol.selectedIndex]?.textContent?.trim() || '';
                const esPaciente = textoRol.toLowerCase() === 'paciente';
                bloquePaciente.classList.toggle('hidden', !esPaciente);

                // Aviso de que el paciente se guardará como "Paciente Cotizante"
                if (infoSubTipo) {
                    infoSubTipo.classList.toggle('hidden', !esPaciente);
                }

                // Si no es paciente, limpiar y deshabilitar los campos adicionales
                if (!esPaciente) {
                    bloquePaciente.querySelectorAll('input, select').forEach(el => {
                        el.disabled = true;
                        el.value = '';
                    });
                } else {
                    bloquePaciente.querySelectorAll('input, select').forEach(el => {
                        el.disabled = false;
                    });
                }
            }
            campoRol.addEventListener('change', toggleDatosPaciente);
            toggleDatosPaciente(); // estado inicial al cargar

            // Mostrar/ocultar campo "Tipo de discapacidad"
            const selDiscapacidad = formUsuario.querySelector('select[name="tiene_discapacidad"]');
            const campoTipoDiscapacidad = document.getElementById('tipo-discapacidad-campo-usuario');
            if (selDiscapacidad && campoTipoDiscapacidad) {
                function toggleTipoDiscapacidad() {
                    const esSi = selDiscapacidad.value === '1';
                    campoTipoDiscapacidad.classList.toggle('hidden', !esSi);
                    campoTipoDiscapacidad.disabled = !esSi;
                    if (!esSi) campoTipoDiscapacidad.value = '';
                }
                selDiscapacidad.addEventListener('change', toggleTipoDiscapacidad);
                toggleTipoDiscapacidad();
            }

            // Mostrar/ocultar campo "Ocupación otra"
            const selOcupacion = document.getElementById('ocupacion-select-usuario');
            const campoOcupacionOtra = document.getElementById('ocupacion-otra-usuario');
            if (selOcupacion && campoOcupacionOtra) {
                function toggleOcupacionOtra() {
                    const esOtro = String(selOcupacion.value) === '{{ $ocupacionOtroId }}';
                    campoOcupacionOtra.classList.toggle('hidden', !esOtro);
                    campoOcupacionOtra.disabled = !esOtro;
                    if (!esOtro) campoOcupacionOtra.value = '';
                }
                selOcupacion.addEventListener('change', toggleOcupacionOtra);
                toggleOcupacionOtra();
            }
        }
    </script>

@endsection