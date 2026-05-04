<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
            Pemisahan Rekod
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

            @php $canWrite = auth()->user()?->canWrite(); @endphp
            <div class="mb-5">
                <h3 class="text-xl font-semibold text-uitm-purple-700 tracking-tight">Rekod Mengikut Kotak</h3>
                <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>
            </div>

            <!-- Print Buttons (shown when a kotak is selected) -->
            <div id="printBar" class="hidden mb-4 flex flex-wrap gap-2 p-3 bg-white ring-1 ring-stone-200 rounded-xl shadow-sm items-center">
                <span class="text-sm text-stone-600 px-2">No. Kotak: <strong class="text-uitm-purple-700" id="selectedKotak"></strong></span>
                <span class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-uitm-purple-50 text-uitm-purple-700">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 2.75C5 1.784 5.784 1 6.75 1h6.5c.966 0 1.75.784 1.75 1.75v3.552c.377.046.752.097 1.126.153A2.212 2.212 0 0118 8.653v4.097A2.25 2.25 0 0115.75 15h-.241l.305 1.984A1.75 1.75 0 0114.084 19H5.916a1.75 1.75 0 01-1.73-2.016L4.492 15H4.25A2.25 2.25 0 012 12.75V8.653c0-1.082.775-2.034 1.874-2.198.374-.056.75-.107 1.127-.153V2.75zm8.5 3.397a41.533 41.533 0 00-7 0V2.75a.25.25 0 01.25-.25h6.5a.25.25 0 01.25.25v3.397z" clip-rule="evenodd"/></svg>
                </span>
                @if(!isset($fakulti) || $fakulti?->borang_pemisahan)
                <button type="button" onclick="printDoc('pemisahan')"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-uitm-purple-700 text-white text-sm rounded-lg shadow-sm hover:bg-uitm-purple-800 transition">
                    Borang Pemisahan
                </button>
                @endif
                @if(!isset($fakulti) || $fakulti?->label_pentadbiran)
                <button type="button" onclick="printDoc('pentadbiran')"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-white text-uitm-purple-700 border border-uitm-purple-200 text-sm rounded-lg hover:bg-uitm-purple-50 transition">
                    Label Pentadbiran
                </button>
                @endif
                @if(!isset($fakulti) || $fakulti?->label_staff)
                <button type="button" onclick="printDoc('staf')"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-white text-uitm-purple-700 border border-uitm-purple-200 text-sm rounded-lg hover:bg-uitm-purple-50 transition">
                    Label Staff
                </button>
                @endif
                @if(!isset($fakulti) || $fakulti?->label_pelajar)
                <button type="button" onclick="printDoc('pelajar')"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-white text-uitm-purple-700 border border-uitm-purple-200 text-sm rounded-lg hover:bg-uitm-purple-50 transition">
                    Label Pelajar
                </button>
                @endif
                <button type="button" onclick="clearKotak()"
                        class="inline-flex items-center px-3 py-2 bg-stone-100 text-stone-700 text-sm rounded-lg hover:bg-stone-200 transition ml-auto">
                    Batal Pilihan
                </button>
            </div>

            <!-- Grouped by Kotak -->
            @forelse($pemisahans as $kotak => $records)
            <div class="mb-6 bg-white shadow-sm ring-1 ring-stone-200 rounded-xl overflow-hidden border-l-4 border-l-uitm-gold-400">
                <div class="px-4 py-3 flex justify-between items-center bg-uitm-purple-100">
                    <h3 class="text-uitm-purple-800 font-semibold text-sm tracking-wide">KOTAK: {{ $kotak }}</h3>
                    <button type="button" onclick="selectKotak('{{ $kotak }}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-white text-uitm-purple-700 text-xs font-medium rounded-md shadow-sm hover:bg-uitm-gold-50 ring-1 ring-uitm-purple-200 transition">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        Pilih Kotak Ini
                    </button>
                </div>
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-uitm-purple-50">
                        <tr>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider w-14">BIL.</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">No. Fail</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Tajuk Fail</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Tarikh Pemisahan</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Tarikh Buka</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Tarikh Tutup</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Tujuan Pemisahan</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">No. Kotak</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Person in Charge</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-stone-200">
                        @foreach($records as $i => $pemisahan)
                        <tr class="hover:bg-uitm-purple-50/30 even:bg-stone-50/40 {{ $canWrite ? 'cursor-pointer' : '' }} transition-colors"
                            @if($canWrite) onclick="window.location.href='{{ route('pemisahan.edit', $pemisahan) }}'" @endif>
                            <td class="px-3 py-2 text-sm text-stone-500 text-right tabular-nums">{{ $i + 1 }}</td>
                            <td class="px-3 py-2 text-sm font-mono text-uitm-purple-700 font-medium">{{ $pemisahan->fail->noRujukan->no_rujukan_full }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700">{{ $pemisahan->fail->noRujukan->perkara }} — Jilid {{ $pemisahan->fail->jilid }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700 tabular-nums">{{ $pemisahan->tarikh_pemisahan?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700 tabular-nums">{{ $pemisahan->fail->tarikh_pertama?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700 tabular-nums">{{ $pemisahan->fail->tarikh_akhir?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700">{{ $pemisahan->tujuan_pemisahan ?? '—' }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700">{{ $pemisahan->fail->kotak ?? '—' }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700">{{ $pemisahan->person_in_charge ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @empty
            @endforelse

            <!-- Records without kotak -->
            @if($allPemisahans->count() > 0)
            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl overflow-hidden border-l-4 border-l-stone-300">
                <div class="px-4 py-3 bg-stone-100">
                    <h3 class="text-stone-700 font-semibold text-sm tracking-wide">TIADA NO. KOTAK</h3>
                </div>
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-stone-50">
                        <tr>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-stone-600 uppercase tracking-wider w-14">BIL.</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-stone-600 uppercase tracking-wider">No. Fail</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-stone-600 uppercase tracking-wider">Tajuk Fail</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-stone-600 uppercase tracking-wider">Tarikh Pemisahan</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-stone-600 uppercase tracking-wider">Tarikh Buka</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-stone-600 uppercase tracking-wider">Tarikh Tutup</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-stone-600 uppercase tracking-wider">Tujuan Pemisahan</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-stone-600 uppercase tracking-wider">No. Kotak</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-stone-600 uppercase tracking-wider">Person in Charge</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-stone-200">
                        @foreach($allPemisahans as $i => $pemisahan)
                        <tr class="hover:bg-stone-50 even:bg-stone-50/40 {{ $canWrite ? 'cursor-pointer' : '' }} transition-colors"
                            @if($canWrite) onclick="window.location.href='{{ route('pemisahan.edit', $pemisahan) }}'" @endif>
                            <td class="px-3 py-2 text-sm text-stone-500 text-right tabular-nums">{{ $i + 1 }}</td>
                            <td class="px-3 py-2 text-sm font-mono text-uitm-purple-700 font-medium">{{ $pemisahan->fail->noRujukan->no_rujukan_full }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700">{{ $pemisahan->fail->noRujukan->perkara }} — Jilid {{ $pemisahan->fail->jilid }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700 tabular-nums">{{ $pemisahan->tarikh_pemisahan?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700 tabular-nums">{{ $pemisahan->fail->tarikh_pertama?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700 tabular-nums">{{ $pemisahan->fail->tarikh_akhir?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700">{{ $pemisahan->tujuan_pemisahan ?? '—' }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700">{{ $pemisahan->fail->kotak ?? '—' }}</td>
                            <td class="px-3 py-2 text-sm text-stone-700">{{ $pemisahan->person_in_charge ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif

            @if($pemisahans->isEmpty() && $allPemisahans->isEmpty())
            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl p-12 text-center">
                <div class="flex flex-col items-center gap-3 text-stone-400">
                    <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                    <p class="text-sm font-medium">Tiada rekod pemisahan</p>
                    <p class="text-xs text-stone-400">Daftar fail dan isi No. Kotak dahulu.</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        let currentKotak = null;

        function selectKotak(kotak) {
            currentKotak = kotak;
            document.getElementById('selectedKotak').textContent = kotak;
            document.getElementById('printBar').classList.remove('hidden');
        }

        function clearKotak() {
            currentKotak = null;
            document.getElementById('printBar').classList.add('hidden');
        }

        function printDoc(type) {
            if (!currentKotak) return;
            const urls = {
                'pemisahan': '{{ route('pemisahan.print-pemisahan') }}',
                'pentadbiran': '{{ route('pemisahan.print-pentadbiran') }}',
                'staf': '{{ route('pemisahan.print-staf') }}',
                'pelajar': '{{ route('pemisahan.print-pelajar') }}',
            };
            window.open(urls[type] + '?kotak=' + encodeURIComponent(currentKotak), '_blank');
        }
    </script>
</x-app-layout>
