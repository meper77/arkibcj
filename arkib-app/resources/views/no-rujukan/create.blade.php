<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('no-rujukan.index') }}" class="inline-flex items-center justify-center h-8 w-8 rounded-full text-stone-500 hover:bg-stone-100 hover:text-uitm-purple-700 transition">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/></svg>
            </a>
            <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
                Daftar No. Rujukan
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-uitm-purple-700 tracking-tight">Maklumat No. Rujukan</h3>
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

                <form method="POST" action="{{ route('no-rujukan.store') }}" x-data="noRujukanForm()" x-init="updatePreview()">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- SIRI -->
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">SIRI <span class="text-uitm-purple-700">*</span></label>
                            <input type="text" inputmode="numeric" name="siri" id="siri" required
                                   value="{{ old('siri') }}"
                                   x-model="siri" x-soft-rule="digits" @input="updatePreview()"
                                   class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition"
                                   placeholder="100">
                            <p class="mt-1 text-xs text-stone-500">Nombor sahaja</p>
                            <x-input-error :messages="$errors->get('siri')" class="mt-1" />
                        </div>

                        <!-- KAMPUS -->
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">KAMPUS</label>
                            <input type="text" name="kampus" id="kampus" required
                                   value="{{ old('kampus', strtoupper(auth()->user()->kampus ?? 'UiTM')) }}"
                                   x-model="kampus" x-soft-rule="uppercase" @input="updatePreview()"
                                   class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition"
                                   placeholder="UiTM">
                            <x-input-error :messages="$errors->get('kampus')" class="mt-1" />
                        </div>

                        <!-- KOD BAHAGIAN -->
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">KOD BAHAGIAN <span class="text-uitm-purple-700">*</span></label>
                            <input type="text" name="kod_bahagian" required
                                   value="{{ old('kod_bahagian') }}"
                                   x-model="kodBahagian" x-soft-rule="uppercase" @input="updatePreview()"
                                   class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition"
                                   placeholder="INFO">
                            <x-input-error :messages="$errors->get('kod_bahagian')" class="mt-1" />
                        </div>

                        <!-- NOMBOR FAIL -->
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">NOMBOR FAIL <span class="text-uitm-purple-700">*</span></label>
                            <input type="text" name="nombor_fail" required
                                   value="{{ old('nombor_fail') }}"
                                   x-model="nomborFail" x-soft-rule="digits-slash" @input="updatePreview()"
                                   class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition"
                                   placeholder="1/1">
                            <p class="mt-1 text-xs text-stone-500">Nombor dan / sahaja (contoh: 1/1)</p>
                            <x-input-error :messages="$errors->get('nombor_fail')" class="mt-1" />
                        </div>
                    </div>

                    <!-- PERKARA -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-stone-700 mb-1">PERKARA <span class="text-uitm-purple-700">*</span></label>
                        <input type="text" name="perkara" required
                               value="{{ old('perkara') }}"
                               x-model="perkara" x-soft-rule="uppercase" @input="updatePreview()"
                               class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition"
                               placeholder="PENTADBIRAN - AM">
                        <x-input-error :messages="$errors->get('perkara')" class="mt-1" />
                    </div>

                    <!-- ADDITIONAL SPACE -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-stone-700 mb-2">RUANG TAMBAHAN</label>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-stone-700">
                                <input type="radio" name="additional_space" value="1"
                                       x-model="additionalSpace" @change="updatePreview()"
                                       class="border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500"
                                       {{ old('additional_space') == '1' ? 'checked' : '' }}>
                                Aktif
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-stone-700">
                                <input type="radio" name="additional_space" value="0"
                                       x-model="additionalSpace" @change="updatePreview()"
                                       class="border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500"
                                       {{ old('additional_space', '0') == '0' ? 'checked' : '' }}>
                                Tidak Aktif
                            </label>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="mt-6 p-4 bg-uitm-purple-50/40 rounded-lg border border-uitm-purple-100 border-l-4 border-l-uitm-gold-400">
                        <p class="text-xs uppercase tracking-wider font-medium text-uitm-purple-700 mb-1">Pratonton No. Rujukan</p>
                        <p class="font-mono text-stone-900 font-semibold text-base" x-text="preview"></p>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 border-t border-stone-200 pt-5">
                        <a href="{{ route('no-rujukan.index') }}"
                           class="px-4 py-2 bg-stone-100 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-200 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 focus:outline-none focus:ring-2 focus:ring-uitm-purple-500 focus:ring-offset-1 transition">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            Daftar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function noRujukanForm() {
            return {
                siri: '{{ old('siri', '') }}',
                kampus: '{{ old('kampus', strtoupper(auth()->user()->kampus ?? 'UiTM')) }}',
                kodBahagian: '{{ old('kod_bahagian', '') }}',
                nomborFail: '{{ old('nombor_fail', '') }}',
                perkara: '{{ old('perkara', '') }}',
                additionalSpace: '{{ old('additional_space', '0') }}',
                preview: '',
                updatePreview() {
                    const space = this.additionalSpace === '1' ? ' ' : '';
                    if (this.siri && this.kampus && this.kodBahagian && this.nomborFail) {
                        this.preview = this.siri + '-' + this.kampus + space + '(' + this.kodBahagian + '. ' + this.nomborFail + ')';
                    } else {
                        this.preview = '—';
                    }
                }
            };
        }
    </script>
</x-app-layout>
