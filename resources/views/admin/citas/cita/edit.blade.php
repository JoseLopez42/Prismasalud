@extends('admin.layout')

@section('titulo', 'Editar cita')
@section('encabezado', 'Citas Médicas · Editar cita')

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

    <a href="{{ route('cita.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a citas
    </a>

    <div class="max-w-2xl mt-4">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                    <div>
                        <p class="font-bold text-lg leading-tight">Editar cita médica</p>
                        <p class="text-xs text-white/70">
                            {{ $cita->fecha->format('d/m/Y') }} ({{ $cita->dia_semana }}) · {{ substr($cita->hora_inicio, 0, 5) }} – {{ substr($cita->hora_fin, 0, 5) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="form-cita" method="POST" action="{{ route('cita.update', $cita->id) }}" class="space-y-5" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Paciente *</label>
                            <select name="cedula_usuario"
                                    class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition"
                                    required>
                                @foreach ($pacientes as $paciente)
                                    <option value="{{ $paciente->cedula }}"
                                        {{ old('cedula_usuario', $cita->cedula_usuario) == $paciente->cedula ? 'selected' : '' }}>
                                        {{ $paciente->apellidos }}, {{ $paciente->nombres }} ({{ $paciente->nombre_rol }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Médico *</label>
                            <select name="cedula_medico"
                                    class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition"
                                    required>
                                @foreach ($medicos as $medico)
                                    <option value="{{ $medico->cedula }}"
                                        {{ old('cedula_medico', $cita->cedula_medico) == $medico->cedula ? 'selected' : '' }}>
                                        {{ $medico->apellidos }}, {{ $medico->nombres }} ({{ $medico->especialidad->nombre ?? 'sin especialidad' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Consultorio *</label>
                            <select name="id_consultorio"
                                    class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition"
                                    required>
                                @foreach ($consultorios as $consultorio)
                                    <option value="{{ $consultorio->id }}"
                                        {{ old('id_consultorio', $cita->id_consultorio) == $consultorio->id ? 'selected' : '' }}>
                                        {{ $consultorio->nombre }}{{ $consultorio->sede ? ' · ' . $consultorio->sede->nombre_sede : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de cita *</label>
                            <select name="id_tipo_cita"
                                    class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition"
                                    required>
                                @foreach ($tiposCita as $tipo)
                                    <option value="{{ $tipo->id }}"
                                        {{ old('id_tipo_cita', $cita->id_tipo_cita) == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de la cita *</label>
                        <input type="date" name="fecha" value="{{ old('fecha', $cita->fecha->format('Y-m-d')) }}"
                               class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                               required>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Hora de inicio *</label>
                            <input type="time" name="hora_inicio" value="{{ old('hora_inicio', substr($cita->hora_inicio, 0, 5)) }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Hora de finalización *</label>
                            <input type="time" name="hora_fin" value="{{ old('hora_fin', substr($cita->hora_fin, 0, 5)) }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado de la cita *</label>
                            <select name="id_estado_cita"
                                    class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition"
                                    required>
                                @foreach ($estadosCita as $estado)
                                    <option value="{{ $estado->id }}"
                                        {{ old('id_estado_cita', $cita->id_estado_cita) == $estado->id ? 'selected' : '' }}>
                                        {{ $estado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Motivo <span class="text-gray-400 font-normal">(opcional)</span></label>
                            <input type="text" name="motivo" value="{{ old('motivo', $cita->motivo) }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   placeholder="Ej: Dolor de cabeza" maxlength="255">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('cita.index') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar-cita"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton-cita" class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Guardar cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const formCita = document.getElementById('form-cita');
        const botonGuardarCita = document.getElementById('boton-guardar-cita');
        const textoBotonCita = document.getElementById('texto-boton-cita');

        formCita.addEventListener('submit', function (evento) {
            const camposInvalidos = Array.from(formCita.querySelectorAll('.campo-form'))
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

            const inicio = formCita.hora_inicio.value;
            const fin = formCita.hora_fin.value;
            if (inicio && fin && fin <= inicio) {
                evento.preventDefault();
                const campoFin = formCita.hora_fin;
                campoFin.classList.add('campo-invalido');
                campoFin.addEventListener('animationend', () => campoFin.classList.remove('campo-invalido'), { once: true });
                campoFin.focus();
                return;
            }

            botonGuardarCita.disabled = true;
            textoBotonCita.innerHTML = '<span class="spinner"></span> Guardando...';
        });
    </script>

@endsection