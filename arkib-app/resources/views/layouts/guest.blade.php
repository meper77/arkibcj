<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Arkib System UiTM') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-stone-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0
                    bg-white relative overflow-hidden">
            <div class="relative">
                <a href="/" class="inline-flex items-center justify-center">
                    <x-application-logo class="h-24 w-auto" />
                </a>
            </div>

            <div class="relative w-full sm:max-w-md mt-6 bg-stone-50 shadow-uitm-lg overflow-hidden sm:rounded-xl ring-1 ring-stone-200">
                <!-- Gold accent top bar -->
                <div class="h-1.5 bg-gradient-to-r from-uitm-gold-400 via-uitm-gold-500 to-uitm-gold-400"></div>
                <div class="px-6 py-6">
                    {{ $slot }}
                </div>
            </div>

            <p class="relative mt-6 text-xs text-stone-500 tracking-wide">
                &copy; {{ date('Y') }} Universiti Teknologi MARA
            </p>
        </div>
    </body>
</html>
