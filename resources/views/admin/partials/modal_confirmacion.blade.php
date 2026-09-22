{{-- Modal de confirmación reutilizable para los listados del panel admin.
     Usa el JS de admin.partials.script_listado (abrirModalConfirmacion). --}}
<div id="modal-confirmar" role="dialog" aria-modal="true" aria-labelledby="modal-titulo">
    <div class="tarjeta-modal bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 p-6 text-center">
        <div id="modal-icono" class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4"></div>
        <h3 id="modal-titulo" class="text-lg font-bold text-gray-900 mb-1">¿Estás seguro?</h3>
        <p id="modal-texto" class="text-sm text-gray-500 mb-6">Esta acción se puede revertir.</p>

        <div class="flex gap-3">
            <button type="button" onclick="cerrarModalConfirmacion()"
                    class="flex-1 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                Cancelar
            </button>
            <button type="button" id="modal-boton-confirmar"
                    class="flex-1 text-white font-semibold py-2.5 rounded-lg transition shadow-sm">
                Confirmar
            </button>
        </div>
    </div>
</div>