{{--
    Botones reutilizables de VOID.
--}}
@props(['variant' => 'primary', 'href' => null, 'type' => 'button'])
@php
    $base = 'inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-neutral-950 disabled:opacity-50 disabled:cursor-not-allowed';
    $variants = [
        'primary' => 'bg-amber-500 text-neutral-950 border border-amber-400 hover:bg-amber-400 hover:shadow-lg hover:shadow-amber-500/10 focus:ring-amber-400',
        'secondary' => 'bg-neutral-800 text-neutral-200 border border-neutral-700 hover:bg-neutral-700 hover:border-neutral-600 focus:ring-primary-500',
        'danger' => 'bg-red-600 text-white border border-red-500 hover:bg-red-500 focus:ring-red-500',
        'ghost' => 'text-neutral-400 hover:text-amber-300 hover:bg-neutral-800 focus:ring-primary-500',
    ];
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif