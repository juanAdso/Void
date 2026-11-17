@props(['name', 'title' => null, 'maxWidth' => 'md'])
@php
    $widths = ['sm' => 'max-w-sm', 'md' => 'max-w-md', 'lg' => 'max-w-lg', 'xl' => 'max-w-xl'];
    $width = $widths[$maxWidth] ?? $widths['md'];
@endphp
<div x-data="{ show: false }" x-show="show" x-cloak
    @open-modal.window="if ($event.detail === '{{ $name }}') show = true" @keydown.escape.window="show = false"
    class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true"
    aria-labelledby="modal-title-{{ $name }}">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div x-show="show" x-transition.opacity.duration.200ms @click="show = false"
            class="fixed inset-0 bg-black/75 backdrop-blur-sm" aria-hidden="true"></div>
        <div x-show="show" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="relative bg-neutral-900 border border-neutral-800 rounded-2xl shadow-2xl shadow-black/50 w-full {{ $width }} overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-neutral-800 flex items-center justify-between">
                <h3 id="modal-title-{{ $name }}" class="text-base font-semibold text-neutral-100">
                    {{ $title }}</h3>
                <button @click="show = false" type="button"
                    class="text-neutral-300 hover:text-amber-300 focus:outline-none" aria-label="Cerrar modal">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-5 sm:px-6 py-5 space-y-3">{{ $slot }}</div>
            @isset($footer)
                <div class="px-5 sm:px-6 py-4 border-t border-neutral-800 flex justify-end gap-3 bg-neutral-950">
                    {{ $footer }}</div>
            @endisset
        </div>
    </div>
</div>
