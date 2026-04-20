<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-uitm-purple-700 tracking-tight">Sistem Arkib UiTM</h1>
        <p class="mt-1 text-sm text-stone-500">Sahkan kata laluan</p>
    </div>

    <div class="mb-4 text-sm text-stone-600 leading-relaxed">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-6">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
