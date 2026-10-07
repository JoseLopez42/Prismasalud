@extends('superadmin.layout')
@section('titulo', 'Administradores')

@section('contenido')
    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Administradores</h1>
            <p class="text-sm text-gray-500 mt-1">Las personas que manejan cada empresa. Tú los creas y ellos entran por el login normal.</p>
        </div>
        <a href="{{ route('superadmin.administradores.create', $empresa !== '' && $empresa !== 'sin' ? ['empresa' => $empresa] : []) }}"
           class="rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-4 py-2.5 transition">+ Nuevo administrador</a>
    </div>

    <form method="GET" action="{{ route('superadmin.administradores.index') }}" class="bg-white rounded-xl border border-gray-200 p-4 mb-5 flex flex-wrap gap-3">
        <input type="search" name="q" value="{{ $busqueda }}" placeholder="Buscar por nombre, correo o cédula…"
               class="flex-1 min-w-[200px] rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
        <select name="empresa" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 max-w-[220px]">
            <option value="">Todas las empresas</option>
            <option value="sin" @selected($empresa === 'sin')>Sin empresa</option>
            @foreach ($empresas as $e)
                <option value="{{ $e->id }}" @selected((string) $empresa === (string) $e->id)>{{ $e->nombre_ips }}</option>
            @endforeach
        </select>
        <select name="estado" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
            <option value="">Activos e inactivos</option>
            <option value="activo" @selected($estado === 'activo')>Solo activos</option>
            <option value="inactivo" @selected($estado === 'inactivo')>Solo inactivos</option>
        </select>
        <button type="submit" class="rounded-lg bg-prisma-azul hover:bg-prisma-turquesa text-white text-sm font-bold px-4 py-2 transition">Buscar</button>
        @if ($busqueda !== '' || $empresa !== '' || $estado !== '')
            <a href="{{ route('superadmin.administradores.index') }}" class="rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-600 text-sm font-bold px-4 py-2 transition">Limpiar</a>
        @endif
    </form>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if ($administradores->isEmpty())
            <div class="px-5 py-14 text-center">
                <p class="text-sm font-semibold text-gray-700">No hay administradores para mostrar.</p>
                <p class="text-sm text-gray-500 mt-1">
                    {{ ($busqueda !== '' || $empresa !== '' || $estado !== '') ? 'Prueba con otra búsqueda o limpia los filtros.' : 'Crea el primero con el botón "Nuevo administrador".' }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="px-5 py-3">Administrador</th>
                            <th class="px-5 py-3">Cédula</th>
                            <th class="px-5 py-3">Empresa</th>
                            <th class="px-5 py-3">Estado</th>
                            <th class="px-5 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($administradores as $a)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 shrink-0 rounded-full bg-prisma-azul text-white text-xs font-extrabold flex items-center justify-center">
                                            {{ mb_strtoupper(mb_substr($a->nombres, 0, 1) . mb_substr($a->apellidos, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 truncate">{{ $a->nombres }} {{ $a->apellidos }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ $a->correo }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $a->cedula }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $a->nombre_ips ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    @if ($a->activo)
                                        <span class="inline-block rounded-full bg-teal-50 text-teal-700 text-xs font-bold px-2.5 py-1">Activo</span>
                                    @else
                                        <span class="inline-block rounded-full bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('superadmin.administradores.edit', $a->cedula) }}"
                                           class="rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1.5 transition">Editar</a>
                                        <form method="POST" action="{{ route('superadmin.administradores.estado', $a->cedula) }}"
                                              onsubmit="return confirm('{{ $a->activo ? '¿Desactivar a este administrador? Ya no podrá entrar.' : '¿Activar a este administrador?' }}');">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="rounded-lg text-xs font-bold px-3 py-1.5 transition {{ $a->activo ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-teal-50 text-teal-700 hover:bg-teal-100' }}">
                                                {{ $a->activo ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($administradores->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $administradores->links() }}</div>
            @endif
        @endif
    </section>
@endsection