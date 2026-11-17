
@props(['label', 'value', 'change' => null, 'trend' => 'up', 'icon' => 'chart'])
<div class="void-stat-card rounded-2xl border border-neutral-800 bg-neutral-900 p-5 sm:p-6 shadow-lg shadow-black/10">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm font-medium text-neutral-400">{{ $label }}</p>
            <p class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-neutral-50">{{ $value }}</p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/15 flex items-center justify-center flex-shrink-0">
            <span class="w-5 h-5 block">@include('layouts.partials.icons', ['icon' => $icon])</span>
        </div>
    </div>
    @if ($change)
        <div class="mt-4 flex items-center gap-1.5 text-sm">
            <span class="inline-flex items-center gap-1 font-medium {{ $trend === 'up' ? 'text-emerald-400' : 'text-red-400' }}">
                @if ($trend === 'up')
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                @else
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                @endif
                {{ $change }}
            </span>
            <span class="text-neutral-500">vs. mes anterior</span>
        </div>
    @endif
</div>