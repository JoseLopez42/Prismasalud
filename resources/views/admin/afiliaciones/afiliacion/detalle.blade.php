@extends('admin.layout')

@section('titulo', 'Detalle de afiliación')
@section('encabezado', 'Afiliaciones · Detalle del núcleo familiar')

@section('contenido')
    <a href="{{ route('afiliaciones.index') }}" class="text-sm text-teal-600 hover:underline">← Volver a afiliaciones</a>

    <div class="mt-4 bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Cotizante: {{ $principal->usuario?->nombres }} {{ $principal->usuario?->apellidos }}</h2>
                <p class="text-sm text-gray-500">C.C. {{ $principal->cc_usuario }} · EPS: {{ $principal->eps?->nombre_eps ?? '—' }}</p>
                <p class="text-sm text-gray-500">Total de personas afiliadas a este núcleo: {{ $familia->count() }}</p>
            </div>
            <a href="{{ route('afiliaciones.create', ['cc_usuario' => $principal->cc_usuario]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 hover:shadow-md transition">
                + Agregar otro beneficiario
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3">C.C. Afiliado</th>
                        <th class="px-4 py-3">Persona afiliada</th>
                        <th class="px-4 py-3">Es</th>
                        <th class="px-4 py-3">Parentesco</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Régimen</th>
                        <th class="px-4 py-3">EPS</th>
                        <th class="px-4 py-3">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($familia as $afiliacion)
                        @php $esTitular = (string) $afiliacion->cc_afiliado === (string) $afiliacion->cc_usuario; @endphp
                        <tr class="hover:bg-teal-50/40 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $afiliacion->cc_afiliado }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">
                                {{ $afiliacion->beneficiario?->nombres }} {{ $afiliacion->beneficiario?->apellidos }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($esTitular)
                                    <span class="inline-flex text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full">Cotizante</span>
                                @else
                                    <span class="inline-flex text-xs font-semibold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full">Beneficiario</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $esTitular ? 'Titular' : ($afiliacion->parentesco ?: '—') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $afiliacion->fecha_afiliacion }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $afiliacion->tipoAfiliacion?->nombre_regimen }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $afiliacion->eps?->nombre_eps }}</td>
                            <td class="px-4 py-3">
                                @if ($afiliacion->id_estado == 1)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ $afiliacion->estado?->estado ?? 'Activo' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>{{ $afiliacion->estado?->estado ?? 'Inactivo' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500">No hay personas afiliadas a este núcleo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection