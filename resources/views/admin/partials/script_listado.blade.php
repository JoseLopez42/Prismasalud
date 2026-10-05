{{-- Scripts comunes de los listados del panel admin:
     1) Modal de confirmación propio (activar/desactivar) con iconos SVG.
     2) Buscador rápido que filtra las filas de la tabla en vivo.
     3) Exportar a Excel (.xlsx) lo que se está viendo (filas filtradas incluidas).

     Requiere que en la página existan:
     - Un input con id="buscador-listado"
     - Una tabla con id="tabla-listado" y filas con clase "fila-dato"
     - Un <tr> con id="fila-vacio-busqueda" (mensaje cuando la búsqueda no encuentra nada)
     - Un elemento con id="contador-resultados" (opcional) --}}
<script>
    // ======================================================================
    // MODAL DE CONFIRMACIÓN (propio, sin confirm() del navegador)
    // ======================================================================
    let formularioPendiente = null;

    const ICONO_MODAL = {
        activar: {
            fondo: 'bg-green-100 text-green-600',
            color: 'bg-green-600 hover:bg-green-700',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        },
        desactivar: {
            fondo: 'bg-red-100 text-red-600',
            color: 'bg-red-600 hover:bg-red-700',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        }
    };

    function abrirModalConfirmacion(idFormulario, titulo, texto, accion) {
        formularioPendiente = document.getElementById(idFormulario);
        if (!formularioPendiente) return;

        const tipo = accion === 'activar' ? 'activar' : 'desactivar';
        const config = ICONO_MODAL[tipo];

        document.getElementById('modal-titulo').textContent = titulo;
        document.getElementById('modal-texto').textContent = texto;

        const icono = document.getElementById('modal-icono');
        icono.className = 'w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4 ' + config.fondo;
        icono.innerHTML = config.svg;

        const botonConfirmar = document.getElementById('modal-boton-confirmar');
        botonConfirmar.textContent = accion === 'activar' ? 'Sí, activar' : 'Sí, desactivar';
        botonConfirmar.className = 'flex-1 text-white font-semibold py-2.5 rounded-lg transition shadow-sm ' + config.color;

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

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape') {
            cerrarModalConfirmacion();
            document.querySelectorAll('.menu-acciones').forEach(function (m) { m.classList.add('hidden'); });
        }
    });

    // ======================================================================
    // MENÚ DE ACCIONES POR FILA (dropdown profesional)
    // ======================================================================
    document.addEventListener('click', function (evento) {
        const boton = evento.target.closest('.boton-menu-acciones');
        const menus = document.querySelectorAll('.menu-acciones');

        if (boton) {
            const menu = document.getElementById(boton.getAttribute('data-menu-id'));
            menus.forEach(function (m) { if (m !== menu) m.classList.add('hidden'); });
            if (menu) menu.classList.toggle('hidden');
            return;
        }

        menus.forEach(function (m) { m.classList.add('hidden'); });
    });

    // ======================================================================
    // BUSCADOR RÁPIDO (filtra las filas de la tabla en vivo)
    // ======================================================================
    function normalizarTexto(texto) {
        return (texto || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const buscador = document.getElementById('buscador-listado');
        const tabla = document.getElementById('tabla-listado');
        const contador = document.getElementById('contador-resultados');

        if (!buscador || !tabla) return;

        const filas = Array.from(tabla.querySelectorAll('tbody tr.fila-dato'));
        const filaVacioBusqueda = document.getElementById('fila-vacio-busqueda');

        function actualizarContador() {
            if (!contador) return;
            const visibles = filas.filter(f => !f.classList.contains('oculto')).length;
            contador.textContent = visibles + ' de ' + filas.length + ' registros';
        }

        buscador.addEventListener('input', function () {
            const termino = normalizarTexto(buscador.value.trim());
            let visibles = 0;

            filas.forEach(function (fila) {
                const coincide = termino === '' || normalizarTexto(fila.textContent).includes(termino);
                fila.classList.toggle('oculto', !coincide);
                if (coincide) visibles++;
            });

            if (filaVacioBusqueda) {
                filaVacioBusqueda.classList.toggle('oculto', visibles > 0);
            }
            actualizarContador();
        });

        actualizarContador();
    });

    // ======================================================================
    // EXPORTAR A EXCEL (.xlsx) lo que se está viendo (respeta el filtro)
    // ======================================================================
    // Excel no admite más de 31 caracteres ni estos signos en el nombre
    // de una hoja, así que el título se normaliza antes de usarlo.
    function nombreHoja(texto) {
        return (texto || 'Listado').replace(/[\\/?*[\]:]/g, ' ').trim().slice(0, 31) || 'Listado';
    }

    function exportarExcel() {
        const tabla = document.getElementById('tabla-listado');
        const boton = document.getElementById('boton-exportar-excel');
        if (!tabla || !window.XLSX) return;

        const titulo = (boton && boton.getAttribute('data-titulo')) || (document.getElementById('titulo-listado')?.textContent.trim() || 'Reporte');

        // Cabeceras: todas menos la última columna (Acciones)
        const cabeceras = Array.from(tabla.querySelectorAll('thead th'))
            .slice(0, -1)
            .map(th => th.textContent.trim());

        // Filas visibles (filtro activo), excluyendo Acciones
        const cuerpo = Array.from(tabla.querySelectorAll('tbody tr.fila-dato'))
            .filter(fila => !fila.classList.contains('oculto') && !fila.classList.contains('fila-vacio'))
            .map(fila => Array.from(fila.querySelectorAll('td'))
                .slice(0, -1)
                .map(td => (td.textContent || '').replace(/\s+/g, ' ').trim()));

        if (!cuerpo.length) {
            alert('No hay registros visibles para exportar.');
            return;
        }

        const libro = XLSX.utils.book_new();
        const hoja = XLSX.utils.aoa_to_sheet([cabeceras, ...cuerpo]);

        // Ancho de columna aproximado según el texto más largo de cada una.
        hoja['!cols'] = cabeceras.map((encabezado, i) => ({
            wch: Math.min(
                50,
                Math.max(encabezado.length, ...cuerpo.map(fila => String(fila[i] ?? '').length)) + 2
            )
        }));

        XLSX.utils.book_append_sheet(libro, hoja, nombreHoja(titulo));

        const hoy = new Date();
        const nombreArchivo = (boton && boton.getAttribute('data-nombre-archivo')) || 'reporte';
        const sello = hoy.toISOString().slice(0, 10);

        XLSX.writeFile(libro, nombreArchivo + '_' + sello + '.xlsx');
    }
</script>