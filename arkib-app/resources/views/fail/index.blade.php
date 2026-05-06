<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
            Fail
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

            @if($errors->has('csv_rows'))
                <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg">
                    <p class="font-semibold text-sm">Ralat semasa import CSV:</p>
                    <pre class="mt-2 text-xs whitespace-pre-wrap font-mono">{{ $errors->first('csv_rows') }}</pre>
                    @if(session('csv_success'))
                        <p class="mt-2 text-sm">{{ session('csv_success') }} rekod berjaya diimport.</p>
                    @endif
                </div>
            @endif

            <div class="mb-5">
                <h3 class="text-xl font-semibold text-uitm-purple-700 tracking-tight">Senarai Fail</h3>
                <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>
            </div>

            <!-- Action Buttons -->
            @php
                $canWrite = auth()->user()?->canWrite();
                $activeJenis = $jenis ?? null;
                $activeKat = $kategori ?? null;
                $activeSub = $sub ?? null;
            @endphp
            <div class="flex flex-wrap gap-2 mb-4">
                @if($canWrite)
                <a href="{{ route('fail.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 focus:outline-none focus:ring-2 focus:ring-uitm-purple-500 focus:ring-offset-1 transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a.75.75 0 01.75.75v3.5h3.5a.75.75 0 010 1.5h-3.5v3.5a.75.75 0 01-1.5 0v-3.5h-3.5a.75.75 0 010-1.5h3.5v-3.5A.75.75 0 0110 5z" clip-rule="evenodd"/></svg>
                    Daftar
                </a>

                <button type="button" onclick="document.getElementById('batchModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white text-uitm-purple-700 text-sm font-medium rounded-lg border border-uitm-purple-200 hover:bg-uitm-purple-50 transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M9.25 13.25a.75.75 0 001.5 0V4.636l2.955 3.129a.75.75 0 001.09-1.03l-4.25-4.5a.75.75 0 00-1.09 0l-4.25 4.5a.75.75 0 101.09 1.03L9.25 4.636v8.614z"/><path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z"/></svg>
                    Batch (xlsx)
                </button>
                @endif

                <button type="button" onclick="toggleSelectMode()" id="selectBtn"
                        class="inline-flex items-center gap-2 px-4 py-2 text-uitm-purple-700 text-sm font-medium rounded-lg hover:bg-uitm-purple-50 transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2.5 3A1.5 1.5 0 001 4.5v.75a.75.75 0 001.5 0V4.5h.75a.75.75 0 000-1.5h-.75zm10 0a.75.75 0 000 1.5h.75v.75a.75.75 0 001.5 0V4.5A1.5 1.5 0 0013.25 3h-.75zM2.5 15.5a.75.75 0 01-.75-.75v-.75a.75.75 0 011.5 0v.75c0 .414-.336.75-.75.75zm10 0a.75.75 0 010-1.5h.75v-.75a.75.75 0 011.5 0v.75a1.5 1.5 0 01-1.5 1.5h-.75z" clip-rule="evenodd"/></svg>
                    Pilih
                </button>

                @if($canWrite)
                <button type="button" onclick="deleteSelected()" id="deleteBtn"
                        class="hidden inline-flex items-center gap-2 px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-rose-700 transition">
                    Padam Dipilih
                </button>
                @endif

                <button type="button" onclick="printSelected()" id="printBtn"
                        class="hidden inline-flex items-center gap-2 px-4 py-2 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 transition">
                    Cetak Dipilih
                </button>

                <button type="button" onclick="cancelSelect()" id="cancelBtn"
                        class="hidden inline-flex items-center px-4 py-2 bg-stone-100 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-200 transition">
                    Batal
                </button>
            </div>

            <!-- JENIS FAIL Filter -->
            <div class="mb-4 flex flex-wrap gap-2 items-center">
                <span class="text-xs uppercase tracking-wider font-medium text-stone-500">Jenis Fail:</span>

                @if($fakulti && $fakulti->fail_am)
                    <a href="{{ route('fail.index', ['jenis' => 'AM']) }}"
                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md transition {{ $activeJenis === 'AM' ? 'bg-uitm-purple-700 text-white' : 'bg-white text-uitm-purple-700 ring-1 ring-uitm-purple-200 hover:bg-uitm-purple-50' }}">
                        AM
                    </a>
                @endif
                @if($fakulti && $fakulti->fail_sulit)
                    <a href="{{ route('fail.index', ['jenis' => 'SULIT']) }}"
                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md transition {{ $activeJenis === 'SULIT' ? 'bg-uitm-purple-700 text-white' : 'bg-white text-uitm-purple-700 ring-1 ring-uitm-purple-200 hover:bg-uitm-purple-50' }}">
                        SULIT
                    </a>
                @endif

                @if($activeJenis === 'SULIT')
                    <span class="text-stone-300">›</span>
                    @if($fakulti && $fakulti->fail_staff)
                        <a href="{{ route('fail.index', ['jenis' => 'SULIT', 'kategori' => 'STAFF']) }}"
                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md transition {{ $activeKat === 'STAFF' ? 'bg-uitm-purple-700 text-white' : 'bg-white text-uitm-purple-700 ring-1 ring-uitm-purple-200 hover:bg-uitm-purple-50' }}">
                            STAFF
                        </a>
                    @endif
                    @if($fakulti && $fakulti->fail_pelajar)
                        <a href="{{ route('fail.index', ['jenis' => 'SULIT', 'kategori' => 'PELAJAR']) }}"
                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md transition {{ $activeKat === 'PELAJAR' ? 'bg-uitm-purple-700 text-white' : 'bg-white text-uitm-purple-700 ring-1 ring-uitm-purple-200 hover:bg-uitm-purple-50' }}">
                            PELAJAR
                        </a>
                    @endif
                @endif

                @if($activeJenis === 'SULIT' && $activeKat === 'STAFF')
                    <span class="text-stone-300">›</span>
                    @if($fakulti && $fakulti->fail_akademik)
                        <a href="{{ route('fail.index', ['jenis' => 'SULIT', 'kategori' => 'STAFF', 'sub' => 'AKADEMIK']) }}"
                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md transition {{ $activeSub === 'AKADEMIK' ? 'bg-uitm-purple-700 text-white' : 'bg-white text-uitm-purple-700 ring-1 ring-uitm-purple-200 hover:bg-uitm-purple-50' }}">
                            AKADEMIK
                        </a>
                    @endif
                    @if($fakulti && $fakulti->fail_pentadbiran)
                        <a href="{{ route('fail.index', ['jenis' => 'SULIT', 'kategori' => 'STAFF', 'sub' => 'PENTADBIRAN']) }}"
                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md transition {{ $activeSub === 'PENTADBIRAN' ? 'bg-uitm-purple-700 text-white' : 'bg-white text-uitm-purple-700 ring-1 ring-uitm-purple-200 hover:bg-uitm-purple-50' }}">
                            PENTADBIRAN
                        </a>
                    @endif
                @endif

                @if($activeJenis)
                    @php
                        $kembali = ['jenis' => $activeJenis];
                        if ($activeSub) {
                            $kembali = ['jenis' => $activeJenis, 'kategori' => $activeKat];
                        } elseif ($activeKat) {
                            $kembali = ['jenis' => $activeJenis];
                        } else {
                            $kembali = [];
                        }
                    @endphp
                    <a href="{{ route('fail.index', $kembali) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium bg-stone-100 text-stone-700 rounded-md hover:bg-stone-200 transition">
                        Kembali
                    </a>
                @endif
            </div>

            <!-- Search Box -->
            <div class="mb-4 flex gap-2 flex-wrap items-center bg-white rounded-xl ring-1 ring-stone-200 shadow-sm p-3">
                <select id="failSearchCol"
                        class="px-3 py-2 border-stone-300 rounded-lg shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500">
                    <option value="-1">Semua Lajur</option>
                    <option value="2">No. Rujukan</option>
                    <option value="3">Perkara</option>
                    <option value="4">Jilid</option>
                    <option value="5">Tarikh Kandungan Pertama</option>
                    <option value="6">Tarikh Kandungan Akhir</option>
                    <option value="7">Tarikh Tutup</option>
                    <option value="8">Kotak</option>
                    <option value="9">Person in Charge</option>
                    <option value="10">Kertas-Kertas Yang Berhubung</option>
                    <option value="student">Student ID</option>
                </select>
                <div class="relative flex-1 min-w-[200px] sm:max-w-md">
                    <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/></svg>
                    <input type="text" id="failSearch" placeholder="Cari…"
                           class="w-full pl-9 pr-3 py-2 border-stone-300 rounded-lg shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500">
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-stone-600">
                    <input type="checkbox" id="failSearchExact" class="rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                    Padanan tepat
                </label>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl overflow-x-auto">
                <form id="deleteForm" method="POST" action="{{ route('fail.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <table id="fail-table" class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-uitm-purple-50 sticky top-0">
                            <tr>
                                <th class="px-3 py-3 text-left w-8">
                                    <input type="checkbox" id="selectAll" onchange="toggleAll(this)"
                                           class="hidden rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                </th>
                                <th class="px-3 py-3 text-right text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider w-14">BIL.</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">No. Rujukan</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Perkara</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Jilid</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Kandungan Pertama</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Kandungan Akhir</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Tarikh Tutup</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Kotak</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Person in Charge</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Kertas-Kertas Yang Berhubung</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-stone-200">
                            @forelse($fails as $i => $fail)
                            <tr class="hover:bg-uitm-purple-50/30 even:bg-stone-50/40 {{ $canWrite ? 'cursor-pointer' : '' }} transition-colors"
                                data-student-id="{{ $fail->studentIds->pluck('student_id')->implode(' ') }}"
                                @if($canWrite) onclick="rowClick(event, '{{ route('fail.edit', $fail) }}')" @endif>
                                <td class="px-3 py-3 align-top">
                                    <input type="checkbox" name="ids[]" value="{{ $fail->id }}"
                                           class="row-checkbox hidden rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500" onclick="event.stopPropagation()">
                                </td>
                                <td class="px-3 py-3 align-top text-sm text-stone-500 text-right tabular-nums no-search">{{ $i + 1 }}</td>
                                <td class="px-3 py-3 align-top text-sm font-mono text-uitm-purple-700 font-medium">{{ $fail->noRujukan->no_rujukan_full }}</td>
                                <td class="px-3 py-3 align-top text-sm text-stone-700">{{ $fail->noRujukan->perkara }}</td>
                                <td class="px-3 py-3 align-top text-sm text-stone-700 tabular-nums">{{ $fail->jilid }}</td>
                                <td class="px-3 py-3 align-top text-sm text-stone-700 tabular-nums">{{ $fail->tarikh_pertama?->format('d/m/Y') }}</td>
                                <td class="px-3 py-3 align-top text-sm text-stone-700 tabular-nums">{{ $fail->tarikh_akhir?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-3 py-3 align-top text-sm text-stone-700 tabular-nums">{{ $fail->tarikh_tutup?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-3 py-3 align-top text-sm text-stone-700">{{ $fail->kotak ?? '—' }}</td>
                                <td class="px-3 py-3 align-top text-sm text-stone-700">{{ $fail->person_in_charge }}</td>
                                <td class="px-3 py-3 align-top text-sm text-stone-700">{{ $fail->kertas_berhubung_label ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3 text-stone-400">
                                        <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776"/></svg>
                                        <p class="text-sm font-medium">Tiada rekod fail</p>
                                        @if($canWrite)<a href="{{ route('fail.create') }}" class="text-xs text-uitm-purple-700 hover:underline">Daftar fail pertama</a>@endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>

            <form id="printForm" method="POST" action="{{ route('fail.print') }}" class="hidden">
                @csrf
            </form>
        </div>
    </div>

    <!-- Batch Modal -->
    <div id="batchModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl shadow-2xl ring-1 ring-stone-200 p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-1 text-uitm-purple-700">Import Fail (Excel / CSV)</h3>
            <div class="h-0.5 w-10 bg-uitm-gold-400 rounded-full mb-4"></div>
            <p class="text-sm text-stone-600 mb-4">Muat turun templat Excel (dengan dropdown noRujukan), isi data, kemudian muat naik.</p>

            <a href="{{ route('fail.csv-template') }}"
               class="inline-flex items-center gap-2 mb-4 px-3 py-2 bg-stone-100 text-stone-700 text-sm rounded-lg hover:bg-stone-200 transition">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 2.75a.75.75 0 00-1.5 0v8.614L6.295 8.235a.75.75 0 10-1.09 1.03l4.25 4.5a.75.75 0 001.09 0l4.25-4.5a.75.75 0 00-1.09-1.03l-2.955 3.129V2.75z"/><path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z"/></svg>
                Muat Turun Templat Excel (.xlsx)
            </a>

            <form method="POST" action="{{ route('fail.csv-import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-stone-700 mb-1">Fail (.xlsx atau .csv)</label>
                    <input type="file" name="csv_file" accept=".csv,.txt,.xlsx,.xls" required
                           class="block w-full text-sm text-stone-600 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-uitm-purple-50 file:text-uitm-purple-700 hover:file:bg-uitm-purple-100 cursor-pointer">
                    @error('csv_file')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('batchModal').classList.add('hidden')"
                            class="px-4 py-2 bg-stone-100 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-200 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 transition">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let selectMode = false;

        function rowClick(event, url) {
            if (selectMode) return;
            if (event.target.type === 'checkbox') return;
            window.location.href = url;
        }

        function toggleSelectMode() {
            selectMode = true;
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.classList.remove('hidden'));
            document.getElementById('selectAll').classList.remove('hidden');
            document.getElementById('selectBtn').classList.add('hidden');
            document.getElementById('deleteBtn').classList.remove('hidden');
            document.getElementById('printBtn').classList.remove('hidden');
            document.getElementById('cancelBtn').classList.remove('hidden');
        }

        function cancelSelect() {
            selectMode = false;
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.classList.add('hidden');
                cb.checked = false;
            });
            document.getElementById('selectAll').classList.add('hidden');
            document.getElementById('selectBtn').classList.remove('hidden');
            document.getElementById('deleteBtn').classList.add('hidden');
            document.getElementById('printBtn').classList.add('hidden');
            document.getElementById('cancelBtn').classList.add('hidden');
        }

        function toggleAll(cb) {
            document.querySelectorAll('.row-checkbox').forEach(box => box.checked = cb.checked);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('failSearch');
            const colSelect = document.getElementById('failSearchCol');
            const exactChk = document.getElementById('failSearchExact');
            const table = document.getElementById('fail-table');
            if (!searchInput || !table) return;

            function runFilter() {
                const q = searchInput.value.trim().toLowerCase();
                const colVal = colSelect.value;
                const colIdx = parseInt(colVal, 10);
                const exact = exactChk.checked;
                table.querySelectorAll('tbody tr').forEach(row => {
                    const cells = row.querySelectorAll('td');
                    const studentId = (row.dataset.studentId || '').toLowerCase();
                    let text = '';
                    if (colVal === 'student') {
                        text = studentId;
                    } else if (colIdx >= 0 && cells[colIdx]) {
                        text = (cells[colIdx].textContent || '').trim().toLowerCase();
                    } else {
                        row.querySelectorAll('td:not(.no-search)').forEach(c => {
                            text += ' ' + (c.textContent || '');
                        });
                        text += ' ' + studentId;
                        text = text.toLowerCase();
                    }
                    const match = q === '' || (exact ? text === q : text.includes(q));
                    row.style.display = match ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', runFilter);
            searchInput.addEventListener('keyup', runFilter);
            colSelect.addEventListener('change', runFilter);
            exactChk.addEventListener('change', runFilter);
        });

        function deleteSelected() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            if (checked.length === 0) {
                alert('Sila pilih sekurang-kurangnya satu rekod.');
                return;
            }
            if (confirm('Padam ' + checked.length + ' rekod yang dipilih?')) {
                document.getElementById('deleteForm').submit();
            }
        }

        function printSelected() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            if (checked.length === 0) {
                alert('Sila pilih sekurang-kurangnya satu rekod.');
                return;
            }
            const printForm = document.getElementById('printForm');
            printForm.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                printForm.appendChild(input);
            });
            printForm.submit();
        }
    </script>
</x-app-layout>
