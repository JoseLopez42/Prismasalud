@extends('admin.layout')

@section('titulo', 'Nueva afiliación')
@section('encabezado', 'Afiliaciones · Nueva afiliación')

@push('estilos')
<style>
    @keyframes aparecer { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
    .alerta-animada { animation: aparecer .25s ease; }
    @keyframes sacudir { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-4px); } 75% { transform: translateX(4px); } }
    .campo-invalido { animation: sacudir .3s ease; border-color: #ef4444 !important; }
    .spinner { width: 16px; height: 16px; border: 2px solid rgba(255,255,255,.4); border-top-color: #fff; border-radius: 50%; animation: girar .6s linear infinite; }
    @keyframes girar { to { transform: rotate(360deg); } }
</style>
@endpush

@section('contenido')
    <a href="{{ route('afiliaciones.index') }}" class="text-sm text-teal-600 hover:underline">← Volver a afiliaciones</a>

    <div class="mt-4">
        @if ($errors->any())
            <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                {{ $errors->first() }}
            </div>
        @endif

        <form id="form-afiliacion" method="POST" action="{{ route('afiliaciones.store') }}" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4" novalidate>
            @csrf

            {{-- Columna 1: datos de la afiliación --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden h-fit">
                <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-5 text-white">
                    <p class="font-bold text-lg leading-tight">Datos de la afiliación</p>
                    <p class="text-xs text-white/70">Registra la afiliación con el titular y el beneficiario</p>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de afiliación</label>
                        <input type="date" name="fecha_afiliacion" value="{{ old('fecha_afiliacion', now()->format('Y-m-d')) }}"
                               class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm bg-gray-100 text-gray-600 focus:outline-none transition cursor-not-allowed" required readonly>
                        <p class="text-xs text-gray-400 mt-1">Se asigna automáticamente la fecha actual del sistema.</p>
                    </div>

                    <div class="bg-teal-50 border border-teal-100 text-teal-800 text-sm rounded-md px-4 py-3">
                        El <strong>parentesco</strong> y la <strong>EPS</strong> se toman automáticamente de la persona
                        afiliada. Los beneficiarios nuevos se registran en el formulario de la derecha.
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de afiliación</label>
                        <select name="id_tipo_afiliacion" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            <option value="">Seleccione...</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id_regimen }}" {{ old('id_tipo_afiliacion') == $tipo->id_regimen ? 'selected' : '' }}>
                                    {{ $tipo->nombre_regimen }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Usuario (cotizante)</label>
                        <select name="cc_usuario" id="sel-usuario" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            <option value="">Seleccione...</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->cedula }}" {{ old('cc_usuario', request('cc_usuario')) == $u->cedula ? 'selected' : '' }}>
                                    {{ $u->nombres }} {{ $u->apellidos }} ({{ $u->cedula }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado</label>
                        <select name="id_estado" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            <option value="">Seleccione...</option>
                            @foreach ($estados as $es)
                                <option value="{{ $es->id_estado }}" {{ old('id_estado') == $es->id_estado ? 'selected' : '' }}>
                                    {{ $es->estado }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Campos adicionales de afiliación --}}
                    <div class="bg-teal-50 border border-teal-100 text-teal-800 text-sm rounded-md px-4 py-3 mb-5">
                        <p class="font-bold mb-2">Datos adicionales del afiliado</p>
                        <p class="text-xs mb-3">Datos que solicita la EPS/IPS al momento de la afiliación.</p>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de documento</label>
                                <select name="id_tipo_documento" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach ($tiposDocumento as $td)
                                        <option value="{{ $td->id_tipo_documento }}" {{ old('id_tipo_documento') == $td->id_tipo_documento ? 'selected' : '' }}>
                                            {{ $td->tipo_documento }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">País de expedición</label>
                                <select name="pais_expedicion" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach ($paises as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('pais_expedicion') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Género</label>
                                <select name="genero" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach ($generos as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('genero') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado civil</label>
                                <select name="estado_civil" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach ($estadosCiviles as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('estado_civil') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
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

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de discapacidad</label>
                                <input type="text" name="tipo_discapacidad" value="{{ old('tipo_discapacidad') }}"
                                       placeholder="Solo si tiene discapacidad"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nacionalidad</label>
                                <input type="text" name="nacionalidad" value="{{ old('nacionalidad', 'Colombia') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">País de nacimiento</label>
                                <input type="text" name="pais_nacimiento" value="{{ old('pais_nacimiento', 'Colombia') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Zona de residencia</label>
                                <select name="zona_residencia" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach ($zonasResidencia as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('zona_residencia') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
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

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nivel educativo</label>
                                <select name="nivel_educativo" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach ($nivelesEducativos as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('nivel_educativo') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ocupación / Profesión</label>
                                <input type="text" name="ocupacion" value="{{ old('ocupacion') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
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
            </div>

            {{-- Columna 2: beneficiario --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden h-fit">
                <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-5 text-white">
                    <p class="font-bold text-lg leading-tight">Beneficiario de la afiliación</p>
                    <p class="text-xs text-white/70">Elige uno existente o crea uno nuevo a la derecha</p>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Origen del beneficiario</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="border border-gray-200 rounded-lg p-3 text-sm font-medium cursor-pointer hover:bg-teal-50">
                                <input type="radio" name="origen_beneficiario" value="existente"
                                       class="mr-1" {{ old('origen_beneficiario', 'existente') === 'existente' ? 'checked' : '' }}>
                                Beneficiario existente
                            </label>
                            <label class="border border-gray-200 rounded-lg p-3 text-sm font-medium cursor-pointer hover:bg-teal-50">
                                <input type="radio" name="origen_beneficiario" value="nuevo"
                                       class="mr-1" {{ old('origen_beneficiario') === 'nuevo' ? 'checked' : '' }}>
                                Nuevo beneficiario
                            </label>
                        </div>
                    </div>

                    {{-- Beneficiario existente --}}
                    <div id="panel-existente" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Beneficiario</label>
                            <select name="cc_afiliado" id="sel-beneficiario"
                                    class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <option value="">Seleccione...</option>
                                @foreach ($beneficiarios as $b)
                                    <option value="{{ $b->cc_afiliado }}"
                                            data-cotizante="{{ $b->cc_cotizante ?? $b->cc_afiliado }}"
                                            {{ old('cc_afiliado') == $b->cc_afiliado ? 'selected' : '' }}>
                                        {{ $b->nombres }} {{ $b->apellidos }} ({{ $b->cc_afiliado }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Nuevo beneficiario --}}
                    <div id="panel-nuevo" class="hidden space-y-4">
                        <div class="bg-teal-50 border border-teal-100 text-teal-800 text-sm rounded-md px-4 py-3">
                            Se crea el beneficiario y su cuenta de acceso. La EPS se hereda del cotizante elegido.
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Es familiar del cotizante</label>
                            <input type="hidden" name="nuevo_cc_cotizante" id="nuevo-cotizante-hidden"
                                   value="{{ old('nuevo_cc_cotizante', old('cc_usuario', request('cc_usuario'))) }}">
                            <select id="nuevo-cotizante"
                                    class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed"
                                    disabled>
                                <option value="">Seleccione...</option>
                                @foreach ($cotizantes as $c)
                                    <option value="{{ $c->cc_afiliado }}" data-eps="{{ $c->eps->nombre_eps ?? 'Sin EPS asignada' }}" {{ old('nuevo_cc_cotizante', old('cc_usuario', request('cc_usuario'))) == $c->cc_afiliado ? 'selected' : '' }}>
                                        {{ $c->nombres }} {{ $c->apellidos }} ({{ $c->cc_afiliado }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Se toma automáticamente del cotizante seleccionado arriba; no se puede elegir otra persona.</p>
                            <p id="eps-nueva" class="text-xs text-teal-700 mt-1.5"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Parentesco</label>
                            <select name="nuevo_parentesco" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <option value="">Seleccione...</option>
                                @foreach ($parentescos as $p)
                                    <option value="{{ $p->nombre }}" {{ old('nuevo_parentesco') === $p->nombre ? 'selected' : '' }}>
                                        {{ $p->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Cédula</label>
                            <input type="number" inputmode="numeric" name="nuevo_cc_afiliado" value="{{ old('nuevo_cc_afiliado') }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombres</label>
                                <input type="text" name="nuevo_nombres" value="{{ old('nuevo_nombres') }}" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellidos</label>
                                <input type="text" name="nuevo_apellidos" value="{{ old('nuevo_apellidos') }}" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Edad</label>
                                <input type="number" name="nuevo_edad" value="{{ old('nuevo_edad') }}" min="0" max="120" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                                <input type="text" name="nuevo_telefono" value="{{ old('nuevo_telefono') }}" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Dirección</label>
                            <input type="text" name="nuevo_direccion" value="{{ old('nuevo_direccion') }}" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Correo</label>
                                <input type="email" name="nuevo_correo" value="{{ old('nuevo_correo') }}" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de nacimiento</label>
                                <input type="date" name="nuevo_fecha_nacimiento" value="{{ old('nuevo_fecha_nacimiento') }}" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto <span class="text-gray-400 font-normal">(opcional)</span></label>
                            <input type="file" name="nuevo_foto" accept="image/*"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm shadow-sm bg-white file:mr-3 file:rounded-md file:border-0 file:bg-teal-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-teal-700 hover:file:bg-teal-100">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('afiliaciones.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton" class="inline-flex items-center gap-1.5">Crear afiliación</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('form-afiliacion');
        const boton = document.getElementById('boton-guardar');
        const texto = document.getElementById('texto-boton');
        const selUsuario = form.querySelector('select[name="cc_usuario"]');
        const selBenef = form.querySelector('select[name="cc_afiliado"]');
        const opcionesBenef = selBenef ? Array.from(selBenef.querySelectorAll('option[value]')).filter(o => o.value !== '').map(o => ({
            valor: o.value,
            texto: o.textContent.trim(),
            cotizante: o.dataset.cotizante || ''
        })) : [];
        const origenes = form.querySelectorAll('input[name="origen_beneficiario"]');
        const panelExistente = document.getElementById('panel-existente');
        const panelNuevo = document.getElementById('panel-nuevo');
        const selCotizante = document.getElementById('nuevo-cotizante');
        const selCotizanteHidden = document.getElementById('nuevo-cotizante-hidden');
        const epsNueva = document.getElementById('eps-nueva');

        // Sincroniza el cotizante del nuevo beneficiario con el usuario (cotizante)
        // seleccionado arriba; así no se vuelve a elegir y no puede ser distinto.
        function sincronizarCotizanteNuevo() {
            if (!selCotizante || !selUsuario) return;
            const usuario = selUsuario.value;
            const existe = Array.from(selCotizante.options).some(o => o.value !== '' && o.value === usuario);
            if (existe) {
                selCotizante.value = usuario;
                if (selCotizanteHidden) selCotizanteHidden.value = usuario;
            } else {
                selCotizante.value = '';
                if (selCotizanteHidden) selCotizanteHidden.value = '';
            }
            mostrarEpsNueva();
        }

        function alternarOrigen() {
            const origen = Array.from(origenes).find(r => r.checked)?.value || 'existente';
            const esNuevo = origen === 'nuevo';
            panelExistente.classList.toggle('hidden', esNuevo);
            panelNuevo.classList.toggle('hidden', !esNuevo);

            if (selBenef) selBenef.required = !esNuevo;
            // El cotizante del beneficiario se sincroniza con el usuario, no es editable.
            if (selCotizanteHidden) selCotizanteHidden.required = esNuevo;
            panelNuevo.querySelectorAll('input, select').forEach(el => {
                if (el.name && el.name.startsWith('nuevo_') && !el.name.endsWith('_foto')
                    && el.id !== 'nuevo-cotizante' && el.id !== 'nuevo-cotizante-hidden') {
                    el.required = esNuevo;
                }
            });
            if (!esNuevo) {
                if (epsNueva) epsNueva.textContent = '';
            } else {
                sincronizarCotizanteNuevo();
                mostrarEpsNueva();
            }
        }

        origenes.forEach(r => r.addEventListener('change', alternarOrigen));
        alternarOrigen();

        if (selUsuario && selBenef) {
            function filtrarBeneficiarios() {
                const usuario = selUsuario.value;
                const lista = usuario ? opcionesBenef.filter(o => o.cotizante === usuario) : [];
                const seleccionado = selBenef.value;
                selBenef.innerHTML = '';
                const base = document.createElement('option');
                base.value = '';
                base.textContent = !usuario
                    ? 'Primero selecciona el usuario...'
                    : (lista.length ? 'Seleccione...' : 'Este usuario no tiene beneficiarios registrados');
                selBenef.appendChild(base);

                lista.forEach(o => {
                    const op = document.createElement('option');
                    op.value = o.valor;
                    op.textContent = o.texto;
                    if (o.valor === seleccionado) op.selected = true;
                    selBenef.appendChild(op);
                });
            }
            selUsuario.addEventListener('change', filtrarBeneficiarios);
            selUsuario.addEventListener('change', sincronizarCotizanteNuevo);
            filtrarBeneficiarios();
            sincronizarCotizanteNuevo();
        }

        function mostrarEpsNueva() {
            if (!selCotizante || !epsNueva) return;
            const opcion = selCotizante.options[selCotizante.selectedIndex];
            if (opcion && opcion.value !== '') {
                epsNueva.textContent = 'EPS heredada: ' + opcion.getAttribute('data-eps');
            } else {
                epsNueva.textContent = '';
            }
        }
        if (selCotizante) {
            selCotizante.addEventListener('change', mostrarEpsNueva);
            mostrarEpsNueva();
        }

        form.addEventListener('submit', function (evento) {
            const invalidos = Array.from(form.querySelectorAll('.campo-form')).filter(c => !c.checkValidity());
            if (invalidos.length > 0) {
                evento.preventDefault();
                invalidos.forEach(c => {
                    c.classList.add('campo-invalido');
                    c.addEventListener('animationend', () => c.classList.remove('campo-invalido'), { once: true });
                });
                invalidos[0].focus();
                return;
            }
            boton.disabled = true;
            texto.innerHTML = '<span class="spinner"></span> Guardando...';
        });
    </script>
@endsection