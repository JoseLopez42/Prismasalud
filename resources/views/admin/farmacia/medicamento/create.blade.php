@extends('admin.layout')

@section('titulo', 'Nuevo medicamento')
@section('encabezado', 'Farmacia · Nuevo medicamento')

@push('estilos')
<style>
    @keyframes aparecer {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .alerta-animada { animation: aparecer .25s ease; }

    @keyframes sacudir {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }
    .campo-invalido { animation: sacudir .3s ease; border-color: #ef4444 !important; }

    .spinner {
        width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: girar .6s linear infinite;
    }
    @keyframes girar { to { transform: rotate(360deg); } }
</style>
@endpush

@section('contenido')

    <a href="{{ route('medicamento.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a medicamentos
    </a>

    <div class="max-w-5xl mt-4">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.5 20.5L3.5 13.5a5 5 0 117.07-7.07l7 7a5 5 0 01-7.07 7.07z"/><line x1="8.5" y1="8.5" x2="15.5" y2="15.5" stroke-linecap="round"/></svg></div>
                    <div>
                        <p class="font-bold text-lg leading-tight">Nuevo medicamento</p>
                        <p class="text-xs text-white/70">Catálogo de medicamentos</p>
                    </div>
                </div>
            </div>

            <div class="px-6 pt-5">
                <div class="flex gap-6 border-b border-gray-200">
                    <a href="{{ route('medicamento.create') }}"
                       class="-mb-px pb-3 text-sm font-semibold border-b-2 {{ $modoApi ? 'border-transparent text-gray-500 hover:text-teal-700' : 'border-teal-600 text-teal-700' }}">
                        Crear manualmente
                    </a>
                    <a href="{{ route('medicamento.create', ['origen' => 'api']) }}"
                       class="-mb-px pb-3 text-sm font-semibold border-b-2 {{ $modoApi ? 'border-teal-600 text-teal-700' : 'border-transparent text-gray-500 hover:text-teal-700' }}">
                        Buscar por API
                    </a>
                </div>
                <p class="text-xs text-gray-500 mt-3">
                    @if ($modoApi)
                        Consulta el catálogo CUM del Invima y guarda el medicamento seleccionado directamente en el catálogo.
                    @else
                        Digita los datos del medicamento o cambia a “Buscar por API” para traerlo del Invima.
                    @endif
                </p>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if ($modoApi)

                    <form method="GET" action="{{ route('medicamento.create') }}" class="flex flex-col sm:flex-row sm:items-center gap-3 mb-5">
                        <input type="hidden" name="origen" value="api">
                        <div class="relative flex-1">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input type="text" name="buscar" value="{{ $buscarApi }}"
                                   placeholder="Buscar: acetaminofen, losartan, dolex..."
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>
                        <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                            Buscar en la API
                        </button>
                        @if ($buscarApi !== '')
                            <a href="{{ route('medicamento.create', ['origen' => 'api']) }}" class="text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
                        @endif
                    </form>

                    @if ($errorApi)
                        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md px-4 py-3 mb-4">
                            <p class="font-bold">No se pudo consultar la API.</p>
                            <p><strong>Código HTTP:</strong> {{ $errorApi['codigo'] }}</p>
                            <p><strong>Detalle:</strong> {{ $errorApi['detalle'] }}</p>
                        </div>
                    @endif

                    @if ($paginadorApi && $paginadorApi->total() > 0)
                        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="px-4 py-3">#</th>
                                        <th class="px-4 py-3">Código</th>
                                        <th class="px-4 py-3">Nombre</th>
                                        <th class="px-4 py-3">Presentación</th>
                                        <th class="px-4 py-3">Descripción</th>
                                        <th class="px-4 py-3">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($paginadorApi as $med)
                                        @php
                                            $codigoClave = mb_strtolower($med['codigo']);
                                            $nombreClave = mb_strtolower($med['nombre']);
                                            $yaRegistrado = isset($existentesPorCodigo[$codigoClave]) || isset($existentesPorNombre[$nombreClave]);
                                        @endphp
                                        <tr class="hover:bg-teal-50/40 transition">
                                            <td class="px-4 py-3 text-gray-400">
                                                {{ ($paginadorApi->currentPage() - 1) * $paginadorApi->perPage() + $loop->index + 1 }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex font-mono text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-md border border-teal-100">
                                                    {{ $med['codigo'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $med['nombre'] }}</td>
                                            <td class="px-4 py-3 text-gray-600">{{ $med['presentacion'] ?: '—' }}</td>
                                            <td class="px-4 py-3 text-gray-600 max-w-xs truncate" title="{{ $med['descripcion'] }}">
                                                {{ $med['descripcion'] ?: '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if ($yaRegistrado)
                                                    <span class="inline-flex items-center text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md">
                                                        Ya en catálogo
                                                    </span>
                                                @else
                                                    <form method="POST" action="{{ route('medicamento.store') }}" data-guardar-api>
                                                        @csrf
                                                        <input type="hidden" name="origen" value="api">
                                                        <input type="hidden" name="codigo" value="{{ $med['codigo'] }}">
                                                        <input type="hidden" name="nombre" value="{{ $med['nombre'] }}">
                                                        <input type="hidden" name="presentacion" value="{{ $med['presentacion'] }}">
                                                        <input type="hidden" name="descripcion" value="{{ $med['descripcion'] }}">
                                                        <button type="submit"
                                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-md hover:bg-green-700 transition disabled:opacity-70 disabled:cursor-not-allowed">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                            Guardar en catálogo
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($paginadorApi->total() > $paginadorApi->perPage())
                            <div class="mt-4">
                                {{ $paginadorApi->onEachSide(1)->links() }}
                            </div>
                        @endif
                    @elseif (!$errorApi)
                        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm px-4 py-12 text-center text-sm text-gray-500">
                            No se encontraron medicamentos para la búsqueda.
                        </div>
                    @endif

                @else

                    <div class="max-w-2xl">
                        <form id="form-medicamento" method="POST" action="{{ route('medicamento.store') }}" class="space-y-5" novalidate>
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Código del medicamento *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V5a2 2 0 012-2h2M4 17v2a2 2 0 002 2h2m8-20h2a2 2 0 012 2v2m-4 16h2a2 2 0 002-2v-2M7 7h10v10H7z"/></svg></span>
                                    <input type="text" name="codigo" value="{{ old('codigo') }}"
                                           class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition font-mono"
                                           placeholder="Ej: ACE-500" required maxlength="50" autofocus>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Es la llave del medicamento: debe ser única y no se podrá cambiar luego.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del medicamento *</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                       placeholder="Ej: Acetaminofén" required maxlength="150">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Presentación <span class="text-gray-400 font-normal">(opcional)</span></label>
                                <input type="text" name="presentacion" value="{{ old('presentacion') }}"
                                       class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                       placeholder="Ej: Tableta 500 mg" maxlength="100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción <span class="text-gray-400 font-normal">(opcional)</span></label>
                                <textarea name="descripcion" rows="3"
                                          class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                          placeholder="Breve descripción del medicamento...">{{ old('descripcion') }}</textarea>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <a href="{{ route('medicamento.index') }}"
                                   class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Cancelar
                                </a>
                                <button type="submit" id="boton-guardar-medicamento"
                                        class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                                    <span id="texto-boton-medicamento" class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Crear medicamento</span>
                                </button>
                            </div>
                        </form>
                    </div>

                @endif
            </div>
        </div>
    </div>

    <script>
        const formMedicamento = document.getElementById('form-medicamento');

        if (formMedicamento) {
            const botonGuardarMedicamento = document.getElementById('boton-guardar-medicamento');
            const textoBotonMedicamento = document.getElementById('texto-boton-medicamento');

            formMedicamento.addEventListener('submit', function (evento) {
                const camposInvalidos = Array.from(formMedicamento.querySelectorAll('.campo-form'))
                    .filter(campo => !campo.checkValidity());

                if (camposInvalidos.length > 0) {
                    evento.preventDefault();
                    camposInvalidos.forEach(campo => {
                        campo.classList.add('campo-invalido');
                        campo.addEventListener('animationend', () => campo.classList.remove('campo-invalido'), { once: true });
                    });
                    camposInvalidos[0].focus();
                    return;
                }

                botonGuardarMedicamento.disabled = true;
                textoBotonMedicamento.innerHTML = '<span class="spinner"></span> Guardando...';
            });
        }

        document.querySelectorAll('form[data-guardar-api]').forEach(formulario => {
            formulario.addEventListener('submit', () => {
                const boton = formulario.querySelector('button[type="submit"]');
                boton.disabled = true;
                boton.innerHTML = '<span class="spinner"></span> Guardando...';
            });
        });
    </script>

@endsection
