@extends('admin.layout')

@section('titulo', 'Ciudades')
@section('encabezado', 'Ubicaciones · Ciudades')

@push('estilos')
<style>
    /* Modal de confirmación: overlay oscuro + tarjeta centrada */
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
            <h2 class="text-xl font-bold text-gray-900">Ciudades</h2>
            <p class="text-sm text-gray-500">Ciudades registradas, agrupadas por departamento</p>
        </div>
        <a href="{{ route('ciudades.create') }}"
           class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
            + Nueva ciudad
        </a>
    </div>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">
            {{ session('exito') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600 border-b-2 border-gray-200">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Ciudad</th>
                    <th class="px-4 py-3">Departamento</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-gray-100">
                @forelse ($ciudades as $ciudad)
                    <tr class="hover:bg-gray-50 {{ !$ciudad->activo ? 'bg-gray-50/60' : '' }}">
                        <td class="px-4 py-3 text-gray-500">#{{ $ciudad->id_ciudad }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $ciudad->ciudad }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $ciudad->departamento->nombre_departamento ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($ciudad->activo)
                                <span class="text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">Activo</span>
                            @else
                                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('ciudades.edit', $ciudad->id_ciudad) }}"
                               class="text-teal-600 hover:underline font-medium">Editar</a>

                            {{-- El formulario ya NO se envía con confirm() del navegador.
                                 En su lugar, el botón abre el modal propio, pasándole el
                                 id de este formulario para saber cuál enviar si confirman. --}}
                            <form id="form-estado-ciudad-{{ $ciudad->id_ciudad }}"
                                  action="{{ route('ciudades.estado', $ciudad->id_ciudad) }}"
                                  method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                            </form>

                            @if ($ciudad->activo)
                                <button type="button"
                                        onclick="abrirModalConfirmacion('form-estado-ciudad-{{ $ciudad->id_ciudad }}', '¿Desactivar esta ciudad?', 'Ya no aparecerá disponible para nuevas sedes.', 'Desactivar')"
                                        class="text-red-600 hover:underline font-medium">
                                    Desactivar
                                </button>
                            @else
                                <button type="button"
                                        onclick="abrirModalConfirmacion('form-estado-ciudad-{{ $ciudad->id_ciudad }}', '¿Activar esta ciudad?', 'Volverá a estar disponible para nuevas sedes.', 'Activar')"
                                        class="text-green-600 hover:underline font-medium">
                                    Activar
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                            Todavía no hay ciudades registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ===================================================
         MODAL DE CONFIRMACIÓN PROPIO
         Reemplaza el confirm() del navegador. Empieza oculto;
         el JS de abajo lo llena con el mensaje correcto y lo
         muestra al hacer clic en "Activar"/"Desactivar".
    =================================================== --}}
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
        // Guarda cuál formulario hay que enviar si el usuario confirma
        let formularioPendiente = null;

        function abrirModalConfirmacion(idFormulario, titulo, texto, textoBoton) {
            formularioPendiente = document.getElementById(idFormulario);

            document.getElementById('modal-titulo').textContent = titulo;
            document.getElementById('modal-texto').textContent = texto;

            const botonConfirmar = document.getElementById('modal-boton-confirmar');
            botonConfirmar.textContent = textoBoton;

            // Si la acción es "Activar", el botón se pinta verde en vez de rojo
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

        // Al confirmar, se envía el formulario que quedó guardado
        document.getElementById('modal-boton-confirmar').addEventListener('click', function () {
            if (formularioPendiente) {
                formularioPendiente.submit();
            }
        });

        // Cerrar el modal si tocan fuera de la tarjeta
        document.getElementById('modal-confirmar').addEventListener('click', function (evento) {
            if (evento.target.id === 'modal-confirmar') {
                cerrarModalConfirmacion();
            }
        });
    </script>

@endsection