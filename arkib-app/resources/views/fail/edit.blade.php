<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('fail.index') }}" class="inline-flex items-center justify-center h-8 w-8 rounded-full text-stone-500 hover:bg-stone-100 hover:text-uitm-purple-700 transition">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/></svg>
            </a>
            <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
                Kemaskini Fail
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-uitm-purple-700 tracking-tight">Maklumat Fail</h3>
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

                <form method="POST" action="{{ route('fail.update', $fail) }}" x-data class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <!-- Read-only fields -->
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">NO. RUJUKAN</label>
                        <input type="text" readonly value="{{ $fail->noRujukan->no_rujukan_full }}"
                               class="block w-full rounded-lg bg-stone-50 border-stone-200 shadow-sm text-sm font-mono text-stone-600 cursor-not-allowed">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">JILID</label>
                            <input type="text" readonly value="{{ $fail->jilid }}"
                                   class="block w-full rounded-lg bg-stone-50 border-stone-200 shadow-sm text-sm text-stone-600 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">TARIKH KANDUNGAN PERTAMA</label>
                            <input type="text" readonly value="{{ $fail->tarikh_pertama?->format('d/m/Y') }}"
                                   class="block w-full rounded-lg bg-stone-50 border-stone-200 shadow-sm text-sm text-stone-600 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="border-t border-stone-200 pt-4">
                        <p class="text-xs uppercase tracking-wider font-medium text-uitm-purple-700 mb-3">Maklumat Boleh Dikemaskini</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1">TARIKH KANDUNGAN AKHIR</label>
                                <input type="date" name="tarikh_akhir"
                                       value="{{ old('tarikh_akhir', $fail->tarikh_akhir?->format('Y-m-d')) }}"
                                       class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                                <x-input-error :messages="$errors->get('tarikh_akhir')" class="mt-1" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1">TARIKH TUTUP</label>
                                <input type="date" name="tarikh_tutup"
                                       value="{{ old('tarikh_tutup', $fail->tarikh_tutup?->format('Y-m-d')) }}"
                                       class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                                <x-input-error :messages="$errors->get('tarikh_tutup')" class="mt-1" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1">NO. KOTAK</label>
                                <input type="text" name="kotak" x-soft-rule="digits"
                                       value="{{ old('kotak', $fail->kotak) }}"
                                       class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition"
                                       placeholder="1">
                                <p class="mt-1 text-xs text-stone-500">Nombor sahaja</p>
                                <x-input-error :messages="$errors->get('kotak')" class="mt-1" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1">PERSON IN CHARGE</label>
                                <input type="text" readonly value="{{ Auth::user()->name }}"
                                       class="block w-full rounded-lg bg-stone-50 border-stone-200 shadow-sm text-sm text-stone-600 cursor-not-allowed">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-stone-700 mb-1">KERTAS-KERTAS YANG BERHUBUNG</label>
                                <select name="kertas_berhubung_id"
                                        class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                                    <option value="">— Tiada —</option>
                                    @foreach($availableKertas as $item)
                                        <option value="{{ $item->id }}" {{ old('kertas_berhubung_id', $fail->kertas_berhubung_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->noRujukan->no_rujukan_full . ($item->jilid > 1 ? ' Jld.'.$item->jilid : '') }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('kertas_berhubung_id')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-stone-200 pt-5">
                        <a href="{{ route('fail.index') }}"
                           class="px-4 py-2 bg-stone-100 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-200 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 transition">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            Kemaskini
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
