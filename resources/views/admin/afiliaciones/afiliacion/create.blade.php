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

    {{-- Una sola columna: "Datos de la afiliación" y debajo el bloque del
         beneficiario. Centrado y con ancho acotado para que se vea ordenado. --}}
    <div class="mt-4 mx-auto max-w-4xl">
        @if ($errors->any())
            <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                {{ $errors->first() }}
            </div>
        @endif

        <form id="form-afiliacion" method="POST" action="{{ route('afiliaciones.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-4" novalidate>
            @csrf

            {{-- Datos de la afiliación --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden h-fit">
                <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-5 text-white">
                    <p class="font-bold text-lg leading-tight">Datos de la afiliación</p>
                    <p class="text-xs text-white/70">Registra la afiliación con el titular y el beneficiario</p>
                </div>

                <div class="p-6 space-y-5">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de afiliación</label>
                            <input type="date" name="fecha_afiliacion" value="{{ old('fecha_afiliacion', now()->format('Y-m-d')) }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm bg-gray-100 text-gray-600 focus:outline-none transition cursor-not-allowed" required readonly>
                            <p class="text-xs text-gray-400 mt-1">Se asigna automáticamente la fecha actual del sistema.</p>
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
                    </div>

                    <div class="bg-teal-50 border border-teal-100 text-teal-800 text-sm rounded-md px-4 py-3">
                        El <strong>parentesco</strong> y la <strong>EPS</strong> se toman automáticamente de la persona
                        afiliada. Si la persona no tiene EPS, la afiliación queda con el <strong>NIT de tu empresa</strong>.
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

                    {{-- El estado no se elige aquí: una afiliación nueva nace Activa.
                         Se cambia después desde el panel de afiliaciones (Activar/Desactivar). --}}
                    <div class="bg-teal-50 border border-teal-100 text-teal-800 text-sm rounded-md px-4 py-3">
                        La afiliación se crea <strong>Activa</strong>. Después puedes desactivarla
                        desde el panel de Afiliaciones, con el botón <strong>Desactivar</strong>.
                    </div>

                </div>
            </div>

            {{-- Beneficiario de la afiliación (debajo de los datos de la afiliación) --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden h-fit">
                <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-5 text-white">
                    <p class="font-bold text-lg leading-tight">Beneficiario de la afiliación</p>
                    <p class="text-xs text-white/70">Afilia al cotizante o agrega un familiar como beneficiario</p>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Empresa: ya está registrada con su NIT, no se escribe a mano --}}
                    <div class="bg-white border border-teal-200 rounded-lg px-4 py-3">
                        <p class="text-xs text-gray-500">La afiliación se registra a nombre de tu empresa:</p>
                        @if (!empty($empresa['nit']))
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $empresa['nombre'] ?: 'Mi empresa' }}
                                <span class="text-gray-500 font-normal">· NIT {{ $empresa['nit'] }}</span>
                            </p>
                        @else
                            <p class="text-sm font-semibold text-gray-900">Mi empresa</p>
                            <p class="text-xs text-amber-600 mt-1">Tu empresa aún no tiene NIT registrado: completes tus datos en "Mi plan".</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Origen del beneficiario</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="border border-gray-200 rounded-lg p-3 text-sm font-medium cursor-pointer hover:bg-teal-50">
                                <input type="radio" name="origen_beneficiario" value="sin"
                                       class="mr-1" {{ old('origen_beneficiario', 'sin') === 'sin' ? 'checked' : '' }}>
                                Sin beneficiario
                            </label>
                            <label class="border border-gray-200 rounded-lg p-3 text-sm font-medium cursor-pointer hover:bg-teal-50">
                                <input type="radio" name="origen_beneficiario" value="nuevo"
                                       class="mr-1" {{ old('origen_beneficiario') === 'nuevo' ? 'checked' : '' }}>
                                Nuevo beneficiario
                            </label>
                        </div>
                    </div>

                    {{-- Sin beneficiario: solo se afilia al cotizante (titular) --}}
                    <div id="panel-sin" class="space-y-4">
                        <div class="bg-teal-50 border border-teal-100 text-teal-800 text-sm rounded-md px-4 py-3">
                            Se afilia únicamente al <strong>cotizante (titular)</strong> elegido arriba.
                            No se registra ningún beneficiario familiar.
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
                            <select name="nuevo_id_parentesco" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <option value="">Seleccione...</option>
                                @foreach ($parentescos as $p)
                                    <option value="{{ $p->id_parentesco }}" {{ (string) old('nuevo_id_parentesco') === (string) $p->id_parentesco ? 'selected' : '' }}>
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

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                            <input type="text" name="nuevo_telefono" value="{{ old('nuevo_telefono') }}" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
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
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de nacimiento <span class="text-red-500">*</span></label>
                                <input type="date" name="nuevo_fecha_nacimiento" value="{{ old('nuevo_fecha_nacimiento') }}" min="1900-01-01" max="{{ now()->toDateString() }}" required class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto <span class="text-gray-400 font-normal">(opcional)</span></label>
                            <input type="file" name="nuevo_foto" accept="image/*"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm shadow-sm bg-white file:mr-3 file:rounded-md file:border-0 file:bg-teal-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-teal-700 hover:file:bg-teal-100">
                        </div>

                        {{-- Datos adicionales del afiliado (solo para beneficiario nuevo) --}}
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
                                    <select name="id_pais_expedicion" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($paises as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ (string) old('id_pais_expedicion') === (string) $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-4">
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
                                    <input type="text" name="tipo_discapacidad" id="tipo-discapacidad-campo"
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

                            <div class="grid grid-cols-2 gap-4 mt-4">
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
                                    <select name="id_nivel_educativo" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($nivelesEducativos as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ (string) old('id_nivel_educativo') === (string) $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ocupación / Profesión</label>
                                    <select name="id_ocupacion" id="ocupacion-select" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="">Seleccione...</option>
                                        @foreach ($ocupaciones as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ (string) old('id_ocupacion') === (string) $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="ocupacion_otra" id="ocupacion-otra"
                                           placeholder="Escribe tu ocupación"
                                           value="{{ old('ocupacion_otra') }}"
                                           class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition hidden"
                                           {{ (string) old('id_ocupacion') === (string) $ocupacionOtroId ? '' : 'disabled' }}>
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
        const origenes = form.querySelectorAll('input[name="origen_beneficiario"]');
        const panelSin = document.getElementById('panel-sin');
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
            const origen = Array.from(origenes).find(r => r.checked)?.value || 'sin';
            const esNuevo = origen === 'nuevo';
            panelSin.classList.toggle('hidden', esNuevo);
            panelNuevo.classList.toggle('hidden', !esNuevo);

            // El cotizante del beneficiario se sincroniza con el usuario, no es editable.
            if (selCotizanteHidden) selCotizanteHidden.required = esNuevo;
            panelNuevo.querySelectorAll('input, select').forEach(el => {
                if (el.name && el.name.startsWith('nuevo_') && !el.name.endsWith('_foto')
                    && el.id !== 'nuevo-cotizante' && el.id !== 'nuevo-cotizante-hidden') {
                    el.required = esNuevo;
                }
                // Campos adicionales del afiliado: solo habilitados cuando es beneficiario nuevo
                if (el.name && !el.name.startsWith('nuevo_') && el.name !== '_token') {
                    el.disabled = !esNuevo;
                    if (!esNuevo) el.value = '';
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

        if (selUsuario) {
            selUsuario.addEventListener('change', sincronizarCotizanteNuevo);
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

        // Mostrar/ocultar campo "Tipo de discapacidad" según la opción seleccionada
        const selDiscapacidad = form.querySelector('select[name="tiene_discapacidad"]');
        const campoTipoDiscapacidad = document.getElementById('tipo-discapacidad-campo');
        if (selDiscapacidad && campoTipoDiscapacidad) {
            function toggleTipoDiscapacidad() {
                const esSi = selDiscapacidad.value === '1';
                campoTipoDiscapacidad.classList.toggle('hidden', !esSi);
                campoTipoDiscapacidad.disabled = !esSi;
                if (!esSi) campoTipoDiscapacidad.value = '';
            }
            selDiscapacidad.addEventListener('change', toggleTipoDiscapacidad);
            toggleTipoDiscapacidad(); // estado inicial
        }

        // Mostrar/ocultar campo "Ocupación otra" según la opción seleccionada
        const selOcupacion = document.getElementById('ocupacion-select');
        const campoOcupacionOtra = document.getElementById('ocupacion-otra');
        if (selOcupacion && campoOcupacionOtra) {
            function toggleOcupacionOtra() {
                const esOtro = String(selOcupacion.value) === '{{ $ocupacionOtroId }}';
                campoOcupacionOtra.classList.toggle('hidden', !esOtro);
                campoOcupacionOtra.disabled = !esOtro;
                if (!esOtro) campoOcupacionOtra.value = '';
            }
            selOcupacion.addEventListener('change', toggleOcupacionOtra);
            toggleOcupacionOtra(); // estado inicial
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