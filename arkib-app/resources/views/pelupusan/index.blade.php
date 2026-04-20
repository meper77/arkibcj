<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
            Pelupusan
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

            {{-- ================= SENARAI MENUNGGU KELULUSAN ================= --}}
            <div class="mb-5">
                <h3 class="text-xl font-semibold text-uitm-purple-700 tracking-tight">Senarai Menunggu Kelulusan</h3>
                <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>
            </div>

            @forelse($pending as $kotak => $records)
                @php $count = $records->count(); @endphp
                <div class="mb-6 bg-white shadow-sm ring-1 ring-stone-200 rounded-xl overflow-hidden border-l-4 border-l-uitm-gold-400">
                    <div class="px-4 py-3 bg-uitm-purple-100">
                        <h4 class="text-uitm-purple-800 font-semibold text-sm tracking-wide">KOTAK: {{ $kotak }}</h4>
                    </div>
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-uitm-purple-50">
                            <tr>
                                <th class="px-3 py-2 text-right text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider w-14">BIL.</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Tajuk Fail</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Status</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Person in Charge</th>
                                <th class="px-3 py-2 text-right text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-stone-200">
                            @php
                                $allApprove = $records->every(fn($r) => $r->status === 'APPROVE');
                                $statusSummary = $records->pluck('status')->unique()->implode(', ');
                                $kotakStatus = $records->pluck('status')->unique()->count() === 1 ? $records->first()->status : 'PENDING';
                            @endphp
                            @foreach($records->values() as $i => $p)
                            <tr class="hover:bg-stone-50 even:bg-stone-50/40 transition-colors">
                                <td class="px-3 py-3 text-sm text-stone-500 text-right tabular-nums">{{ $i + 1 }}</td>
                                <td class="px-3 py-3 text-sm text-stone-700">
                                    {{ $p->pemisahan->fail->noRujukan->perkara ?? '—' }}
                                    @if($p->pemisahan->fail->jilid ?? null) <span class="text-stone-400">— Jilid {{ $p->pemisahan->fail->jilid }}</span> @endif
                                </td>
                                @if($i === 0)
                                    <td rowspan="{{ $count }}" class="px-3 py-3 align-middle">
                                        <form method="POST" action="{{ route('pelupusan.kotak-status') }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="kotak" value="{{ $kotak }}">
                                            <select name="status" onchange="this.form.submit()"
                                                    class="text-sm rounded-lg border-stone-300 shadow-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 font-medium
                                                    {{ $kotakStatus === 'APPROVE' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($kotakStatus === 'DECLINE' ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-amber-50 text-amber-800 border-amber-200') }}">
                                                <option value="PENDING" {{ $kotakStatus === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                                <option value="APPROVE" {{ $kotakStatus === 'APPROVE' ? 'selected' : '' }}>APPROVE</option>
                                                <option value="DECLINE" {{ $kotakStatus === 'DECLINE' ? 'selected' : '' }}>DECLINE</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td rowspan="{{ $count }}" class="px-3 py-3 align-middle text-sm text-stone-700">
                                        {{ $p->person_in_charge ?? '—' }}
                                    </td>
                                    <td rowspan="{{ $count }}" class="px-3 py-3 align-middle text-right">
                                        @if($allApprove)
                                        <form method="POST" action="{{ route('pelupusan.lupus-kotak') }}"
                                              onsubmit="return confirm('Lupus semua fail dalam kotak {{ $kotak }}? Tindakan tidak boleh dibatalkan.')">
                                            @csrf
                                            <input type="hidden" name="kotak" value="{{ $kotak }}">
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-uitm-purple-700 text-white text-xs font-medium rounded-md shadow-sm hover:bg-uitm-purple-800 transition">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5z" clip-rule="evenodd"/></svg>
                                                Lupus
                                            </button>
                                        </form>
                                        @else
                                        <span class="inline-flex items-center text-xs text-stone-400 italic">Semua rekod mesti APPROVE</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            @empty
                <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl p-10 text-center mb-8">
                    <div class="flex flex-col items-center gap-2 text-stone-400">
                        <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-medium">Tiada rekod menunggu kelulusan</p>
                    </div>
                </div>
            @endforelse

            {{-- ================= SELEPAS PELUPUSAN ================= --}}
            <div class="mb-5 mt-10">
                <h3 class="text-xl font-semibold text-uitm-purple-700 tracking-tight">Selepas Pelupusan</h3>
                <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>
            </div>

            @forelse($afterLupus as $kotak => $records)
                @php $count = $records->count(); @endphp
                <div class="mb-4 bg-white shadow-sm ring-1 ring-stone-200 rounded-xl overflow-hidden border-l-4 border-l-uitm-gold-400">
                    <div class="px-4 py-3 flex flex-wrap justify-between items-center gap-2 bg-uitm-purple-100">
                        <h4 class="text-uitm-purple-800 font-semibold text-sm tracking-wide">KOTAK: {{ $kotak }}</h4>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pelupusan.print') }}?kotak={{ urlencode($kotak) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1 bg-white text-uitm-purple-700 text-xs font-medium rounded-md ring-1 ring-uitm-purple-200 hover:bg-uitm-gold-50 transition">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 2.75C5 1.784 5.784 1 6.75 1h6.5c.966 0 1.75.784 1.75 1.75v3.552c.377.046.752.097 1.126.153A2.212 2.212 0 0118 8.653v4.097A2.25 2.25 0 0115.75 15h-.241l.305 1.984A1.75 1.75 0 0114.084 19H5.916a1.75 1.75 0 01-1.73-2.016L4.492 15H4.25A2.25 2.25 0 012 12.75V8.653c0-1.082.775-2.034 1.874-2.198.374-.056.75-.107 1.127-.153V2.75z" clip-rule="evenodd"/></svg>
                                Cetak Borang Pelupusan
                            </a>
                            <form method="POST" action="{{ route('pelupusan.selepas-kotak') }}"
                                  onsubmit="return confirm('Padam semua rekod selepas pelupusan untuk kotak {{ $kotak }}?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="kotak" value="{{ $kotak }}">
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-white text-rose-700 text-xs font-medium rounded-md ring-1 ring-rose-200 hover:bg-rose-50 transition">
                                    Padam
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-uitm-purple-50">
                            <tr>
                                <th class="px-3 py-2 text-right text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider w-14">BIL.</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Tajuk Fail</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Person in Charge</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-stone-200">
                            @foreach($records->values() as $i => $r)
                            <tr class="even:bg-stone-50/40">
                                <td class="px-3 py-2 text-sm text-stone-500 text-right tabular-nums">{{ $i + 1 }}</td>
                                <td class="px-3 py-2 text-sm text-stone-700">{{ $r->tajuk_fail ?? '—' }}</td>
                                @if($i === 0)
                                    <td rowspan="{{ $count }}" class="px-3 py-2 align-middle text-sm text-stone-700">
                                        {{ $r->person_in_charge ?? '—' }}
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            @empty
                <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl p-10 text-center">
                    <div class="flex flex-col items-center gap-2 text-stone-400">
                        <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                        <p class="text-sm font-medium">Tiada rekod dilupuskan lagi</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
