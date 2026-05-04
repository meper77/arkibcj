<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center h-8 w-8 rounded-full text-stone-500 hover:bg-stone-100 hover:text-uitm-purple-700 transition">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/></svg>
            </a>
            <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
                Tambah Pengguna
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-uitm-purple-700 tracking-tight">Maklumat Pengguna Baharu</h3>
                    <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>
                </div>

                @if($errors->any())
                    <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-5 p-3 bg-uitm-gold-50 border border-uitm-gold-200 rounded-lg flex items-center gap-2 text-sm text-uitm-purple-800">
                    <svg class="h-4 w-4 text-uitm-gold-600 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    Kata laluan lalai: <strong class="font-mono">password</strong>
                </div>

                <form method="POST" action="{{ route('users.store') }}" x-data class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-stone-700 mb-1">NAMA PENUH <span class="text-uitm-purple-700">*</span></label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                   x-soft-rule="uppercase"
                                   class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-stone-700 mb-1">EMEL <span class="text-uitm-purple-700">*</span></label>
                            <input type="email" name="email" required value="{{ old('email') }}"
                                   class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition"
                                   placeholder="nama@uitm.edu.my">
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">KAMPUS</label>
                            <input type="text" name="kampus" value="{{ old('kampus', 'UiTM') }}"
                                   x-soft-rule="uppercase"
                                   class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                            <x-input-error :messages="$errors->get('kampus')" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">CAWANGAN</label>
                            <input type="text" name="cawangan" value="{{ old('cawangan') }}"
                                   x-soft-rule="uppercase"
                                   class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition"
                                   placeholder="SEGAMAT">
                            <x-input-error :messages="$errors->get('cawangan')" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">FAKULTI/BAHAGIAN</label>
                            <select name="fakulti_bahagian_id"
                                    class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                                <option value="">— Tiada —</option>
                                @foreach($fakultis as $fb)
                                <option value="{{ $fb->id }}" {{ old('fakulti_bahagian_id') == $fb->id ? 'selected' : '' }}>{{ $fb->nama }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('fakulti_bahagian_id')" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">JAWATAN</label>
                            <select name="position"
                                    class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                                <option value="">— Tiada —</option>
                                <option value="PTRJ" {{ old('position') === 'PTRJ' ? 'selected' : '' }}>PTRJ</option>
                                <option value="PRJ" {{ old('position') === 'PRJ' ? 'selected' : '' }}>PRJ</option>
                            </select>
                            <x-input-error :messages="$errors->get('position')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-stone-200 pt-5">
                        <a href="{{ route('users.index') }}"
                           class="px-4 py-2 bg-stone-100 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-200 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 transition">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a.75.75 0 01.75.75v3.5h3.5a.75.75 0 010 1.5h-3.5v3.5a.75.75 0 01-1.5 0v-3.5h-3.5a.75.75 0 010-1.5h3.5v-3.5A.75.75 0 0110 5z" clip-rule="evenodd"/></svg>
                            Tambah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
