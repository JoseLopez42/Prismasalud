@extends('medico.layout')

@section('titulo', 'Inicio')
@section('encabezado', 'Panel del Médico')

@section('contenido')

    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">
        Hola, Dr(a). {{ $medico->nombres ?? '' }} {{ $medico->apellidos ?? '' }}
    </h2>
    <p class="text-sm text-gray-500 mb-6">
        {{ $medico->especialidad->nombre ?? 'Sin especialidad' }} · Citas programadas para tu consulta
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        @foreach ($resumen as $item)
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 mb-3">
                    @include('admin.partials.icono', ['icono' => $item['icono'], 'clase' => 'w-5 h-5'])
                </div>
                <p class="text-2xl font-extrabold text-gray-900">{{ $item['numero'] }}</p>
                <p class="text-xs text-gray-500">{{ $item['texto'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h3 class="text-base font-bold text-gray-900">Mis citas programadas</h3>
            <p class="text-sm text-gray-500">Revisa al paciente y abre su historia médica desde aquí</p>
        </div>
        <a href="{{ route('medico.historia.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 hover:shadow-md transition">
            @include('admin.partials.icono', ['icono' => 'historia', 'clase' => 'w-4 h-4'])
            Buscar historia por cédula
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Paciente</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Horario</th>
                    <th class="px-4 py-3">Consultorio</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($citas as $cita)
                    <tr class="hover:bg-teal-50/40 transition {{ !$cita->activo ? 'bg-gray-50/60' : '' }}">                        <td class="px-4 py-3">
                            <span class="font-semibold text-gray-900">
                                {{ $cita->paciente ? $cita->paciente->nombres . ' ' . $cita->paciente->apellidos : 'Paciente no encontrado' }}
                            </span>
                            <span class="block text-xs text-gray-400">C.C. {{ $cita->cedula_usuario }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-700">{{ $cita->fecha->format('d/m/Y') }}</span>
                            <span class="block text-xs text-gray-400 capitalize">{{ $cita->dia_semana }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ substr($cita->hora_inicio, 0, 5) }} – {{ substr($cita->hora_fin, 0, 5) }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $cita->consultorio->nombre ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full border border-teal-100">
                                {{ $cita->tipoCita->nombre ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $cita->activo ? 'text-green-700 bg-green-50' : 'text-gray-500 bg-gray-100' }} px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full {{ $cita->activo ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $cita->estadoCita->nombre ?? ($cita->activo ? 'Activa' : 'Inactiva') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex flex-wrap justify-end gap-2">
                                <a href="{{ route('medico.cita.show', $cita->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 text-white border border-green-600 text-xs font-semibold hover:bg-green-700 transition">
                                    @include('admin.partials.icono', ['icono' => 'historia', 'clase' => 'w-4 h-4'])
                                    Atender cita
                                </a>
                                <a href="{{ route('medico.procedimiento.index', ['cedula' => $cita->cedula_usuario, 'cita' => $cita->id]) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-semibold hover:bg-indigo-600 hover:text-white transition">
                                    @include('admin.partials.icono', ['icono' => 'procedimientos', 'clase' => 'w-4 h-4'])
                                    Historial
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <div class="inline-flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                                    @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-10 h-10 text-teal-400'])
                                </div>
                                <p class="text-base font-semibold text-gray-700">Todavía no tienes citas programadas</p>
                                <p class="text-sm text-gray-400 mt-1">Cuando el administrador te asigne una cita, aparecerá aquí.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
