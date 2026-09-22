@extends('admin.layout')

@section('titulo', 'Roles')
@section('encabezado', 'Usuarios y Roles · Roles')

@section('contenido')

    <a href="{{ route('usuarios.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a usuarios
    </a>

    <div class="flex items-center justify-between mt-2 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Roles</h2>
            <p class="text-sm text-gray-500">Tipos de usuario del sistema</p>
        </div>
        <a href="{{ route('roles.create') }}"
           class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
            + Nuevo rol
        </a>
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

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden max-w-xl">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Rol</th>
                    <th class="px-4 py-3">Usuarios</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                {{-- $roles viene con usuarios_count (withCount) --}}
                @forelse ($roles as $rol)
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">#{{ $rol->id }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $rol->nombre }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full">
                                {{ $rol->usuarios_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('roles.edit', $rol->id) }}"
                               class="text-teal-600 hover:underline font-medium">Editar</a>

                            <form action="{{ route('roles.destroy', $rol->id) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('¿Eliminar este rol?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-medium">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-400">
                            Todavía no hay roles registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection