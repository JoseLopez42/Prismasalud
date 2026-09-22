{{-- Menú de acciones por fila (dropdown profesional).
     Variables esperadas:
       $rutaEditar          -> ruta de edición
       $formId              -> id del form de estado (sin espacios)
       $rutaEstado          -> ruta PATCH de estado
       $activo              -> bool: estado actual del registro
       $tituloDesactivar    -> título del modal al desactivar
       $textoDesactivar     -> descripción del modal al desactivar
       $tituloActivar       -> título del modal al activar
       $textoActivar        -> descripción del modal al activar
--}}
<div class="relative flex justify-end menu-contenedor-acciones">
    <button type="button" data-menu-id="menu-{{ $formId }}" title="Ver acciones"
            class="boton-menu-acciones inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 text-xs font-medium hover:bg-gray-50 hover:text-gray-700 hover:border-gray-300 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="12" cy="5" r="1.75"/><circle cx="12" cy="12" r="1.75"/><circle cx="12" cy="19" r="1.75"/>
        </svg>
        Acciones
    </button>

    <form id="{{ $formId }}" action="{{ $rutaEstado }}" method="POST" class="hidden">
        @csrf
        @method('PATCH')
    </form>

    <div id="menu-{{ $formId }}" class="menu-acciones absolute right-0 top-full mt-1.5 w-48 bg-white border border-gray-200 rounded-xl shadow-lg z-50 py-1.5 hidden">
        <a href="{{ $rutaEditar }}"
           class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar
        </a>

        @if ($activo)
            <button type="button" onclick="abrirModalConfirmacion('{{ $formId }}', '{{ $tituloDesactivar }}', '{{ $textoDesactivar }}', 'desactivar')"
                    class="flex items-center gap-2.5 w-full px-3.5 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Desactivar
            </button>
        @else
            <button type="button" onclick="abrirModalConfirmacion('{{ $formId }}', '{{ $tituloActivar }}', '{{ $textoActivar }}', 'activar')"
                    class="flex items-center gap-2.5 w-full px-3.5 py-2 text-sm text-green-600 hover:bg-green-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Activar
            </button>
        @endif
    </div>
</div>