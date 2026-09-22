@extends('admin.layout')

@section('titulo', 'Sedes')
@section('encabezado', 'Ubicaciones · Sedes')

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

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Sedes</h2>
            <p class="text-sm text-gray-500">Sedes físicas de PrismaSalud IPS, agrupadas por ciudad</p>
        </div>
        <a href="{{ route('sedes.create') }}"
           class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
            + Nueva sede
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
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Dirección</th>
                    <th class="px-4 py-3">Teléfono</th>
                    <th class="px-4 py-3">Ciudad</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sedes as $sede)
                    <tr class="border-t border-gray-100 hover:bg-gray-50 {{ !$sede->activo ? 'bg-gray-50/60' : '' }}">
                        <td class="px-4 py-3 text-gray-500">#{{ $sede->id_sede }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $sede->nombre_sede }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sede->direccion }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sede->telefono }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sede->ciudad->ciudad ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($sede->activo)
                                <span class="text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">Activo</span>
                            @else
                                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('sedes.edit', $sede->id_sede) }}"
                               class="text-teal-600 hover:underline font-medium">Editar</a>

                            <form id="form-estado-sede-{{ $sede->id_sede }}"
                                  action="{{ route('sedes.estado', $sede->id_sede) }}"
                                  method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                            </form>

                            @if ($sede->activo)
                                <button type="button"
                                        onclick="abrirModalConfirmacion('form-estado-sede-{{ $sede->id_sede }}', '¿Desactivar esta sede?', 'Ya no aparecerá disponible para nuevas agendaciones.', 'Desactivar')"
                                        class="text-red-600 hover:underline font-medium">
                                    Desactivar
                                </button>
                            @else
                                <button type="button"
                                        onclick="abrirModalConfirmacion('form-estado-sede-{{ $sede->id_sede }}', '¿Activar esta sede?', 'Volverá a estar disponible para nuevas agendaciones.', 'Activar')"
                                        class="text-green-600 hover:underline font-medium">
                                    Activar
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                            Todavía no hay sedes registradas.
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