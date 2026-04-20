@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-uitm-purple-700 text-sm font-semibold leading-5 text-uitm-purple-700 focus:outline-none focus:border-uitm-purple-800 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-stone-600 hover:text-uitm-purple-700 hover:border-uitm-gold-400 focus:outline-none focus:text-uitm-purple-700 focus:border-uitm-gold-400 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
