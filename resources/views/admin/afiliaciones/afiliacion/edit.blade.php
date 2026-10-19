@extends('admin.layout')

@section('titulo', 'Editar afiliación')
@section('encabezado', 'Afiliaciones · Editar afiliación')

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

    <a href="{{ route('afiliaciones.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a afiliaciones
    </a>

    <div class="max-w-2xl mt-4">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <p class="font-bold text-lg leading-tight">Editar afiliación</p>
                <p class="text-xs text-white/70">#{{ $afiliacion->id_afiliacion }}</p>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="form-afiliacion" method="POST" action="{{ route('afiliaciones.update', $afiliacion->id_afiliacion) }}" class="space-y-5" novalidate>
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de afiliación</label>
                        <input type="date" name="fecha_afiliacion" value="{{ old('fecha_afiliacion', $afiliacion->fecha_afiliacion) }}"
                               class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                    </div>

                    <div class="bg-teal-50 border border-teal-100 text-teal-800 text-sm rounded-md px-4 py-3">
                        El <strong>parentesco</strong> y la <strong>EPS</strong> se toman automáticamente de la persona
                        registrada como beneficiario (la EPS es la de su cotizante).
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de afiliación</label>
                        <select name="id_tipo_afiliacion" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id_regimen }}" {{ old('id_tipo_afiliacion', $afiliacion->id_tipo_afiliacion) == $tipo->id_regimen ? 'selected' : '' }}>
                                    {{ $tipo->nombre_regimen }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Usuario</label>
                        <select name="cc_usuario" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->cedula }}" {{ old('cc_usuario', $afiliacion->cc_usuario) == $u->cedula ? 'selected' : '' }}>
                                    {{ $u->nombres }} {{ $u->apellidos }} ({{ $u->cedula }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Beneficiario</label>
                        <select name="cc_afiliado" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            @foreach ($beneficiarios as $b)
                                <option value="{{ $b->cc_afiliado }}" data-cotizante="{{ $b->cc_cotizante ?? $b->cc_afiliado }}" {{ old('cc_afiliado', $afiliacion->cc_afiliado) == $b->cc_afiliado ? 'selected' : '' }}>
                                    {{ $b->nombres }} {{ $b->apellidos }} ({{ $b->cc_afiliado }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado</label>
                        <select name="id_estado" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            @foreach ($estados as $es)
                                <option value="{{ $es->id_estado }}" {{ old('id_estado', $afiliacion->id_estado) == $es->id_estado ? 'selected' : '' }}>
                                    {{ $es->estado }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Datos del cotizante (titular de la afiliación) --}}
                    <div class="bg-teal-50 border border-teal-100 text-teal-800 text-sm rounded-md px-4 py-3 mb-5">
                        <p class="font-bold mb-2">Datos del cotizante (titular)</p>
                        <p class="text-xs mb-3">Edita los datos personales de quien es el titular de esta afiliación.</p>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombres</label>
                                <input type="text" name="nombres_cotizante"
                                       value="{{ old('nombres_cotizante', $afiliacion->usuario->nombres ?? '') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellidos</label>
                                <input type="text" name="apellidos_cotizante"
                                       value="{{ old('apellidos_cotizante', $afiliacion->usuario->apellidos ?? '') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Correo</label>
                                <input type="email" name="correo_cotizante"
                                       value="{{ old('correo_cotizante', $afiliacion->usuario->correo ?? '') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                                <input type="text" name="telefono_cotizante"
                                       value="{{ old('telefono_cotizante', $afiliacion->usuario->telefono ?? '') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            </div>
                        </div>
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
                                        <option value="{{ $td->id_tipo_documento }}" {{ old('id_tipo_documento', $afiliacion->id_tipo_documento) == $td->id_tipo_documento ? 'selected' : '' }}>
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
                                        <option value="{{ $valor }}" {{ old('pais_expedicion', $afiliacion->pais_expedicion ?? '') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
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
                                        <option value="{{ $valor }}" {{ old('genero', $afiliacion->genero ?? '') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado civil</label>
                                <select name="estado_civil" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach ($estadosCiviles as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('estado_civil', $afiliacion->estado_civil ?? '') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
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
                                        <option value="{{ $valor }}" {{ old('grupo_etnico', $afiliacion->grupo_etnico ?? '') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">¿Tiene discapacidad?</label>
                                <select name="tiene_discapacidad" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="0" {{ (old('tiene_discapacidad', $afiliacion->tiene_discapacidad ?? false) ? '1' : '0') === '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('tiene_discapacidad', $afiliacion->tiene_discapacidad ?? false) ? 'selected' : '' }}>Sí</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de discapacidad</label>
                                <input type="text" name="tipo_discapacidad" id="tipo-discapacidad-campo"
                                       placeholder="Solo si tiene discapacidad"
                                       value="{{ old('tipo_discapacidad', $afiliacion->tipo_discapacidad ?? '') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition {{ (old('tiene_discapacidad', $afiliacion->tiene_discapacidad ?? false) ? '1' : '0') === '1' ? '' : 'hidden' }}"
                                       {{ (old('tiene_discapacidad', $afiliacion->tiene_discapacidad ?? false) ? '1' : '0') === '1' ? '' : 'disabled' }}>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nacionalidad</label>
                                <select name="nacionalidad" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach ($paises as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('nacionalidad', $afiliacion->nacionalidad ?? '') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">País de nacimiento</label>
                                <input type="text" name="pais_nacimiento" value="{{ old('pais_nacimiento', $afiliacion->pais_nacimiento ?? 'Colombia') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Zona de residencia</label>
                                <select name="zona_residencia" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach ($zonasResidencia as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('zona_residencia', $afiliacion->zona_residencia ?? '') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono fijo</label>
                                <input type="text" name="telefono_fijo" value="{{ old('telefono_fijo', $afiliacion->telefono_fijo ?? '') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Estrato</label>
                                <select name="estrato" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach ($estratos as $e)
                                        <option value="{{ $e }}" {{ old('estrato', $afiliacion->estrato ?? '') == $e ? 'selected' : '' }}>Estrato {{ $e }}</option>
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
                                        <option value="{{ $valor }}" {{ old('nivel_educativo', $afiliacion->nivel_educativo ?? '') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ocupación / Profesión</label>
                                <select name="ocupacion" id="ocupacion-select" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach ($ocupaciones as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('ocupacion', $afiliacion->ocupacion ?? '') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="ocupacion_otra" id="ocupacion-otra"
                                       placeholder="Escribe tu ocupación"
                                       value="{{ old('ocupacion_otra', ($afiliacion->ocupacion ?? '') === 'Otro' ? '' : $afiliacion->ocupacion) }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition {{ (old('ocupacion', $afiliacion->ocupacion ?? '') === 'Otro') ? '' : 'hidden' }}"
                                       {{ (old('ocupacion', $afiliacion->ocupacion ?? '') === 'Otro') ? '' : 'disabled' }}>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de sangre</label>
                                <select name="tipo_sangre" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach ($tiposSangre as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('tipo_sangre', $afiliacion->tipo_sangre ?? '') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">¿Víctima del conflicto armado?</label>
                                <select name="victima_conflicto_armado" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="0" {{ (old('victima_conflicto_armado', $afiliacion->victima_conflicto_armado ?? false) ? '1' : '0') === '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('victima_conflicto_armado', $afiliacion->victima_conflicto_armado ?? false) ? 'selected' : '' }}>Sí</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Número de afiliación anterior</label>
                            <input type="text" name="numero_afiliacion_anterior" value="{{ old('numero_afiliacion_anterior', $afiliacion->numero_afiliacion_anterior ?? '') }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('afiliaciones.index') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton" class="inline-flex items-center gap-1.5">Guardar cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('form-afiliacion');
        const boton = document.getElementById('boton-guardar');
        const texto = document.getElementById('texto-boton');

        // Beneficiario: solo salen los familiares del usuario (cotizante) elegido
        // (y el propio cotizante, que es el titular).
        const selUsuario = form.querySelector('select[name="cc_usuario"]');
        const selBenef = form.querySelector('select[name="cc_afiliado"]');
        const opcionesBenef = Array.from(selBenef.querySelectorAll('option[data-cotizante]'))
            .map(o => ({ valor: o.value, texto: o.textContent.trim(), cotizante: o.dataset.cotizante }));
        let seleccionado = selBenef.value;

        function filtrarBeneficiarios() {
            seleccionado = selBenef.value || seleccionado;
            const usuario = selUsuario.value;
            const lista = usuario ? opcionesBenef.filter(o => o.cotizante === usuario) : [];

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

        selUsuario.addEventListener('change', function () { seleccionado = ''; filtrarBeneficiarios(); });
        filtrarBeneficiarios();

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
            toggleTipoDiscapacidad();
        }

        // Mostrar/ocultar campo "Ocupación otra" según la opción seleccionada
        const selOcupacion = document.getElementById('ocupacion-select');
        const campoOcupacionOtra = document.getElementById('ocupacion-otra');
        if (selOcupacion && campoOcupacionOtra) {
            function toggleOcupacionOtra() {
                const esOtro = selOcupacion.value === 'Otro';
                campoOcupacionOtra.classList.toggle('hidden', !esOtro);
                campoOcupacionOtra.disabled = !esOtro;
                if (!esOtro) campoOcupacionOtra.value = '';
            }
            selOcupacion.addEventListener('change', toggleOcupacionOtra);
            toggleOcupacionOtra();
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