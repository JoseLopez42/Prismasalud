@extends('admin.layout')

@section('titulo', 'Roles')
@section('encabezado', 'Usuarios y Roles · Roles')

@section('contenido')

    <a href="{{ route('usuarios.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a usuarios
    </a>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mt-2 mb-5">
        <div>
            <h2 id="titulo-listado" class="text-xl font-bold text-gray-900">Roles</h2>
            <p class="text-sm text-gray-500">Tipos de usuario del sistema</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="boton-exportar-pdf" onclick="exportarPdf()"
                    data-titulo-pdf="Listado de Roles" data-nombre-archivo="roles"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-50 hover:shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar PDF
            </button>
            <a href="{{ route('roles.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 hover:shadow-md transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo rol
            </a>
        </div>
    </div>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">
            {{ session('exito') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Buscador rápido --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
        <div class="relative sm:w-80">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" id="buscador-listado" placeholder="Buscar por nombre del rol o cantidad de usuarios..."
                   class="w-full border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-sm bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
        </div>
        <span id="contador-resultados" class="text-xs text-gray-400 font-medium"></span>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto max-w-2xl">
        <table id="tabla-listado" class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Rol</th>
                    <th class="px-4 py-3">Usuarios</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- $roles viene con usuarios_count (withCount) --}}
                @forelse ($roles as $rol)
                    <tr class="fila-dato hover:bg-teal-50/40 transition {{ !$rol->activo ? 'bg-gray-50/60' : '' }}">
                        <td class="px-4 py-3 text-gray-500">#{{ $rol->id }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900">{{ $rol->nombre }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full">
                                {{ $rol->usuarios_count }} usuario(s)
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($rol->activo)
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @include('admin.partials.acciones_fila', [
                                'rutaEditar' => route('roles.edit', $rol->id),
                                'formId' => 'form-estado-rol-' . $rol->id,
                                'rutaEstado' => route('roles.estado', $rol->id),
                                'activo' => $rol->activo,
                                'tituloDesactivar' => '¿Desactivar este rol?',
                                'textoDesactivar' => 'Ya no podrá elegirse al crear o editar usuarios.',
                                'tituloActivar' => '¿Activar este rol?',
                                'textoActivar' => 'Volverá a estar disponible para usuarios.',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr class="fila-vacio">
                        <td colspan="5" class="px-4 py-16 text-center">
                            <div class="inline-flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-violet-50 flex items-center justify-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <p class="text-base font-semibold text-gray-700">Todavía no hay roles</p>
                                <p class="text-sm text-gray-400 mt-1 mb-5">Crea el primer rol para asignarlo a los usuarios.</p>
                                <a href="{{ route('roles.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Crear rol
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr id="fila-vacio-busqueda" class="fila-vacio oculto">
                        <td colspan="5" class="px-4 py-12 text-center">
                            <p class="text-sm font-semibold text-gray-500">Sin resultados</p>
                            <p class="text-xs text-gray-400 mt-1">No se encontraron roles con ese criterio de búsqueda.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('admin.partials.modal_confirmacion')

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
    @include('admin.partials.script_listado')
@endpush