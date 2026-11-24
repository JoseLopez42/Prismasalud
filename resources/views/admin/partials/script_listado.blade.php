{{-- Scripts comunes de los listados del panel admin:
     1) Modal de confirmación propio (activar/desactivar) con iconos SVG.
     2) Buscador rápido que filtra las filas de la tabla en vivo.
     3) Exportar a Excel (.xlsx) y PDF (.pdf) lo que se está viendo (filas filtradas incluidas).

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
            ocultarMenusAcciones();
        }
    });

    // ======================================================================
    // MENÚ DE ACCIONES POR FILA (dropdown profesional)
    // Se posiciona de forma fija para que NO se corte con el overflow-x-auto
    // de la tarjeta de la tabla (antes pasaba en listados anchos).
    // ======================================================================
    function posicionarMenuAcciones(menu, boton) {
        if (!menu || !boton) return;

        const rect = boton.getBoundingClientRect();

        // El ancho se mide ya con el menú visible.
        const ancho = menu.offsetWidth || 192;

        menu.style.position = 'fixed';
        menu.style.top = rect.bottom + 'px';              // el mt-1.5 de la clase da el espacio
        menu.style.left = Math.max(8, rect.right - ancho) + 'px';
        menu.style.right = 'auto';
        menu.style.zIndex = '100';
    }

    function ocultarMenusAcciones() {
        document.querySelectorAll('.menu-acciones').forEach(function (menu) {
            menu.classList.add('hidden');
            menu.removeAttribute('style');
        });
    }

    document.addEventListener('click', function (evento) {
        const boton = evento.target.closest('.boton-menu-acciones');

        if (boton) {
            const menu = document.getElementById(boton.getAttribute('data-menu-id'));

            document.querySelectorAll('.menu-acciones').forEach(function (otro) {
                if (otro !== menu) {
                    otro.classList.add('hidden');
                    otro.removeAttribute('style');
                }
            });

            if (menu) {
                const seAbrira = menu.classList.contains('hidden');
                menu.classList.toggle('hidden');

                if (seAbrira) {
                    posicionarMenuAcciones(menu, boton);
                } else {
                    menu.removeAttribute('style');
                }
            }
            return;
        }

        ocultarMenusAcciones();
    });

    // Si la página se desplaza o cambia de tamaño, cerramos los menús abiertos
    // para que no queden flotando en una posición vieja.
    window.addEventListener('scroll', ocultarMenusAcciones, true);
    window.addEventListener('resize', ocultarMenusAcciones);

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
    // EXPORTAR LO QUE SE ESTÁ VIENDO (respeta el filtro del buscador)
    // Excel (.xlsx) y PDF (.pdf) comparten exactamente las mismas filas.
    // ======================================================================
    // Excel no admite más de 31 caracteres ni estos signos en el nombre
    // de una hoja, así que el título se normaliza antes de usarlo.
    function nombreHoja(texto) {
        return (texto || 'Listado').replace(/[\\/?*[\]:]/g, ' ').trim().slice(0, 31) || 'Listado';
    }

    function obtenerDatosExportacion() {
        const tabla = document.getElementById('tabla-listado');
        const boton = document.getElementById('boton-exportar-excel');
        if (!tabla) return null;

        const titulo = (boton && boton.getAttribute('data-titulo'))
            || (document.getElementById('titulo-listado')?.textContent.trim()
            || 'Reporte');

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
            return null;
        }

        return {
            titulo: titulo,
            cabeceras: cabeceras,
            cuerpo: cuerpo,
            nombreArchivo: (boton && boton.getAttribute('data-nombre-archivo')) || 'reporte'
        };
    }

    function exportarExcel() {
        const datos = obtenerDatosExportacion();
        if (!datos || !window.XLSX) return;

        const libro = XLSX.utils.book_new();
        const hoja = XLSX.utils.aoa_to_sheet([datos.cabeceras, ...datos.cuerpo]);

        // Ancho de columna aproximado según el texto más largo de cada una.
        hoja['!cols'] = datos.cabeceras.map((encabezado, i) => ({
            wch: Math.min(
                50,
                Math.max(encabezado.length, ...datos.cuerpo.map(fila => String(fila[i] ?? '').length)) + 2
            )
        }));

        XLSX.utils.book_append_sheet(libro, hoja, nombreHoja(datos.titulo));

        const sello = new Date().toISOString().slice(0, 10);
        XLSX.writeFile(libro, datos.nombreArchivo + '_' + sello + '.xlsx');
    }

    // Las librerías de PDF se cargan solo cuando el usuario pulsa el botón,
    // para no penalizar la carga de todos los listados.
    function cargarScript(url) {
        return new Promise(function (resolver, rechazar) {
            const script = document.createElement('script');
            script.src = url;
            script.onload = resolver;
            script.onerror = function () {
                rechazar(new Error('No se pudo cargar ' + url));
            };
            document.head.appendChild(script);
        });
    }

    async function exportarPDF() {
        const datos = obtenerDatosExportacion();
        if (!datos) return;

        try {
            await cargarScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
            await cargarScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js');
        } catch (error) {
            alert('No se pudo generar el PDF. Revisa tu conexión e inténtalo de nuevo.');
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
        const ancho = doc.internal.pageSize.getWidth();
        const alto = doc.internal.pageSize.getHeight();

        // Encabezado del documento
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(16);
        doc.setTextColor(27, 77, 107);
        doc.text(datos.titulo, 40, 42);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        doc.setTextColor(107, 114, 128);
        doc.text(
            'Generado: ' + new Date().toLocaleDateString('es-CO')
            + '  |  Registros: ' + datos.cuerpo.length,
            40, 60
        );

        // Tabla con las mismas columnas/filas que el Excel
        doc.autoTable({
            startY: 74,
            head: [datos.cabeceras],
            body: datos.cuerpo,
            theme: 'grid',
            styles: { font: 'helvetica', fontSize: 8, cellPadding: 5, overflow: 'linebreak' },
            headStyles: { fillColor: [27, 77, 107], textColor: 255, fontStyle: 'bold' },
            alternateRowStyles: { fillColor: [245, 250, 250] },
            margin: { left: 40, right: 40, bottom: 40 }
        });

        // Pie de página en cada hoja
        const totalPaginas = doc.internal.getNumberOfPages();
        for (let pagina = 1; pagina <= totalPaginas; pagina++) {
            doc.setPage(pagina);
            doc.setFontSize(8);
            doc.setTextColor(150, 150, 150);
            doc.text('PrismaSalud', 40, alto - 18);
            doc.text('Página ' + pagina + ' de ' + totalPaginas, ancho - 40, alto - 18, { align: 'right' });
        }

        const sello = new Date().toISOString().slice(0, 10);
        doc.save(datos.nombreArchivo + '_' + sello + '.pdf');
    }

    // Agrega el botón de PDF junto al de Excel en TODOS los listados que ya
    // tienen exportación, sin necesidad de editar cada vista.
    document.addEventListener('DOMContentLoaded', function () {
        const excel = document.getElementById('boton-exportar-excel');
        if (!excel || document.getElementById('boton-exportar-pdf')) return;

        const pdf = document.createElement('button');
        pdf.type = 'button';
        pdf.id = 'boton-exportar-pdf';
        pdf.onclick = exportarPDF;
        pdf.className = 'inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-200 text-red-700 text-sm font-semibold rounded-md hover:bg-red-50 hover:shadow-sm transition';
        pdf.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">'
            + '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" />'
            + '</svg><span>Descargar PDF</span>';

        excel.insertAdjacentElement('afterend', pdf);
    });
</script>