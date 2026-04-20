<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-uitm-purple-700 tracking-tight">Sistem Arkib UiTM</h1>
        <p class="mt-1 text-sm text-stone-500">Daftar akaun baharu</p>
    </div>

    <form method="POST" action="{{ route('register') }}" x-data>
        @csrf

        <!-- Nama Penuh -->
        <div>
            <x-input-label for="name" :value="__('Nama Penuh')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="NAMA PENUH" x-soft-rule="uppercase" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Emel -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Emel (@uitm.edu.my)')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@uitm.edu.my" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Kata Laluan -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Laluan (tepat 8 aksara)')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" maxlength="8" minlength="8" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Sahkan Kata Laluan -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Sahkan Kata Laluan')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" maxlength="8" minlength="8" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Kampus -->
        <div class="mt-4">
            <x-input-label for="kampus" :value="__('Kampus')" />
            <x-text-input id="kampus" class="block mt-1 w-full" type="text" name="kampus" :value="old('kampus', 'UiTM')" required placeholder="UiTM" x-soft-rule="uppercase" />
            <x-input-error :messages="$errors->get('kampus')" class="mt-2" />
        </div>

        <!-- Cawangan -->
        <div class="mt-4">
            <x-input-label for="cawangan" :value="__('Cawangan')" />
            <x-text-input id="cawangan" class="block mt-1 w-full" type="text" name="cawangan" :value="old('cawangan')" placeholder="SEGAMAT" x-soft-rule="uppercase" />
            <x-input-error :messages="$errors->get('cawangan')" class="mt-2" />
        </div>

        <!-- Fakulti/Bahagian -->
        <div class="mt-4">
            <x-input-label for="fakulti_bahagian" :value="__('Fakulti/Bahagian')" />
            <x-text-input id="fakulti_bahagian" class="block mt-1 w-full" type="text" name="fakulti_bahagian" :value="old('fakulti_bahagian')" x-soft-rule="uppercase" />
            <x-input-error :messages="$errors->get('fakulti_bahagian')" class="mt-2" />
        </div>

        <!-- Jawatan -->
        <div class="mt-4">
            <x-input-label for="position" :value="__('Jawatan')" />
            <select id="position" name="position" class="block mt-1 w-full border-stone-300 rounded-md shadow-sm focus:border-uitm-purple-700 focus:ring-uitm-purple-200 transition duration-150 ease-in-out">
                <option value="" selected>— Tiada Jawatan —</option>
                <option value="PTRJ" {{ old('position') === 'PTRJ' ? 'selected' : '' }}>PTRJ</option>
                <option value="PRJ" {{ old('position') === 'PRJ' ? 'selected' : '' }}>PRJ</option>
            </select>
            <x-input-error :messages="$errors->get('position')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-uitm-purple-700 hover:text-uitm-purple-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uitm-purple-300 transition" href="{{ route('login') }}">
                {{ __('Sudah berdaftar?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
