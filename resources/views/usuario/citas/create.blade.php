@extends('usuario.layout')

@section('titulo', 'Agendar cita')
@section('encabezado', 'Agendar cita')

@section('contenido')

    <div class="mb-5">
        <h2 class="text-xl font-bold text-gray-900">Agendar una nueva cita</h2>
        <p class="text-sm text-gray-500">Selecciona el médico que te atenderá. El consultorio se asigna automáticamente.</p>
    </div>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">{{ session('exito') }}</div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 md:p-6 max-w-3xl">
        <form method="POST" action="{{ route('usuario.citas.store') }}" class="space-y-5">
            @csrf

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de cita *</label>
                    <select name="id_tipo_cita" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        <option value="">Selecciona un tipo...</option>
                        @foreach ($tiposCita as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('id_tipo_cita') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Médico *</label>
                    <select name="cedula_medico" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        <option value="">Selecciona un médico...</option>
                        @foreach ($medicos as $medico)
                            <option value="{{ $medico->cedula }}" {{ old('cedula_medico') == $medico->cedula ? 'selected' : '' }}>
                                Dr(a). {{ $medico->nombres }} {{ $medico->apellidos }} — {{ $medico->especialidad->nombre ?? 'Sin especialidad' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha *</label>
                    <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Hora de inicio *</label>
                    <input type="time" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Hora de fin *</label>
                    <input type="time" name="hora_fin" id="hora_fin" value="{{ old('hora_fin') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Motivo de la consulta <span class="text-gray-400 font-normal">(opcional)</span></label>
                <textarea name="motivo" rows="3" maxlength="255"
                          placeholder="Cuéntanos brevemente el motivo de tu cita"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('motivo') }}</textarea>
            </div>

            <div class="bg-teal-50 border border-teal-100 rounded-xl px-4 py-3 text-xs text-teal-700">
                Tu cita quedará <strong>asignada directamente</strong> al médico seleccionado en estado <strong>Pendiente</strong>.
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 hover:shadow-md transition">
                    @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-4 h-4'])
                    Agendar cita
                </button>
                <a href="{{ route('usuario.citas.index') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                    Ver mis citas
                </a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script>
        // Al elegir la hora de inicio, propone la hora de fin 30 minutos después
        const horaInicio = document.getElementById('hora_inicio');
        const horaFin = document.getElementById('hora_fin');

        horaInicio.addEventListener('change', function () {
            if (!horaInicio.value || horaFin.value) return;

            const [h, m] = horaInicio.value.split(':').map(Number);
            const fin = new Date();
            fin.setHours(h, m + 30, 0, 0);

            horaFin.value = String(fin.getHours()).padStart(2, '0') + ':' + String(fin.getMinutes()).padStart(2, '0');
        });
    </script>
@endpush
