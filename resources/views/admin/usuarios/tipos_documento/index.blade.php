@extends('admin.layout')

@section('titulo', 'Tipos de documento')
@section('encabezado', 'Usuarios y Roles · Tipos de documento')

@section('contenido')

    <a href="{{ route('usuarios.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a usuarios
    </a>

    <div class="flex items-center justify-between mt-2 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Tipos de documento</h2>
            <p class="text-sm text-gray-500">Se usan al registrar un usuario</p>
        </div>
        <a href="{{ route('tipos_documento.create') }}"
           class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
            + Nuevo tipo
        </a>
    </div>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">
            {{ session('exito') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden max-w-xl">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Tipo de documento</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tiposDocumento as $tipo)
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">#{{ $tipo->id_tipo_documento }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $tipo->tipo_documento }}</td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('tipos_documento.edit', $tipo->id_tipo_documento) }}"
                               class="text-teal-600 hover:underline font-medium">Editar</a>

                            <form action="{{ route('tipos_documento.destroy', $tipo->id_tipo_documento) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('¿Eliminar este tipo de documento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-medium">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-10 text-center text-gray-400">
                            Todavía no hay tipos de documento registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection