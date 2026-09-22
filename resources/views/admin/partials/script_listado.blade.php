{{-- Scripts comunes de los listados del panel admin:
     1) Modal de confirmación propio (activar/desactivar) con iconos SVG.
     2) Buscador rápido que filtra las filas de la tabla en vivo.
     3) Exportar a PDF lo que se está viendo (fila filtradas incluidas).

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
    // EXPORTAR A PDF lo que se está viendo (respeta el filtro activo)
    // ======================================================================
    function exportarPdf() {
        const tabla = document.getElementById('tabla-listado');
        const boton = document.getElementById('boton-exportar-pdf');
        if (!tabla || !window.jspdf) return;

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'portrait', unit: 'pt', format: 'a4' });

        const titulo = (boton && boton.getAttribute('data-titulo-pdf')) || (document.getElementById('titulo-listado')?.textContent || 'Reporte');

        // Encabezado del PDF
        doc.setFillColor(26, 92, 58);
        doc.rect(0, 0, 595.28, 60, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(16);
        doc.text('PrismaSalud IPS', 40, 28);
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);
        doc.text('Institución Prestadora de Servicios de Salud · Panel Administrador', 40, 44);

        doc.setTextColor(31, 41, 55);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(14);
        doc.text(titulo, 40, 90);

        const hoy = new Date();
        const fecha = hoy.toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' });
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        doc.setTextColor(107, 114, 128);
        doc.text('Generado el ' + fecha + ' · ' + hoy.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' }), 40, 106);

        // Cabeceras: todas menos la última columna (Acciones)
        const celdasCabecera = Array.from(tabla.querySelectorAll('thead th'));
        const cabeceras = celdasCabecera.slice(0, -1).map(th => th.textContent.trim());

        // Filas visibles (filtro activo), excluyendo Acciones
        const filasVisibles = Array.from(tabla.querySelectorAll('tbody tr.fila-dato'))
            .filter(fila => !fila.classList.contains('oculto') && !fila.classList.contains('fila-vacio'));

        const cuerpo = filasVisibles.map(fila => {
            return Array.from(fila.querySelectorAll('td'))
                .slice(0, -1)
                .map(td => (td.textContent || '').replace(/\s+/g, ' ').trim());
        });

        doc.autoTable({
            head: [cabeceras],
            body: cuerpo,
            startY: 116,
            styles: { font: 'helvetica', fontSize: 9, cellPadding: 6, textColor: [51, 65, 85] },
            headStyles: { fillColor: [26, 92, 58], textColor: 255, fontStyle: 'bold' },
            alternateRowStyles: { fillColor: [246, 250, 249] },
            margin: { left: 40, right: 40 },
            columnStyles: { 0: { cellWidth: 'auto' } }
        });

        // Resumen al final
        const total = cuerpo.length;
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(9);
        doc.setTextColor(31, 41, 55);
        doc.text('Total: ' + total + ' registro(s)', 40, doc.lastAutoTable.finalY + 20);

        const nombreArchivo = (boton && boton.getAttribute('data-nombre-archivo')) || 'reporte';
        doc.save(nombreArchivo + '_' + new Date().toISOString().slice(0, 10) + '.pdf');
    }
</script>