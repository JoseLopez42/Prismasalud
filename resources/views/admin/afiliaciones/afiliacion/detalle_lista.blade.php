@extends('admin.layout')

@section('titulo', 'Detalle de afiliación')
@section('encabezado', 'Afiliaciones · Detalle de afiliación')

@section('contenido')
    <a href="{{ route('afiliaciones.index') }}" class="text-sm text-teal-600 hover:underline">← Volver a afiliaciones</a>

    <div class="mt-4 bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900">Núcleos familiares con más de un afiliado</h2>
        <p class="text-sm text-gray-500 mt-1">Personas (cotizantes) que tienen varios beneficiarios afiliados a su cargo.</p>

        <div class="mt-4 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3">C.C. Cotizante</th>
                        <th class="px-4 py-3">Cotizante</th>
                        <th class="px-4 py-3">EPS</th>
                        <th class="px-4 py-3">Personas afiliadas</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($nucleos as $nucleo)
                        <tr class="hover:bg-teal-50/40 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $nucleo['cc_usuario'] }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">
                                {{ $nucleo['cotizante']?->nombres }} {{ $nucleo['cotizante']?->apellidos }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $nucleo['eps']?->nombre_eps ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $nucleo['total'] }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('afiliaciones.detalle', $nucleo['representante']->id_afiliacion) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 text-xs font-medium hover:bg-gray-50 hover:text-gray-800 hover:border-gray-300 transition">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-500">No hay núcleos con más de un afiliado todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection