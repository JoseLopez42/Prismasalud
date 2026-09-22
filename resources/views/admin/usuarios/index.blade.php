@extends('admin.layout')

@section('titulo', 'Usuarios')
@section('encabezado', 'Usuarios y Roles')

@section('contenido')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Usuarios</h2>
            <p class="text-sm text-gray-500">Administrador, Paciente, Medico y Farmaceuta</p>
        </div>
        <a href="{{ route('usuarios.create') }}"
           class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
            + Nuevo usuario
        </a>
    </div>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">
            {{ session('exito') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Cédula</th>
                    <th class="px-4 py-3">Nombres</th>
                    <th class="px-4 py-3">Apellidos</th>
                    <th class="px-4 py-3">Correo</th>
                    <th class="px-4 py-3">Rol</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $usuario->cedula }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $usuario->nombres }}</td>
                        <td class="px-4 py-3">{{ $usuario->apellidos }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $usuario->correo }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full">
                                {{ $usuario->tipoUsuario->nombre ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('usuarios.edit', $usuario->cedula) }}"
                               class="text-teal-600 hover:underline font-medium">Editar</a>

                            <form action="{{ route('usuarios.destroy', $usuario->cedula) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('¿Eliminar este usuario?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-medium">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                            Todavía no hay usuarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection