<section class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl p-6">
    <header class="mb-5">
        <h2 class="text-lg font-semibold text-uitm-purple-700 tracking-tight">
            {{ __('Profile Information') }}
        </h2>
        <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>

        <p class="mt-3 text-sm text-stone-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-sm text-amber-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="ml-1 underline text-sm text-uitm-purple-700 hover:text-uitm-purple-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-uitm-purple-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-emerald-700">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-700 font-medium"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
