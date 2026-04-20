@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-uitm-purple-700 text-start text-base font-semibold text-uitm-purple-700 bg-uitm-purple-50 focus:outline-none focus:text-uitm-purple-800 focus:bg-uitm-purple-100 focus:border-uitm-purple-800 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-stone-600 hover:text-uitm-purple-700 hover:bg-uitm-purple-50 hover:border-uitm-gold-400 focus:outline-none focus:text-uitm-purple-700 focus:bg-uitm-purple-50 focus:border-uitm-gold-400 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
