@props(['title' => null, 'subtitle' => null, 'padding' => true])

<div
    {{ $attributes->merge(['class' => 'void-card bg-neutral-900 rounded-2xl border border-neutral-800 shadow-xl shadow-black/20']) }}>
    @if ($title)
        <div class="px-5 sm:px-6 py-4 border-b border-neutral-800 flex items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-semibold text-neutral-100">{{ $title }}</h3>
                @if ($subtitle)
                    <p class="text-sm text-neutral-400 mt-1 leading-relaxed">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding ? 'p-5 sm:p-6' : '' }}">
        {{ $slot }}
    </div>
</div>
