@extends('usuario.layout')

@section('titulo', 'Mis medicamentos')
@section('encabezado', 'Mis medicamentos')

@section('contenido')

    <div class="mb-5">
        <h2 class="text-xl font-bold text-gray-900">Mis medicamentos</h2>
        <p class="text-sm text-gray-500">Prescripciones que los médicos te han formulado durante tus citas</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Medicamento</th>
                    <th class="px-4 py-3">Dosis</th>
                    <th class="px-4 py-3">Frecuencia</th>
                    <th class="px-4 py-3">Duración</th>
                    <th class="px-4 py-3">Médico</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($prescripciones as $prescripcion)
                    <tr class="hover:bg-teal-50/40 transition">
                        <td class="px-4 py-3 text-gray-700">{{ $prescripcion->fecha->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-gray-900">{{ $prescripcion->medicamento }}</span>
                            @if ($prescripcion->indicaciones)
                                <span class="block text-xs text-gray-400">{{ $prescripcion->indicaciones }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $prescripcion->dosis ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $prescripcion->frecuencia ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $prescripcion->duracion ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $prescripcion->medico ? 'Dr(a). ' . $prescripcion->medico->nombres . ' ' . $prescripcion->medico->apellidos : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="inline-flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                                    @include('admin.partials.icono', ['icono' => 'medicamentos', 'clase' => 'w-10 h-10 text-teal-400'])
                                </div>
                                <p class="text-base font-semibold text-gray-700">Aún no tienes medicamentos formulados</p>
                                <p class="text-sm text-gray-400 mt-1">Cuando un médico te formule un medicamento, aparecerá aquí.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
