@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-stone-700 tracking-wide']) }}>
    {{ $value ?? $slot }}
</label>
