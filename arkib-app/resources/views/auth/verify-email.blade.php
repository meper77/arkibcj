<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-uitm-purple-700 tracking-tight">Sistem Arkib UiTM</h1>
        <p class="mt-1 text-sm text-stone-500">Sahkan alamat emel</p>
    </div>

    <div class="mb-4 text-sm text-stone-600 leading-relaxed">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 flex items-start gap-2 rounded-md border border-green-200 bg-green-50 px-3 py-2 text-sm font-medium text-green-700">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <span>{{ __('A new verification link has been sent to the email address you provided during registration.') }}</span>
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-uitm-purple-700 hover:text-uitm-purple-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uitm-purple-300 transition">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
