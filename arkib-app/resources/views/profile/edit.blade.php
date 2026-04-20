<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
            Profil
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Profile Info Display + edit -->
            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-uitm-purple-700 tracking-tight">Maklumat Profil</h3>
                    <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>
                </div>

                @if(session('status') === 'profile-updated')
                    <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm flex items-center gap-2">
                        <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Profil berjaya dikemaskini.
                    </div>
                @endif

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 p-4 bg-uitm-purple-50/40 rounded-lg border border-uitm-purple-100 border-l-4 border-l-uitm-gold-400">
                    <div>
                        <dt class="text-xs text-uitm-purple-700 uppercase font-medium tracking-wider">Nama Penuh</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-stone-900">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-uitm-purple-700 uppercase font-medium tracking-wider">Emel</dt>
                        <dd class="mt-0.5 text-sm text-stone-900">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-uitm-purple-700 uppercase font-medium tracking-wider">Kampus</dt>
                        <dd class="mt-0.5 text-sm text-stone-900">{{ $user->kampus }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-uitm-purple-700 uppercase font-medium tracking-wider">Cawangan</dt>
                        <dd class="mt-0.5 text-sm text-stone-900">{{ $user->cawangan ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-uitm-purple-700 uppercase font-medium tracking-wider">Fakulti/Bahagian</dt>
                        <dd class="mt-0.5 text-sm text-stone-900">{{ $user->fakulti_bahagian ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-uitm-purple-700 uppercase font-medium tracking-wider">Jawatan</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-stone-900">
                            @if($user->position)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-uitm-gold-100 text-uitm-purple-800 text-xs font-semibold tracking-wide">{{ $user->position }}</span>
                            @else
                                <span class="text-stone-400">—</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                <form method="POST" action="{{ route('profile.update') }}" x-data class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="name" :value="__('Nama Penuh')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required x-soft-rule="uppercase" />
                            <x-input-error class="mt-1" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="kampus" :value="__('Kampus')" />
                            <x-text-input id="kampus" name="kampus" type="text" class="mt-1 block w-full" :value="old('kampus', $user->kampus)" required x-soft-rule="uppercase" />
                            <x-input-error class="mt-1" :messages="$errors->get('kampus')" />
                        </div>

                        <div>
                            <x-input-label for="cawangan" :value="__('Cawangan')" />
                            <x-text-input id="cawangan" name="cawangan" type="text" class="mt-1 block w-full" :value="old('cawangan', $user->cawangan)" x-soft-rule="uppercase" />
                            <x-input-error class="mt-1" :messages="$errors->get('cawangan')" />
                        </div>

                        <div>
                            <x-input-label for="fakulti_bahagian" :value="__('Fakulti/Bahagian')" />
                            <x-text-input id="fakulti_bahagian" name="fakulti_bahagian" type="text" class="mt-1 block w-full" :value="old('fakulti_bahagian', $user->fakulti_bahagian)" x-soft-rule="uppercase" />
                            <x-input-error class="mt-1" :messages="$errors->get('fakulti_bahagian')" />
                        </div>
                    </div>

                    <div class="pt-2">
                        <x-primary-button>Simpan Profil</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-uitm-purple-700 tracking-tight">Tukar Kata Laluan</h3>
                    <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>
                    <p class="mt-2 text-xs text-stone-500">Kata laluan baharu mesti sekurang-kurangnya 8 aksara.</p>
                </div>

                @if(session('status') === 'password-updated')
                    <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm flex items-center gap-2">
                        <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Kata laluan berjaya ditukar.
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4 max-w-sm" x-data="{ show: false }">
                        <div class="flex items-center justify-end -mb-2">
                            <button type="button" @click="show = !show"
                                    class="inline-flex items-center gap-1 text-xs text-stone-500 hover:text-uitm-purple-700 focus:outline-none">
                                <svg x-show="!show" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg x-show="show" x-cloak class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908A3 3 0 1115 12M9.88 9.88l4.24 4.24M3 3l18 18"/></svg>
                                <span x-text="show ? 'Sembunyikan' : 'Tunjuk kata laluan'"></span>
                            </button>
                        </div>

                        <div>
                            <x-input-label for="current_password" :value="__('Kata Laluan Semasa')" />
                            <x-text-input id="current_password" name="current_password" type="password" ::type="show ? 'text' : 'password'" class="mt-1 block w-full" autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('current_password')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Kata Laluan Baharu (minimum 8 aksara)')" />
                            <x-text-input id="password" name="password" type="password" ::type="show ? 'text' : 'password'" class="mt-1 block w-full" minlength="8" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Sahkan Kata Laluan Baharu')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" ::type="show ? 'text' : 'password'" class="mt-1 block w-full" minlength="8" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                        </div>

                        <div class="pt-1">
                            <x-primary-button>Tukar Kata Laluan</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Transfer Position -->
            @if($user->position)
            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl p-6">
                <div class="mb-3">
                    <h3 class="text-lg font-semibold text-uitm-purple-700 tracking-tight">Pindah Jawatan</h3>
                    <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>
                </div>
                <p class="text-sm text-stone-600 mb-4">
                    Anda memegang jawatan
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-uitm-gold-100 text-uitm-purple-800 text-xs font-semibold tracking-wide">{{ $user->position }}</span>.
                    Pindahkan kepada pengguna lain jika perlu.
                </p>

                @if(session('status') === 'position-transferred')
                    <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                        Jawatan berjaya dipindahkan.
                    </div>
                @endif

                @if($errors->has('transfer'))
                    <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-sm">
                        {{ $errors->first('transfer') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.transfer-position') }}"
                      onsubmit="return confirm('Pindahkan jawatan {{ $user->position }} kepada pengguna yang dipilih?')">
                    @csrf

                    @php
                        $otherUsers = \App\Models\User::where('id', '!=', $user->id)
                            ->where('is_superadmin', false)
                            ->orderBy('name')
                            ->get();
                    @endphp

                    <div class="max-w-sm">
                        <x-input-label for="target_user_id" :value="__('Pindah kepada')" />
                        <select name="target_user_id" required
                                class="mt-1 block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                            <option value="">— Pilih Pengguna —</option>
                            @foreach($otherUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('target_user_id')" class="mt-1" />

                        <div class="mt-4">
                            <x-primary-button>Pindah Jawatan</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
