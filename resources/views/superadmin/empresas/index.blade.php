@extends('superadmin.layout')
@section('titulo', 'Empresas')

@section('contenido')
    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Empresas</h1>
            <p class="text-sm text-gray-500 mt-1">IPS y EPS con licencia. Aquí corriges sus datos o las suspendes.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            {{-- Descargas: usan la búsqueda y el filtro que tengas puestos --}}
            <button type="button" id="btn-pdf" data-url="{{ route('superadmin.empresas.exportar', request()->query()) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-bold px-4 py-2.5 transition disabled:opacity-60">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
                <span>Descargar PDF</span>
            </button>
            <button type="button" id="btn-excel" data-url="{{ route('superadmin.empresas.exportar', request()->query()) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-bold px-4 py-2.5 transition disabled:opacity-60">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
                <span>Descargar Excel</span>
            </button>
            <a href="{{ route('superadmin.empresas.create') }}"
               class="rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-4 py-2.5 transition">+ Nueva empresa</a>
        </div>
    </div>
    <p id="aviso-descarga" class="hidden mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3" role="alert"></p>

    {{-- Buscador y filtro --}}
    <form method="GET" action="{{ route('superadmin.empresas.index') }}" class="bg-white rounded-xl border border-gray-200 p-4 mb-5 flex flex-wrap gap-3">
        <input type="search" name="q" value="{{ $busqueda }}" placeholder="Buscar por nombre, NIT o contacto…"
               class="flex-1 min-w-[200px] rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
        <select name="estado" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
            <option value="">Todos los estados</option>
            @foreach (['activa' => 'Activas', 'pendiente' => 'Pendientes', 'por_vencer' => 'Por vencer', 'vencida' => 'Vencidas', 'suspendida' => 'Suspendidas'] as $clave => $texto)
                <option value="{{ $clave }}" @selected($estado === $clave)>{{ $texto }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-prisma-azul hover:bg-prisma-turquesa text-white text-sm font-bold px-4 py-2 transition">Buscar</button>
        @if ($busqueda !== '' || $estado !== '')
            <a href="{{ route('superadmin.empresas.index') }}" class="rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-600 text-sm font-bold px-4 py-2 transition">Limpiar</a>
        @endif
    </form>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if ($empresas->isEmpty())
            <div class="px-5 py-14 text-center">
                <p class="text-sm font-semibold text-gray-700">No hay empresas para mostrar.</p>
                <p class="text-sm text-gray-500 mt-1">
                    {{ ($busqueda !== '' || $estado !== '') ? 'Prueba con otra búsqueda o limpia el filtro.' : 'Crea la primera con el botón "Nueva empresa".' }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="px-5 py-3">Empresa</th>
                            <th class="px-5 py-3">Contacto</th>
                            <th class="px-5 py-3">Plan</th>
                            <th class="px-5 py-3">Vence</th>
                            <th class="px-5 py-3">Estado</th>
                            <th class="px-5 py-3 text-center">Admins</th>
                            <th class="px-5 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($empresas as $licencia)
                            @php $dias = $licencia->diasRestantes(); @endphp
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-5 py-3">
                                    <p class="font-semibold text-gray-900">{{ $licencia->nombre_ips }}</p>
                                    <p class="text-xs text-gray-500">NIT {{ $licencia->nit }}</p>
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    <p>{{ $licencia->contacto_nombre ?: '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $licencia->contacto_email ?: '' }}</p>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $licencia->nombrePlan() }}</td>
                                <td class="px-5 py-3 text-gray-600">
                                    <p>{{ $licencia->fecha_fin->format('d/m/Y') }}</p>
                                    <p class="text-xs {{ $dias < 0 ? 'text-red-500' : 'text-gray-400' }}">
                                        @if ($dias < 0) hace {{ abs($dias) }} {{ abs($dias) === 1 ? 'día' : 'días' }}
                                        @elseif ($dias === 0) vence hoy
                                        @else en {{ $dias }} {{ $dias === 1 ? 'día' : 'días' }}
                                        @endif
                                    </p>
                                </td>
                                <td class="px-5 py-3">@include('superadmin._estado', ['licencia' => $licencia])</td>
                                <td class="px-5 py-3 text-center">
                                    <a href="{{ route('superadmin.administradores.index', ['empresa' => $licencia->id]) }}"
                                       class="inline-block min-w-[2rem] rounded-full bg-gray-100 hover:bg-teal-50 hover:text-teal-700 text-gray-700 text-xs font-bold px-2.5 py-1 transition">
                                        {{ $admins[$licencia->id] ?? 0 }}
                                    </a>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="grid grid-cols-[5.5rem_6.5rem] justify-end gap-2 items-center ml-auto">
                                        <a href="{{ route('superadmin.empresas.edit', $licencia->id) }}"
                                           class="rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1.5 transition text-center w-full">Editar</a>
                                        @if ($licencia->estado === 'inactiva')
                                            <form method="POST" action="{{ route('superadmin.empresas.estado', $licencia->id) }}"
                                                  onsubmit="return confirm('¿Activar esta licencia?');">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="rounded-lg text-xs font-bold px-3 py-1.5 transition bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-center w-full">
                                                    Activar
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('superadmin.empresas.estado', $licencia->id) }}"
                                                  onsubmit="return confirm('{{ $licencia->estado === 'suspendida' ? '¿Reactivar esta empresa?' : '¿Suspender esta empresa?' }}');">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="rounded-lg text-xs font-bold px-3 py-1.5 transition {{ $licencia->estado === 'suspendida' ? 'bg-teal-50 text-teal-700 hover:bg-teal-100' : 'bg-red-50 text-red-600 hover:bg-red-100' }} text-center w-full">
                                                    {{ $licencia->estado === 'suspendida' ? 'Reactivar' : 'Suspender' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($empresas->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $empresas->links() }}</div>
            @endif
        @endif
    </section>

@push('scripts')
{{-- Librerías para armar los archivos en el navegador --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
(function () {
    var btnPdf = document.getElementById('btn-pdf');
    var btnExcel = document.getElementById('btn-excel');
    var aviso = document.getElementById('aviso-descarga');

    function hoy() {
        var d = new Date();
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    }

    // Pide los datos al servidor (con la misma búsqueda y filtro de la pantalla)
    function pedirDatos(boton) {
        aviso.classList.add('hidden');
        return fetch(boton.getAttribute('data-url'), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(function (r) {
                if (!r.ok) throw new Error('El servidor respondió ' + r.status);
                return r.json();
            });
    }

    function conCarga(boton, hacer) {
        var texto = boton.querySelector('span');
        var original = texto.textContent;
        boton.disabled = true;
        texto.textContent = 'Preparando...';
        pedirDatos(boton)
            .then(hacer)
            .catch(function (e) {
                aviso.textContent = 'No se pudo preparar la descarga. ' + e.message + '. Revisa que tengas internet y vuelve a intentarlo.';
                aviso.classList.remove('hidden');
            })
            .then(function () { boton.disabled = false; texto.textContent = original; });
    }

    btnExcel.addEventListener('click', function () {
        conCarga(btnExcel, function (datos) {
            var hoja = XLSX.utils.aoa_to_sheet([datos.columnas].concat(datos.filas));
            hoja['!cols'] = [{ wch: 32 }, { wch: 16 }, { wch: 24 }, { wch: 30 }, { wch: 16 }, { wch: 20 }, { wch: 12 }, { wch: 12 }, { wch: 12 }, { wch: 8 }];
            var libro = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(libro, hoja, 'Empresas');
            XLSX.writeFile(libro, 'empresas-prismasalud-' + hoy() + '.xlsx');
        });
    });

    btnPdf.addEventListener('click', function () {
        conCarga(btnPdf, function (datos) {
            var doc = new window.jspdf.jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
            var ancho = doc.internal.pageSize.getWidth();

            doc.setFont('helvetica', 'bold');
            doc.setFontSize(16);
            doc.setTextColor(27, 77, 107);
            doc.text('PrismaSalud - Empresas con licencia', 40, 42);

            doc.setFont('helvetica', 'normal');
            doc.setFontSize(9);
            doc.setTextColor(107, 114, 128);
            doc.text(datos.filtros + '  |  Total: ' + datos.total + '  |  Generado: ' + new Date().toLocaleDateString('es-CO'), 40, 60);

            doc.autoTable({
                startY: 74,
                head: [datos.columnas],
                body: datos.filas,
                styles: { fontSize: 8, cellPadding: 4, textColor: [17, 24, 39] },
                headStyles: { fillColor: [27, 77, 107], textColor: 255, fontStyle: 'bold' },
                alternateRowStyles: { fillColor: [243, 244, 246] },
                margin: { left: 40, right: 40 },
                didDrawPage: function () {
                    var pagina = doc.internal.getNumberOfPages();
                    doc.setFontSize(8);
                    doc.setTextColor(107, 114, 128);
                    doc.text('Página ' + pagina, ancho - 40, doc.internal.pageSize.getHeight() - 20, { align: 'right' });
                }
            });

            doc.save('empresas-prismasalud-' + hoy() + '.pdf');
        });
    });
})();
</script>
@endpush
@endsection