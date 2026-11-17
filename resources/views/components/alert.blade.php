@props(['type' => 'info'])
@php
    $map = [
        'success' => ['bg-emerald-950/70 text-emerald-200 border-emerald-800', 'text-emerald-300'],
        'error' => ['bg-red-950/70 text-red-200 border-red-800', 'text-red-300'],
        'warning' => ['bg-amber-950/60 text-amber-100 border-amber-800', 'text-amber-300'],
        'info' => ['bg-blue-950/60 text-blue-100 border-blue-800', 'text-blue-300'],
    ];
    [$classes, $iconColor] = $map[$type] ?? $map['info'];
@endphp
<div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.200ms role="alert"
    {{ $attributes->merge(['class' => "flex items-start gap-3 border rounded-xl px-4 py-3 text-sm shadow-lg shadow-black/10 $classes"]) }}>
    <div class="flex-1">{{ $slot }}</div>
    <button @click="show = false" type="button" class="{{ $iconColor }} hover:opacity-70 focus:outline-none"
        aria-label="Cerrar alerta">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
