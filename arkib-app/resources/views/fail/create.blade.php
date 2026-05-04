<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('fail.index') }}" class="inline-flex items-center justify-center h-8 w-8 rounded-full text-stone-500 hover:bg-stone-100 hover:text-uitm-purple-700 transition">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/></svg>
            </a>
            <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
                Daftar Fail
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

                <form method="POST" action="{{ route('fail.store') }}"
                      x-data="{
                          jenis: '{{ old('jenis_fail', '') }}',
                          kategori: '{{ old('kategori', '') }}',
                          sub: '{{ old('sub_kategori', '') }}',
                          permAm: {{ $fakulti && $fakulti->fail_am ? 'true' : 'false' }},
                          permSulit: {{ $fakulti && $fakulti->fail_sulit ? 'true' : 'false' }},
                          permPelajar: {{ $fakulti && $fakulti->fail_pelajar ? 'true' : 'false' }},
                          permStaff: {{ $fakulti && $fakulti->fail_staff ? 'true' : 'false' }},
                          permAkademik: {{ $fakulti && $fakulti->fail_akademik ? 'true' : 'false' }},
                          permPentadbiran: {{ $fakulti && $fakulti->fail_pentadbiran ? 'true' : 'false' }},
                          permStudentId: {{ $fakulti && $fakulti->student_id ? 'true' : 'false' }},
                          showStudentId() {
                              return this.jenis === 'SULIT' && this.kategori === 'PELAJAR' && this.permStudentId;
                          }
                      }" class="space-y-4">
                    @csrf

                    <!-- NO. RUJUKAN -->
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">NO. RUJUKAN <span class="text-uitm-purple-700">*</span></label>
                        <select name="no_rujukan_id" required
                                class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                            <option value="">— Pilih No. Rujukan —</option>
                            @foreach($noRujukans as $nr)
                            <option value="{{ $nr->id }}" {{ old('no_rujukan_id') == $nr->id ? 'selected' : '' }}>
                                {{ $nr->no_rujukan_full }} — {{ $nr->perkara }}
                            </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('no_rujukan_id')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- JILID -->
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">JILID <span class="text-uitm-purple-700">*</span></label>
                            <input type="text" inputmode="numeric" name="jilid" x-soft-rule="digits" required
                                   value="{{ old('jilid') }}"
                                   class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition"
                                   placeholder="1">
                            <p class="mt-1 text-xs text-stone-500">Nombor sahaja</p>
                            <x-input-error :messages="$errors->get('jilid')" class="mt-1" />
                        </div>

                        <!-- TARIKH KANDUNGAN PERTAMA -->
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">TARIKH KANDUNGAN PERTAMA <span class="text-uitm-purple-700">*</span></label>
                            <input type="date" name="tarikh_pertama" required
                                   value="{{ old('tarikh_pertama') }}"
                                   class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                            <x-input-error :messages="$errors->get('tarikh_pertama')" class="mt-1" />
                        </div>
                    </div>

                    <!-- JENIS FAIL -->
                    <div class="border-t border-stone-200 pt-4">
                        <label class="block text-sm font-medium text-stone-700 mb-2">JENIS FAIL <span class="text-uitm-purple-700">*</span></label>
                        <div class="flex flex-wrap gap-3 text-sm">
                            <template x-if="permAm">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="jenis_fail" value="AM" x-model="jenis"
                                           @change="kategori=''; sub=''"
                                           class="border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                    AM
                                </label>
                            </template>
                            <template x-if="permSulit">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="jenis_fail" value="SULIT" x-model="jenis"
                                           class="border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                    SULIT
                                </label>
                            </template>
                        </div>

                        <!-- KATEGORI (when SULIT) -->
                        <div x-show="jenis === 'SULIT'" x-cloak class="mt-3 ml-4 pl-3 border-l-2 border-uitm-gold-300">
                            <label class="block text-sm font-medium text-stone-700 mb-2">KATEGORI <span class="text-uitm-purple-700">*</span></label>
                            <div class="flex flex-wrap gap-3 text-sm">
                                <template x-if="permStaff">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="kategori" value="STAFF" x-model="kategori"
                                               @change="sub=''"
                                               class="border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                        STAFF
                                    </label>
                                </template>
                                <template x-if="permPelajar">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="kategori" value="PELAJAR" x-model="kategori"
                                               @change="sub=''"
                                               class="border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                        PELAJAR
                                    </label>
                                </template>
                            </div>

                            <!-- SUB-KATEGORI (when STAFF) -->
                            <div x-show="kategori === 'STAFF'" x-cloak class="mt-3 ml-4 pl-3 border-l-2 border-uitm-gold-300">
                                <label class="block text-sm font-medium text-stone-700 mb-2">SUB-KATEGORI <span class="text-uitm-purple-700">*</span></label>
                                <div class="flex flex-wrap gap-3 text-sm">
                                    <template x-if="permAkademik">
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="sub_kategori" value="AKADEMIK" x-model="sub"
                                                   class="border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            AKADEMIK
                                        </label>
                                    </template>
                                    <template x-if="permPentadbiran">
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="sub_kategori" value="PENTADBIRAN" x-model="sub"
                                                   class="border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            PENTADBIRAN
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <!-- STUDENT IDs (when PELAJAR + permission) -->
                            <div x-show="showStudentId()" x-cloak class="mt-3 ml-4 pl-3 border-l-2 border-uitm-gold-300"
                                 x-data="{ ids: {{ collect(old('student_ids', [''])) ->filter(fn($v)=>$v!==null)->values()->toJson() }} }"
                                 x-init="if (!ids.length) ids = ['']">
                                <label class="block text-sm font-medium text-stone-700 mb-1">STUDENT ID <span class="text-uitm-purple-700">*</span></label>
                                <template x-for="(_, idx) in ids" :key="idx">
                                    <div class="mt-2 flex gap-2 items-center">
                                        <input type="text" inputmode="numeric" :name="'student_ids[]'" pattern="\d+"
                                               x-model="ids[idx]"
                                               class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition"
                                               placeholder="0123456789">
                                        <button type="button" @click="ids.splice(idx,1); if(!ids.length) ids=['']"
                                                class="px-2 py-1 text-xs text-rose-700 bg-rose-50 ring-1 ring-rose-200 rounded-md hover:bg-rose-100" x-show="ids.length > 1">Buang</button>
                                    </div>
                                </template>
                                <button type="button" @click="ids.push('')"
                                        class="mt-2 inline-flex items-center gap-1 px-2.5 py-1 text-xs text-uitm-purple-700 bg-white ring-1 ring-uitm-purple-200 rounded-md hover:bg-uitm-purple-50">
                                    + Tambah Student ID
                                </button>
                                <x-input-error :messages="$errors->get('student_ids')" class="mt-1" />
                                <x-input-error :messages="$errors->get('student_ids.*')" class="mt-1" />
                                <p class="mt-1 text-xs text-stone-500">Nombor sahaja. Boleh tambah lebih dari satu.</p>
                            </div>
                        </div>
                    </div>

                    <!-- PERSON IN CHARGE -->
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">PERSON IN CHARGE</label>
                        <input type="text" readonly value="{{ Auth::user()->name }}"
                               class="block w-full rounded-lg bg-stone-50 border-stone-200 shadow-sm text-sm text-stone-600 cursor-not-allowed">
                        <p class="mt-1 text-xs text-stone-500">Diambil secara automatik dari pengguna log masuk.</p>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-stone-200 pt-5">
                        <a href="{{ route('fail.index') }}"
                           class="px-4 py-2 bg-stone-100 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-200 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 transition">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            Daftar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
