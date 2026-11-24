@extends('admin.layout')

@section('titulo', 'Nuevo procedimiento')
@section('encabezado', 'Procedimientos · Nuevo procedimiento')

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

    <a href="{{ route('procedimiento.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a procedimientos
    </a>

    <div class="max-w-2xl mt-4">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center">
                        @include('admin.partials.icono', ['icono' => 'procedimientos', 'clase' => 'w-6 h-6 text-white'])
                    </div>
                    <div>
                        <p class="font-bold text-lg leading-tight">Nuevo procedimiento</p>
                        <p class="text-xs text-white/70">Registra el procedimiento realizado a un paciente</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if (isset($errors) && $errors->any())
                    <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="form-procedimiento" method="POST" action="{{ route('procedimiento.store') }}" class="space-y-5" novalidate>
                    @csrf

                    <div>
                        <label for="id_cita" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Cita programada <span class="text-gray-400 font-normal">(opcional)</span>
                        </label>
                        <select id="id_cita" name="id_cita"
                                class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition">
                            <option value="">Sin cita asociada</option>
                            @foreach ($citas as $cita)
                                <option value="{{ $cita->id }}"
                                        data-paciente="{{ $cita->cedula_usuario }}"
                                        data-medico="{{ $cita->cedula_medico }}"
                                        data-fecha="{{ $cita->fecha ? $cita->fecha->format('Y-m-d') : '' }}"
                                        {{ old('id_cita') == $cita->id ? 'selected' : '' }}>
                                    #{{ $cita->id }} · {{ $cita->fecha ? $cita->fecha->format('d/m/Y') : '' }}
                                    · {{ trim(($cita->paciente->nombres ?? '') . ' ' . ($cita->paciente->apellidos ?? '')) ?: 'Paciente' }}
                                    · {{ trim(($cita->medico->nombres ?? '') . ' ' . ($cita->medico->apellidos ?? '')) ?: 'Médico' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Al elegir una cita se completan automáticamente el paciente, el médico y la fecha.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="cedula_paciente" class="block text-sm font-medium text-gray-700 mb-1.5">Paciente *</label>
                            <select id="cedula_paciente" name="cedula_paciente" required
                                    class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition">
                                <option value="">Seleccione el paciente</option>
                                @foreach ($pacientes as $paciente)
                                    <option value="{{ $paciente->cedula }}" {{ old('cedula_paciente') == $paciente->cedula ? 'selected' : '' }}>
                                        {{ $paciente->nombres }} {{ $paciente->apellidos }} · C.C. {{ $paciente->cedula }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="cedula_medico" class="block text-sm font-medium text-gray-700 mb-1.5">Médico *</label>
                            <select id="cedula_medico" name="cedula_medico" required
                                    class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition">
                                <option value="">Seleccione el médico</option>
                                @foreach ($medicos as $medico)
                                    <option value="{{ $medico->cedula }}" {{ old('cedula_medico') == $medico->cedula ? 'selected' : '' }}>
                                        {{ $medico->nombres }} {{ $medico->apellidos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="id_tipo_procedimiento" class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de procedimiento *</label>
                            <div class="flex gap-2">
                                <select id="id_tipo_procedimiento" name="id_tipo_procedimiento" required
                                        class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition">
                                    <option value="">Seleccione el tipo</option>
                                    @foreach ($tipos as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('id_tipo_procedimiento') == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('tipo_procedimiento.create') }}" target="_blank" rel="noopener"
                                   title="Crear otro tipo de procedimiento"
                                   class="flex-shrink-0 inline-flex items-center justify-center w-11 rounded-lg border border-gray-300 text-teal-600 hover:bg-teal-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                </a>
                            </div>
                        </div>

                        <div>
                            <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1.5">Fecha del procedimiento *</label>
                            <input type="date" id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1.5">Procedimiento *</label>
                        <select id="descripcion" name="descripcion" required
                                class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition">
                            <option value="">Seleccione el procedimiento</option>
                            @foreach ($catalogo->groupBy('tipoProcedimiento.nombre') as $categoria => $items)
                                <optgroup label="{{ $categoria }}">
                                    @foreach ($items as $item)
                                        <option value="{{ $item->nombre }}" {{ old('descripcion') == $item->nombre ? 'selected' : '' }}>
                                            {{ $item->nombre }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Listado cargado desde la base de datos, agrupado por tipo de procedimiento.</p>
                    </div>

                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1.5">Observaciones <span class="text-gray-400 font-normal">(opcional)</span></label>
                        <textarea id="observaciones" name="observaciones" rows="3"
                                  class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                  placeholder="Notas adicionales sobre el procedimiento...">{{ old('observaciones') }}</textarea>
                    </div>

                    <label class="flex items-center gap-2.5 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-lg px-3 py-3 cursor-pointer">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        Procedimiento activo
                    </label>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('procedimiento.index') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar-procedimiento"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton-procedimiento" class="inline-flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Crear procedimiento
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const formProcedimiento = document.getElementById('form-procedimiento');
        const botonGuardarProcedimiento = document.getElementById('boton-guardar-procedimiento');
        const textoBotonProcedimiento = document.getElementById('texto-boton-procedimiento');
        const selectorCita = document.getElementById('id_cita');

        selectorCita.addEventListener('change', function () {
            const opcion = selectorCita.options[selectorCita.selectedIndex];

            if (!opcion || !opcion.value) {
                return;
            }

            document.getElementById('cedula_paciente').value = opcion.dataset.paciente || '';
            document.getElementById('cedula_medico').value = opcion.dataset.medico || '';

            if (opcion.dataset.fecha) {
                document.getElementById('fecha').value = opcion.dataset.fecha;
            }
        });

        formProcedimiento.addEventListener('submit', function (evento) {
            const camposInvalidos = Array.from(formProcedimiento.querySelectorAll('.campo-form'))
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

            botonGuardarProcedimiento.disabled = true;
            textoBotonProcedimiento.innerHTML = '<span class="spinner"></span> Guardando...';
        });
    </script>

@endsection
