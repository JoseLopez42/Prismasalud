@extends('farmacia.layout')

@section('titulo', 'Inventario')
@section('encabezado', 'Farmacia · Inventario')

@section('contenido')

    @if (session('exito'))
        <div class="flex items-center gap-2 bg-green-50 text-green-700 text-sm rounded-xl px-4 py-3 mb-5 border border-green-100">
            @include('admin.partials.icono', ['icono' => 'inventario', 'clase' => 'w-5 h-5 flex-shrink-0'])
            {{ session('exito') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 text-red-700 text-sm rounded-xl px-4 py-3 mb-5 border border-red-100">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Inventario de medicamentos</h2>
            <p class="text-sm text-gray-500">Registra cuántas unidades hay disponibles de cada medicamento</p>
        </div>
    </div>

    {{-- Buscador --}}
    <form method="GET" action="{{ route('farmacia.inventario.index') }}" class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="relative sm:max-w-md w-full">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por código, nombre o presentación..."
                   class="w-full border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-sm bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
        </div>
        <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition">
            Buscar
        </button>
        @if ($buscar !== '')
            <a href="{{ route('farmacia.inventario.index') }}"
               class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                Limpiar
            </a>
        @endif
    </form>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Código</th>
                        <th class="px-4 py-3">Medicamento</th>
                        <th class="px-4 py-3">Stock actual</th>
                        <th class="px-4 py-3 text-right">Registrar stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($medicamentos as $medicamento)
                        <tr class="hover:bg-teal-50/40 transition">
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center font-mono text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-md border border-teal-100">
                                    {{ $medicamento->codigo }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900">{{ $medicamento->nombre }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $medicamento->presentacion ?: 'Sin presentación' }}
                                    @if ($medicamento->descripcion) · {{ \Illuminate\Support\Str::limit($medicamento->descripcion, 60) }} @endif
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                @if ($medicamento->stock > 0)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ $medicamento->stock }} unidades
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Sin stock
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('farmacia.inventario.stock', $medicamento->codigo) }}"
                                      class="flex items-center justify-end gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="stock" min="0" max="1000000" value="{{ $medicamento->stock }}"
                                           class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition">
                                        Guardar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-16 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <div class="w-20 h-20 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                                        @include('admin.partials.icono', ['icono' => 'inventario', 'clase' => 'w-10 h-10 text-teal-400'])
                                    </div>
                                    <p class="text-base font-semibold text-gray-700">No hay medicamentos en el inventario</p>
                                    <p class="text-sm text-gray-400 mt-1">El administrador debe crear los medicamentos en el catálogo.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($medicamentos->hasPages())
        <div class="mt-5">
            {{ $medicamentos->links() }}
        </div>
    @endif

@endsection
