@extends('admin.layout')

@section('titulo', 'Roles')
@section('encabezado', 'Usuarios y Roles · Roles')

@push('estilos')
<style>
    #modal-confirmar {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
        opacity: 0;
        visibility: hidden;
        transition: opacity .2s ease, visibility .2s ease;
    }
    #modal-confirmar.abierto {
        opacity: 1;
        visibility: visible;
    }
    #modal-confirmar .tarjeta-modal {
        transform: scale(.95);
        transition: transform .2s ease;
    }
    #modal-confirmar.abierto .tarjeta-modal {
        transform: scale(1);
    }
</style>
@endpush

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
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                {{-- $roles viene con usuarios_count (withCount) --}}
                @forelse ($roles as $rol)
                    <tr class="border-t border-gray-100 hover:bg-gray-50 {{ !$rol->activo ? 'bg-gray-50/60' : '' }}">
                        <td class="px-4 py-3 text-gray-500">#{{ $rol->id }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $rol->nombre }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full">
                                {{ $rol->usuarios_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($rol->activo)
                                <span class="text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">Activo</span>
                            @else
                                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('roles.edit', $rol->id) }}"
                               class="text-teal-600 hover:underline font-medium">Editar</a>

                            <form id="form-estado-rol-{{ $rol->id }}"
                                  action="{{ route('roles.estado', $rol->id) }}"
                                  method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                            </form>

                            @if ($rol->activo)
                                <button type="button"
                                        onclick="abrirModalConfirmacion('form-estado-rol-{{ $rol->id }}', '¿Desactivar este rol?', 'Ya no podrá elegirse al crear o editar usuarios.', 'Desactivar')"
                                        class="text-red-600 hover:underline font-medium">
                                    Desactivar
                                </button>
                            @else
                                <button type="button"
                                        onclick="abrirModalConfirmacion('form-estado-rol-{{ $rol->id }}', '¿Activar este rol?', 'Volverá a estar disponible para usuarios.', 'Activar')"
                                        class="text-green-600 hover:underline font-medium">
                                    Activar
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                            Todavía no hay roles registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="modal-confirmar">
        <div class="tarjeta-modal bg-white rounded-2xl shadow-xl max-w-sm w-full mx-4 p-6 text-center">
            <div id="modal-icono" class="w-14 h-14 mx-auto rounded-full flex items-center justify-center text-2xl mb-4 bg-red-50">
                ⚠️
            </div>
            <h3 id="modal-titulo" class="text-lg font-bold text-gray-900 mb-1">¿Estás seguro?</h3>
            <p id="modal-texto" class="text-sm text-gray-500 mb-6">Esta acción se puede revertir.</p>

            <div class="flex gap-3">
                <button type="button" onclick="cerrarModalConfirmacion()"
                        class="flex-1 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="button" id="modal-boton-confirmar"
                        class="flex-1 bg-red-600 text-white font-semibold py-2.5 rounded-lg hover:bg-red-700 transition">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <script>
        let formularioPendiente = null;

        function abrirModalConfirmacion(idFormulario, titulo, texto, textoBoton) {
            formularioPendiente = document.getElementById(idFormulario);

            document.getElementById('modal-titulo').textContent = titulo;
            document.getElementById('modal-texto').textContent = texto;

            const botonConfirmar = document.getElementById('modal-boton-confirmar');
            botonConfirmar.textContent = textoBoton;

            const esActivar = textoBoton === 'Activar';
            document.getElementById('modal-icono').className =
                'w-14 h-14 mx-auto rounded-full flex items-center justify-center text-2xl mb-4 ' +
                (esActivar ? 'bg-green-50' : 'bg-red-50');
            document.getElementById('modal-icono').textContent = esActivar ? '✅' : '⚠️';
            botonConfirmar.className =
                'flex-1 text-white font-semibold py-2.5 rounded-lg transition ' +
                (esActivar ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700');

            document.getElementById('modal-confirmar').classList.add('abierto');
        }

        function cerrarModalConfirmacion() {
            document.getElementById('modal-confirmar').classList.remove('abierto');
            formularioPendiente = null;
        }

        document.getElementById('modal-boton-confirmar').addEventListener('click', function () {
            if (formularioPendiente) {
                formularioPendiente.submit();
            }
        });

        document.getElementById('modal-confirmar').addEventListener('click', function (evento) {
            if (evento.target.id === 'modal-confirmar') {
                cerrarModalConfirmacion();
            }
        });
    </script>

@endsection