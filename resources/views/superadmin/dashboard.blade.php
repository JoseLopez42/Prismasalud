@extends('superadmin.layout')
@section('titulo', 'Inicio')

@section('contenido')
    {{-- Encabezado --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900"><span id="saludo">Bienvenido</span>, {{ $nombre }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                <span id="fecha-hoy"></span> · Actualizado a las <span id="hora-act">--:--</span>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" id="btn-actualizar"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 text-sm font-bold px-4 py-2.5 transition">
                <svg id="icono-actualizar" class="w-4 h-4 transition-transform duration-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 4v5h-5"/></svg>
                Actualizar
            </button>
            <a href="{{ route('superadmin.administradores.create') }}"
               class="rounded-lg border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 text-sm font-bold px-4 py-2.5 transition">+ Nuevo administrador</a>
            <a href="{{ route('superadmin.empresas.create') }}"
               class="rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-4 py-2.5 transition">+ Nueva empresa</a>
        </div>
    </div>

    {{-- Tarjetas: cada una abre Empresas ya filtrada --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        @foreach ([
            ['Empresas', $resumen['total'], route('superadmin.empresas.index'), 'Ver todas'],
            ['Activas', $resumen['activas'], route('superadmin.empresas.index', ['estado' => 'activa']), 'Ver activas'],
            ['Por vencer (15 días)', $resumen['por_vencer'], route('superadmin.empresas.index', ['estado' => 'por_vencer']), 'Ver las que vencen'],
            ['Vencidas', $resumen['vencidas'], route('superadmin.empresas.index', ['estado' => 'vencida']), 'Ver vencidas'],
        ] as $tarjeta)
            <a href="{{ $tarjeta[2] }}"
               class="group block bg-white rounded-xl border border-gray-200 p-5 transition hover:border-prisma-claro hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <p class="text-xs font-bold text-gray-500 uppercase">{{ $tarjeta[0] }}</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1" data-contar="{{ $tarjeta[1] }}">{{ $tarjeta[1] }}</p>
                <p class="mt-2 text-xs font-bold text-gray-400 group-hover:text-prisma-claro transition">
                    {{ $tarjeta[3] }} <span class="inline-block transition-transform group-hover:translate-x-1">→</span>
                </p>
            </a>
        @endforeach
    </section>

    <section class="grid sm:grid-cols-2 gap-4 mb-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-bold text-gray-500 uppercase">Ingresos aprobados</p>
            <p id="ingresos" class="text-3xl font-extrabold text-gray-900 mt-1 transition">${{ number_format($resumen['ingresos'], 0, ',', '.') }} <span class="text-sm font-semibold text-gray-400">COP</span></p>
        </div>
        <a href="{{ route('superadmin.administradores.index') }}"
           class="group block bg-white rounded-xl border border-gray-200 p-5 transition hover:border-prisma-claro hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
            <p class="text-xs font-bold text-gray-500 uppercase">Administradores creados</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-1" data-contar="{{ $resumen['administradores'] }}">{{ $resumen['administradores'] }}</p>
            <p class="mt-2 text-xs font-bold text-gray-400 group-hover:text-prisma-claro transition">
                Ver administradores <span class="inline-block transition-transform group-hover:translate-x-1">→</span>
            </p>
        </a>
    </section>

    {{-- Reparto de empresas por estado --}}
    <section class="bg-white rounded-xl border border-gray-200 p-5 mb-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-extrabold text-gray-900">Estado de las licencias</h2>
        </div>

        @if ($resumen['total'] === 0)
            <p class="text-sm text-gray-500">Cuando registres empresas, aquí verás cómo se reparten.</p>
        @else
            <div class="flex h-3 w-full overflow-hidden rounded-full bg-gray-100">
                @foreach ([
                    ['al_dia', 'Al día', 'activa', 'bg-prisma-teal'],
                    ['por_vencer', 'Por vencer', 'por_vencer', 'bg-prisma-claro'],
                    ['vencidas', 'Vencidas', 'vencida', 'bg-prisma-azul'],
                    ['suspendidas', 'Suspendidas', 'suspendida', 'bg-gray-400'],
                ] as $grupo)
                    @if ($reparto[$grupo[0]] > 0)
                        <a href="{{ route('superadmin.empresas.index', ['estado' => $grupo[2]]) }}"
                           class="{{ $grupo[3] }} h-full transition-opacity hover:opacity-80 focus:opacity-80 focus:outline-none"
                           style="width: {{ round($reparto[$grupo[0]] / $resumen['total'] * 100, 2) }}%"
                           title="{{ $grupo[1] }}: {{ $reparto[$grupo[0]] }}"></a>
                    @endif
                @endforeach
            </div>
            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-xs text-gray-600">
                @foreach ([
                    ['al_dia', 'Al día', 'activa', 'bg-prisma-teal'],
                    ['por_vencer', 'Por vencer', 'por_vencer', 'bg-prisma-claro'],
                    ['vencidas', 'Vencidas', 'vencida', 'bg-prisma-azul'],
                    ['suspendidas', 'Suspendidas', 'suspendida', 'bg-gray-400'],
                ] as $grupo)
                    <a href="{{ route('superadmin.empresas.index', ['estado' => $grupo[2]]) }}" class="inline-flex items-center gap-1.5 hover:text-gray-900 hover:underline">
                        <span class="w-2.5 h-2.5 rounded-sm {{ $grupo[3] }}"></span>
                        {{ $grupo[1] }} <strong class="text-gray-900">{{ $reparto[$grupo[0]] }}</strong>
                    </a>
                @endforeach
            </div>
        @endif
    </section>


@endsection

@push('scripts')
<script>
(function () {
    var $ = function (s, r) { return (r || document).querySelector(s); };
    var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

    // Saludo, fecha y hora
    var ahora = new Date(), h = ahora.getHours();
    $('#saludo').textContent = h < 12 ? 'Buenos días' : (h < 19 ? 'Buenas tardes' : 'Buenas noches');
    var fecha = ahora.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    $('#fecha-hoy').textContent = fecha.charAt(0).toUpperCase() + fecha.slice(1);
    $('#hora-act').textContent = ahora.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });

    // Botón actualizar
    $('#btn-actualizar').addEventListener('click', function () {
        $('#icono-actualizar').style.transform = 'rotate(360deg)';
        setTimeout(function () { location.reload(); }, 450);
    });

    // Números que suben hasta su valor
    $$('[data-contar]').forEach(function (el) {
        var meta = parseInt(el.getAttribute('data-contar'), 10) || 0;
        if (meta === 0 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        var ini = null;
        function paso(t) {
            if (ini === null) ini = t;
            var p = Math.min((t - ini) / 600, 1);
            el.textContent = Math.round(meta * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(paso);
        }
        el.textContent = '0';
        requestAnimationFrame(paso);
    });

    // Buscar y filtrar "Últimas empresas"
    var buscar = $('#buscar-empresa');
    if (buscar) {
        var estadoSel = '';
        var filas = $$('.fila-empresa');
        var aplicar = function () {
            var q = buscar.value.trim().toLowerCase(), visibles = 0;
            filas.forEach(function (f) {
                var ok = (estadoSel === '' || f.getAttribute('data-estado') === estadoSel) && (q === '' || f.getAttribute('data-texto').indexOf(q) !== -1);
                f.classList.toggle('hidden', !ok);
                if (ok) visibles++;
            });
            $('#sin-resultados').classList.toggle('hidden', visibles !== 0);
        };
        buscar.addEventListener('input', aplicar);
        $$('#chips-estado .chip').forEach(function (chip) {
            chip.addEventListener('click', function () {
                estadoSel = chip.getAttribute('data-estado');
                $$('#chips-estado .chip').forEach(function (c) {
                    var on = c === chip;
                    ['bg-prisma-azul', 'border-prisma-azul', 'text-white'].forEach(function (k) { c.classList.toggle(k, on); });
                    ['bg-white', 'border-gray-300', 'text-gray-600', 'hover:bg-gray-100'].forEach(function (k) { c.classList.toggle(k, !on); });
                });
                aplicar();
            });
        });
        $('#limpiar-filtros').addEventListener('click', function () { buscar.value = ''; $('#chips-estado .chip').click(); buscar.focus(); });

        // Fila completa clicable (con teclado también)
        filas.forEach(function (f) {
            f.addEventListener('click', function (e) { if (!e.target.closest('a,button')) location.href = f.getAttribute('data-href'); });
            f.addEventListener('keydown', function (e) { if (e.key === 'Enter' && e.target === f) location.href = f.getAttribute('data-href'); });
        });

        // Atajo: "/" lleva al buscador
        document.addEventListener('keydown', function (e) {
            var tag = (e.target.tagName || '').toLowerCase();
            if (e.key === '/' && tag !== 'input' && tag !== 'textarea' && tag !== 'select') { e.preventDefault(); buscar.focus(); }
            if (e.key === 'Escape' && document.activeElement === buscar) { buscar.value = ''; aplicar(); buscar.blur(); }
        });
    }

    // Rango de días en "Por vencer pronto"
    var items = $$('.item-vencer');
    if (items.length) {
        var rango = 15;
        var pintar = function () {
            var n = 0;
            items.forEach(function (li) {
                var ok = parseInt(li.getAttribute('data-dias'), 10) <= rango;
                li.classList.toggle('hidden', !ok);
                if (ok) n++;
            });
            $('#total-vencer').textContent = n;
            $('#vacio-rango').classList.toggle('hidden', n !== 0);
            $$('.rango').forEach(function (b) {
                var on = parseInt(b.getAttribute('data-dias'), 10) === rango;
                ['bg-prisma-azul', 'text-white'].forEach(function (k) { b.classList.toggle(k, on); });
                ['bg-white', 'text-gray-600', 'hover:bg-gray-100'].forEach(function (k) { b.classList.toggle(k, !on); });
            });
        };
        $$('.rango').forEach(function (b) { b.addEventListener('click', function () { rango = parseInt(b.getAttribute('data-dias'), 10); pintar(); }); });
        pintar();
    } else if ($('#total-vencer')) { $('#total-vencer').textContent = '0'; }

    // Copiar correo de contacto
    $$('.copiar').forEach(function (b) {
        b.addEventListener('click', function () {
            var correo = b.getAttribute('data-correo');
            var listo = function () { var t = b.textContent; b.textContent = 'Copiado ✓'; setTimeout(function () { b.textContent = 'Copiar correo'; }, 1500); };
            if (navigator.clipboard && navigator.clipboard.writeText) { navigator.clipboard.writeText(correo).then(listo, function () { prompt('Copia el correo:', correo); }); }
            else { prompt('Copia el correo:', correo); }
        });
    });
})();
</script>
@endpush    