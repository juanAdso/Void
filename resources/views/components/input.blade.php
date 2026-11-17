{{--
    Campo reutilizable de formulario.
--}}
@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'hint' => null])
<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-neutral-300 mb-1.5">{{ $label }}</label>
    @endif
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}"
        {{ $attributes->merge([
            'class' => 'w-full px-3.5 py-2.5 text-sm rounded-xl border ' .
                       ($errors->has($name) ? 'border-red-700 focus:ring-red-500' : 'border-neutral-700 focus:ring-primary-500') .
                       ' bg-neutral-950 text-neutral-100 focus:outline-none focus:ring-2 focus:border-transparent transition placeholder:text-neutral-600'
        ]) }}
        @if ($errors->has($name)) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif>
    @if ($hint && !$errors->has($name))
        <p class="mt-1.5 text-xs text-neutral-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
    @enderror
</div>