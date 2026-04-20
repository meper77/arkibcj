<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-uitm-purple-700 tracking-tight">Sistem Arkib UiTM</h1>
        <p class="mt-1 text-sm text-stone-500">Sila log masuk untuk meneruskan</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zM12 14v7m-8-7h16" /></svg>
                </span>
                <x-text-input id="email" class="block w-full pl-9" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c.83 0 1.5.67 1.5 1.5v2a1.5 1.5 0 11-3 0v-2c0-.83.67-1.5 1.5-1.5zM7 11V7a5 5 0 0110 0v4M5 11h14a1 1 0 011 1v8a1 1 0 01-1 1H5a1 1 0 01-1-1v-8a1 1 0 011-1z" /></svg>
                </span>
                <x-text-input id="password" class="block w-full pl-9"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-stone-300 text-uitm-purple-700 shadow-sm focus:ring-uitm-purple-200" name="remember">
                <span class="ms-2 text-sm text-stone-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-uitm-purple-700 hover:text-uitm-purple-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uitm-purple-300 transition" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
