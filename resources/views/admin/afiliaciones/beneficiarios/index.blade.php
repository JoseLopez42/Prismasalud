@extends('admin.layout')

@section('titulo', 'Beneficiarios')
@section('encabezado', 'Afiliaciones · Beneficiarios')

@section('contenido')

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h2 id="titulo-listado" class="text-xl font-bold text-gray-900">Beneficiarios</h2>
            <p class="text-sm text-gray-500">Personas afiliadas al sistema de salud, separadas por cotizantes y beneficiarios</p>
        </div>
        <a href="{{ route('beneficiarios.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 hover:shadow-md transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo cotizante
        </a>
    </div>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">{{ session('exito') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
        <div class="relative sm:w-80">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" id="buscador-listado" placeholder="Buscar..."
                   class="w-full border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-sm bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
        </div>
        <span id="contador-resultados" class="text-xs text-gray-400 font-medium"></span>
    </div>

    @php
        $cotizantes = $beneficiarios->where('tipo_persona', 'cotizante');
        $familiares = $beneficiarios->where('tipo_persona', 'beneficiario');
    @endphp

    {{-- Cotizantes --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Cotizantes</h3>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
            <table id="tabla-listado" class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3">C.C.</th>
                        <th class="px-4 py-3">Nombres</th>
                        <th class="px-4 py-3">EPS</th>
                        <th class="px-4 py-3">Edad</th>
                        <th class="px-4 py-3">Teléfono</th>
                        <th class="px-4 py-3">Correo</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($cotizantes as $b)
                        <tr class="fila-dato hover:bg-teal-50/40 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $b->cc_afiliado }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $b->nombres }} {{ $b->apellidos }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->eps?->nombre_eps ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->edad }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->telefono }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->correo }}</td>
                            <td class="px-4 py-3">
                                @if ($b->usuario?->activo ?? true)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button type="button"
                                        class="btn-acciones inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 hover:border-gray-300 transition"
                                        aria-label="Acciones" aria-haspopup="menu" aria-expanded="false"
                                        data-estado-url="{{ route('beneficiarios.estado', $b->cc_afiliado) }}"
                                        data-edit-url="{{ route('beneficiarios.edit', $b->cc_afiliado) }}"
                                        data-activo="{{ ($b->usuario?->activo ?? true) ? '1' : '0' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <circle cx="10" cy="4" r="1.6"/><circle cx="10" cy="10" r="1.6"/><circle cx="10" cy="16" r="1.6"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr class="fila-vacio">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">No hay cotizantes registrados.</td>
                        </tr>
                    @endforelse
                    <tr id="fila-vacio-busqueda" class="fila-vacio oculto">
                        <td colspan="7" class="px-4 py-12 text-center">
                            <p class="text-sm font-semibold text-gray-500">Sin resultados</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Beneficiarios --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Beneficiarios (familiares)</h3>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3">C.C.</th>
                        <th class="px-4 py-3">Nombres</th>
                        <th class="px-4 py-3">Familiar de</th>
                        <th class="px-4 py-3">EPS</th>
                        <th class="px-4 py-3">Parentesco</th>
                        <th class="px-4 py-3">Edad</th>
                        <th class="px-4 py-3">Teléfono</th>
                        <th class="px-4 py-3">Correo</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($familiares as $b)
                        <tr class="hover:bg-teal-50/40 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $b->cc_afiliado }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $b->nombres }} {{ $b->apellidos }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $b->cotizante?->nombres }} {{ $b->cotizante?->apellidos }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->eps?->nombre_eps ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->parentesco?->nombre ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->edad }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->telefono }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->correo }}</td>
                            <td class="px-4 py-3">
                                @if ($b->usuario?->activo ?? true)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button type="button"
                                        class="btn-acciones inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 hover:border-gray-300 transition"
                                        aria-label="Acciones" aria-haspopup="menu" aria-expanded="false"
                                        data-estado-url="{{ route('beneficiarios.estado', $b->cc_afiliado) }}"
                                        data-edit-url="{{ route('beneficiarios.edit', $b->cc_afiliado) }}"
                                        data-activo="{{ ($b->usuario?->activo ?? true) ? '1' : '0' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <circle cx="10" cy="4" r="1.6"/><circle cx="10" cy="10" r="1.6"/><circle cx="10" cy="16" r="1.6"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">No hay beneficiarios familiares registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Menú de acciones (3 puntos), compartido por todas las filas --}}
    <div id="menu-acciones"
         class="fixed z-50 hidden w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-1 text-sm"
         role="menu" aria-label="Acciones de la persona">
        <form id="form-estado-menu" method="POST" action="">
            @csrf
            @method('PATCH')
            <button type="submit" id="btn-estado-menu"
                    class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                    role="menuitem">Desactivar</button>
        </form>
        <a id="link-editar-menu" href="#"
           class="block px-4 py-2 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
           role="menuitem">Editar</a>
    </div>

@endsection

@push('scripts')
    @include('admin.partials.script_listado')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menu = document.getElementById('menu-acciones');
            const formEstado = document.getElementById('form-estado-menu');
            const botonEstado = document.getElementById('btn-estado-menu');
            const enlaceEditar = document.getElementById('link-editar-menu');
            let disparadorActual = null;

            function cerrarMenu() {
                menu.classList.add('hidden');
                if (disparadorActual) disparadorActual.setAttribute('aria-expanded', 'false');
                disparadorActual = null;
            }

            document.addEventListener('click', function (event) {
                const disparador = event.target.closest('.btn-acciones');

                if (disparador) {
                    event.preventDefault();
                    const abrir = disparador !== disparadorActual;
                    cerrarMenu();
                    if (!abrir) return;

                    formEstado.action = disparador.dataset.estadoUrl;
                    enlaceEditar.href = disparador.dataset.editUrl;
                    botonEstado.textContent = disparador.dataset.activo === '1' ? 'Desactivar' : 'Activar';

                    // Se muestra primero para poder medirlo y se coloca cerca
                    // del botón usando position: fixed (no lo corta la tabla).
                    menu.classList.remove('hidden');
                    const rect = disparador.getBoundingClientRect();
                    const ancho = menu.offsetWidth;
                    const alto = menu.offsetHeight;
                    let top = rect.bottom + 6;
                    if (top + alto > window.innerHeight - 8) top = rect.top - alto - 6;

                    menu.style.left = Math.max(8, rect.right - ancho) + 'px';
                    menu.style.top = Math.max(8, top) + 'px';

                    disparador.setAttribute('aria-expanded', 'true');
                    disparadorActual = disparador;
                    return;
                }

                if (!menu.contains(event.target)) cerrarMenu();
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') cerrarMenu();
            });

            window.addEventListener('scroll', cerrarMenu, true);
            window.addEventListener('resize', cerrarMenu);
        });
    </script>
@endpush