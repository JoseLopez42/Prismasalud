@extends('usuario.layout')

@section('titulo', 'Mis citas')
@section('encabezado', 'Mis citas')

@section('contenido')

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Mis citas</h2>
            <p class="text-sm text-gray-500">Consulta el estado de tus citas programadas</p>
        </div>
        <a href="{{ route('usuario.citas.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 hover:shadow-md transition">
            @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-4 h-4'])
            Agendar cita
        </a>
    </div>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">{{ session('exito') }}</div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Horario</th>
                    <th class="px-4 py-3">Médico</th>
                    <th class="px-4 py-3">Consultorio</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($citas as $cita)
                    <tr class="hover:bg-teal-50/40 transition">
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-700">{{ $cita->fecha->format('d/m/Y') }}</span>
                            <span class="block text-xs text-gray-400 capitalize">{{ $cita->dia_semana }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ substr($cita->hora_inicio, 0, 5) }} – {{ substr($cita->hora_fin, 0, 5) }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-gray-900">
                                {{ $cita->medico ? 'Dr(a). ' . $cita->medico->nombres . ' ' . $cita->medico->apellidos : 'Sin asignar' }}
                            </span>
                            <span class="block text-xs text-gray-400">{{ $cita->medico->especialidad->nombre ?? '' }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $cita->consultorio->nombre ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full border border-teal-100">
                                {{ $cita->tipoCita->nombre ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                {{ $cita->estadoCita->nombre ?? 'Pendiente' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="inline-flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                                    @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-10 h-10 text-teal-400'])
                                </div>
                                <p class="text-base font-semibold text-gray-700">Todavía no tienes citas</p>
                                <p class="text-sm text-gray-400 mt-1">Agenda tu primera cita y quedará asignada a un médico.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
