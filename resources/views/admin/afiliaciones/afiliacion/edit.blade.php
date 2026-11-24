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

                    {{-- Empresa: ya está registrada con su NIT, no se escribe a mano --}}
                    <div class="bg-white border border-teal-200 rounded-lg px-4 py-3">
                        <p class="text-xs text-gray-500">La afiliación está registrada a nombre de tu empresa:</p>
                        @if (!empty($empresa['nit']))
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $empresa['nombre'] ?: 'Mi empresa' }}
                                <span class="text-gray-500 font-normal">· NIT {{ $empresa['nit'] }}</span>
                            </p>
                        @else
                            <p class="text-sm font-semibold text-gray-900">Mi empresa</p>
                            <p class="text-xs text-amber-600 mt-1">Tu empresa aún no tiene NIT registrado: completa tus datos en "Mi plan".</p>
                        @endif
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
                        @php
                            // Si la afiliación es del propio cotizante, se muestra
                            // la opción "sin beneficiario".
                            $selAfiliado = old('cc_afiliado', $afiliacion->cc_afiliado == $afiliacion->cc_usuario ? '' : $afiliacion->cc_afiliado);
                        @endphp
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Beneficiario</label>
                        <select name="cc_afiliado" class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            <option value="" {{ $selAfiliado === '' ? 'selected' : '' }}>Sin beneficiario (solo el cotizante)</option>
                            @foreach ($beneficiarios as $b)
                                <option value="{{ $b->cc_afiliado }}"
                                        data-cotizante="{{ $b->cc_cotizante ?? $b->cc_afiliado }}"
                                        {{ $selAfiliado == $b->cc_afiliado ? 'selected' : '' }}>
                                    {{ $b->nombres }} {{ $b->apellidos }} ({{ $b->cc_afiliado }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Solo se cambia quién queda afiliado en este registro. Los datos de la persona se editan en su propio módulo.</p>
                    </div>

                    {{-- El estado no se edita aquí: se cambia desde el panel de
                         afiliaciones con los botones Activar / Desactivar. --}}
                    <div class="bg-teal-50 border border-teal-100 text-teal-800 text-sm rounded-md px-4 py-3">
                        Estado actual: <strong>{{ $afiliacion->estado?->estado ?? 'Activo' }}</strong>.
                        Para cambiarlo usa el botón <strong>Activar/Desactivar</strong> del panel de Afiliaciones.
                    </div>

                    {{-- Aquí solo se editan datos de la afiliación.
                         Los datos del usuario, del beneficiario y los datos personales
                         del afiliado (documento, género, etc.) se editan en sus
                         propios módulos. --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 mb-5 text-sm text-gray-600">
                        Aquí solo se editan los datos de la <strong>afiliación</strong>.
                        Los datos del afiliado (documento, género, etc.) ya están registrados:
                        para cambiarlos edita la ficha de la persona en su propio módulo.
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

        // Beneficiario: la primera opción es "sin beneficiario" (solo el
        // cotizante). Los demás son los familiares del usuario (cotizante)
        // elegido, más el propio cotizante (que es el titular).
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
            base.textContent = 'Sin beneficiario (solo el cotizante)';
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