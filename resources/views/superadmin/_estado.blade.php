{{-- Etiqueta de estado de una licencia. Uso: @include('superadmin._estado', ['licencia' => $licencia]) --}}
@if ($licencia->estado === 'vencida')
    <span class="inline-block rounded-full bg-red-50 text-red-600 text-xs font-bold px-2.5 py-1">Vencida</span>
@elseif ($licencia->estado === 'inactiva')
    <span class="inline-block rounded-full bg-yellow-50 text-yellow-700 text-xs font-bold px-2.5 py-1">Pendiente</span>
@elseif ($licencia->estado === 'suspendida')
    <span class="inline-block rounded-full bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1">Suspendida</span>
@elseif ($licencia->estaVencida())
    <span class="inline-block rounded-full bg-red-50 text-red-600 text-xs font-bold px-2.5 py-1">Vencida</span>
@elseif ($licencia->estaPorVencer())
    <span class="inline-block rounded-full bg-amber-50 text-amber-600 text-xs font-bold px-2.5 py-1">Por vencer</span>
@elseif ($licencia->estado === 'prueba')
    <span class="inline-block rounded-full bg-sky-50 text-sky-700 text-xs font-bold px-2.5 py-1">En prueba</span>
@else
    <span class="inline-block rounded-full bg-teal-50 text-teal-700 text-xs font-bold px-2.5 py-1">Activa</span>
@endif