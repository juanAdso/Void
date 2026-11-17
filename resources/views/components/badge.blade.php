{{--
    Estados para listados y tablas.
--}}
@props(['status'])
@php
    $map = [
        'activo' => 'bg-emerald-950 text-emerald-300 ring-emerald-700/50',
        'pendiente' => 'bg-amber-950 text-amber-300 ring-amber-700/50',
        'inactivo' => 'bg-neutral-800 text-neutral-300 ring-neutral-600/50',
    ];
    $key = strtolower($status);
    $classes = $map[$key] ?? 'bg-neutral-800 text-neutral-300 ring-neutral-600/50';
@endphp
<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $classes }}">{{ $status }}</span>