<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
            Pengurusan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg flex items-start gap-3">
                    <svg class="h-5 w-5 mt-0.5 flex-shrink-0 text-emerald-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg">
                    @foreach($errors->all() as $err)
                        <p class="text-sm">{{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <div class="mb-5">
                <h3 class="text-xl font-semibold text-uitm-purple-700 tracking-tight">Senarai Pengguna</h3>
                <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>
            </div>

            <div class="mb-4 flex flex-wrap gap-2">
                <a href="{{ route('users.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 focus:outline-none focus:ring-2 focus:ring-uitm-purple-500 focus:ring-offset-1 transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a.75.75 0 01.75.75v3.5h3.5a.75.75 0 010 1.5h-3.5v3.5a.75.75 0 01-1.5 0v-3.5h-3.5a.75.75 0 010-1.5h3.5v-3.5A.75.75 0 0110 5z" clip-rule="evenodd"/></svg>
                    Tambah Pengguna
                </a>

                <button type="button"
                        onclick="document.getElementById('tambahFakultiModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white text-uitm-purple-700 text-sm font-medium rounded-lg border border-uitm-purple-200 hover:bg-uitm-purple-50 transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a.75.75 0 01.75.75v3.5h3.5a.75.75 0 010 1.5h-3.5v3.5a.75.75 0 01-1.5 0v-3.5h-3.5a.75.75 0 010-1.5h3.5v-3.5A.75.75 0 0110 5z" clip-rule="evenodd"/></svg>
                    Tambah Fakulti/Bahagian
                </button>
            </div>

            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-uitm-purple-50">
                        <tr>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider w-14">BIL.</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Nama Penuh</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Emel</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Kampus</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Cawangan</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Fakulti/Bahagian</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Jawatan</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-stone-200">
                        @forelse($users as $i => $user)
                        <tr class="hover:bg-stone-50 even:bg-stone-50/40 transition-colors">
                            <td class="px-3 py-3 text-sm text-stone-500 text-right tabular-nums">{{ $i + 1 }}</td>
                            <td class="px-3 py-3 text-sm font-medium text-stone-900">{{ $user->name }}</td>
                            <td class="px-3 py-3 text-sm text-stone-700">{{ $user->email }}</td>
                            <td class="px-3 py-3 text-sm text-stone-700">{{ $user->kampus }}</td>
                            <td class="px-3 py-3 text-sm text-stone-700">{{ $user->cawangan ?? '—' }}</td>
                            <td class="px-3 py-3 text-sm text-stone-700">
                                <form method="POST" action="{{ route('users.update-fakulti', $user) }}" class="flex items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <select name="fakulti_bahagian_id" onchange="this.form.requestSubmit()"
                                            class="rounded-md border-stone-300 shadow-sm text-xs py-1 focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                                        <option value="">— Tiada —</option>
                                        @foreach($fakultis as $f)
                                            <option value="{{ $f->id }}" {{ $user->fakulti_bahagian_id == $f->id ? 'selected' : '' }}>
                                                {{ $f->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-3 py-3 text-sm">
                                @if($user->position)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-uitm-gold-100 text-uitm-purple-800 text-xs font-semibold tracking-wide">{{ $user->position }}</span>
                                @else
                                    <span class="text-stone-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-sm text-right">
                                <div class="flex flex-wrap gap-1 justify-end">
                                    <a href="{{ route('users.edit-position', $user) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-white text-uitm-purple-700 text-xs font-medium rounded-md ring-1 ring-uitm-purple-200 hover:bg-uitm-purple-50 transition">
                                        Jawatan
                                    </a>

                                    <form method="POST" action="{{ route('users.reset-password', $user) }}"
                                          onsubmit="return confirm('Reset kata laluan pengguna ini kepada \'password\'?')">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-white text-amber-700 text-xs font-medium rounded-md ring-1 ring-amber-200 hover:bg-amber-50 transition">
                                            Reset KL
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('users.destroy', $user) }}"
                                          onsubmit="return confirm('Padam pengguna {{ $user->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-white text-rose-700 text-xs font-medium rounded-md ring-1 ring-rose-200 hover:bg-rose-50 transition">
                                            Padam
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 text-stone-400">
                                    <p class="text-sm font-medium">Tiada pengguna berdaftar</p>
                                    <a href="{{ route('users.create') }}" class="text-xs text-uitm-purple-700 hover:underline">Tambah pengguna pertama</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Senarai Fakulti/Bahagian -->
            <div class="mt-10 mb-5">
                <h3 class="text-xl font-semibold text-uitm-purple-700 tracking-tight">Senarai Fakulti/Bahagian</h3>
                <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>
            </div>

            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl overflow-x-auto" x-data="{ openId: null }">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-uitm-purple-50">
                        <tr>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider w-14">BIL.</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Fakulti/Bahagian</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-stone-200">
                        @forelse($fakultis as $i => $fakulti)
                        <tr class="hover:bg-stone-50 even:bg-stone-50/40 transition-colors">
                            <td class="px-3 py-3 text-sm text-stone-500 text-right tabular-nums">{{ $i + 1 }}</td>
                            <td class="px-3 py-3 text-sm font-medium text-stone-900">{{ $fakulti->nama }}</td>
                            <td class="px-3 py-3 text-sm text-right">
                                <div class="flex flex-wrap gap-1 justify-end">
                                    <button type="button"
                                            @click="openId = (openId === {{ $fakulti->id }} ? null : {{ $fakulti->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-white text-uitm-purple-700 text-xs font-medium rounded-md ring-1 ring-uitm-purple-200 hover:bg-uitm-purple-50 transition">
                                        Permission Control
                                    </button>
                                    <form method="POST" action="{{ route('pengurusan.fakulti.destroy', $fakulti) }}"
                                          onsubmit="return confirm('Padam fakulti/bahagian {{ $fakulti->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-white text-rose-700 text-xs font-medium rounded-md ring-1 ring-rose-200 hover:bg-rose-50 transition">
                                            Padam
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr x-show="openId === {{ $fakulti->id }}" x-cloak>
                            <td colspan="3" class="px-4 py-4 bg-uitm-purple-50/40">
                                <form method="POST" action="{{ route('pengurusan.fakulti.permissions', $fakulti) }}"
                                      x-data="{
                                          space1: {{ $fakulti->additional_space_1 ? 'true' : 'false' }},
                                          space2: {{ $fakulti->additional_space_2 ? 'true' : 'false' }},
                                          cawangan: {{ $fakulti->additional_cawangan ? 'true' : 'false' }},
                                          fail_am: {{ $fakulti->fail_am ? 'true' : 'false' }},
                                          fail_sulit: {{ $fakulti->fail_sulit ? 'true' : 'false' }},
                                          fail_pelajar: {{ $fakulti->fail_pelajar ? 'true' : 'false' }},
                                          fail_staff: {{ $fakulti->fail_staff ? 'true' : 'false' }},
                                          fail_akademik: {{ $fakulti->fail_akademik ? 'true' : 'false' }},
                                          fail_pentadbiran: {{ $fakulti->fail_pentadbiran ? 'true' : 'false' }},
                                          student_id: {{ $fakulti->student_id ? 'true' : 'false' }},
                                          borang_pemisahan: {{ $fakulti->borang_pemisahan ? 'true' : 'false' }},
                                          label_pentadbiran: {{ $fakulti->label_pentadbiran ? 'true' : 'false' }},
                                          label_staff: {{ $fakulti->label_staff ? 'true' : 'false' }},
                                          label_pelajar: {{ $fakulti->label_pelajar ? 'true' : 'false' }},
                                          syncCascade() {
                                              if (!this.fail_sulit) { this.fail_pelajar = false; this.fail_staff = false; }
                                              if (!this.fail_staff) { this.fail_akademik = false; this.fail_pentadbiran = false; }
                                          }
                                      }">
                                    @csrf
                                    @method('PATCH')

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="additional_space_1" value="1" x-model="space1"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            ADDITIONAL SPACE 1
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="additional_space_2" value="1" x-model="space2"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            ADDITIONAL SPACE 2
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="additional_cawangan" value="1" x-model="cawangan"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            ADDITIONAL CAWANGAN
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="fail_am" value="1" x-model="fail_am"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            FAIL AM
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="fail_sulit" value="1" x-model="fail_sulit" @change="syncCascade()"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            FAIL SULIT
                                        </label>
                                        <label class="inline-flex items-center gap-2" :class="!fail_sulit ? 'opacity-50' : ''">
                                            <input type="checkbox" name="fail_pelajar" value="1" x-model="fail_pelajar" :disabled="!fail_sulit"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            FAIL PELAJAR
                                        </label>
                                        <label class="inline-flex items-center gap-2" :class="!fail_sulit ? 'opacity-50' : ''">
                                            <input type="checkbox" name="fail_staff" value="1" x-model="fail_staff" :disabled="!fail_sulit" @change="syncCascade()"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            FAIL STAFF
                                        </label>
                                        <label class="inline-flex items-center gap-2" :class="(!fail_sulit || !fail_staff) ? 'opacity-50' : ''">
                                            <input type="checkbox" name="fail_akademik" value="1" x-model="fail_akademik" :disabled="!fail_sulit || !fail_staff"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            FAIL AKADEMIK
                                        </label>
                                        <label class="inline-flex items-center gap-2" :class="(!fail_sulit || !fail_staff) ? 'opacity-50' : ''">
                                            <input type="checkbox" name="fail_pentadbiran" value="1" x-model="fail_pentadbiran" :disabled="!fail_sulit || !fail_staff"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            FAIL PENTADBIRAN
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="student_id" value="1" x-model="student_id"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            STUDENT ID
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="borang_pemisahan" value="1" x-model="borang_pemisahan"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            BORANG PEMISAHAN
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="label_pentadbiran" value="1" x-model="label_pentadbiran"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            LABEL PENTADBIRAN
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="label_staff" value="1" x-model="label_staff"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            LABEL STAFF
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="label_pelajar" value="1" x-model="label_pelajar"
                                                   class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                            LABEL PELAJAR
                                        </label>
                                    </div>

                                    <div class="mt-4 flex justify-end gap-2">
                                        <button type="button" @click="openId = null"
                                                class="px-3 py-1.5 bg-stone-100 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-200 transition">
                                            Batal
                                        </button>
                                        <button type="submit"
                                                class="px-3 py-1.5 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 transition">
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-12 text-center">
                                <p class="text-sm text-stone-400">Tiada fakulti/bahagian. Tambah yang pertama melalui butang di atas.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tambah Fakulti Modal -->
    <div id="tambahFakultiModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl shadow-2xl ring-1 ring-stone-200 p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-1 text-uitm-purple-700">Tambah Fakulti/Bahagian</h3>
            <div class="h-0.5 w-10 bg-uitm-gold-400 rounded-full mb-4"></div>

            <form method="POST" action="{{ route('pengurusan.fakulti.store') }}" x-data>
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-stone-700 mb-1">FAKULTI/BAHAGIAN <span class="text-uitm-purple-700">*</span></label>
                    <input type="text" name="nama" required
                           x-soft-rule="uppercase"
                           class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('tambahFakultiModal').classList.add('hidden')"
                            class="px-4 py-2 bg-stone-100 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-200 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 transition">
                        Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
