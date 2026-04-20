<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pemisahan.index') }}" class="inline-flex items-center justify-center h-8 w-8 rounded-full text-stone-500 hover:bg-stone-100 hover:text-uitm-purple-700 transition">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/></svg>
            </a>
            <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
                Kemaskini Rekod Pemisahan
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-uitm-purple-700 tracking-tight">Maklumat Pemisahan</h3>
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

                <!-- Info display -->
                <dl class="mb-6 p-4 bg-uitm-purple-50/40 rounded-lg border border-uitm-purple-100 border-l-4 border-l-uitm-gold-400 grid grid-cols-1 sm:grid-cols-3 gap-x-3 gap-y-2 text-sm">
                    <dt class="text-xs font-medium text-uitm-purple-700 uppercase tracking-wider">No. Fail</dt>
                    <dd class="sm:col-span-2 font-mono text-stone-900">{{ $pemisahan->fail->noRujukan->no_rujukan_full }}</dd>

                    <dt class="text-xs font-medium text-uitm-purple-700 uppercase tracking-wider">Tajuk Fail</dt>
                    <dd class="sm:col-span-2 text-stone-900">{{ $pemisahan->fail->noRujukan->perkara }} — Jilid {{ $pemisahan->fail->jilid }}</dd>

                    <dt class="text-xs font-medium text-uitm-purple-700 uppercase tracking-wider">Tarikh Buka</dt>
                    <dd class="sm:col-span-2 text-stone-900 tabular-nums">{{ $pemisahan->fail->tarikh_pertama?->format('d/m/Y') ?? '—' }}</dd>

                    <dt class="text-xs font-medium text-uitm-purple-700 uppercase tracking-wider">Tarikh Tutup</dt>
                    <dd class="sm:col-span-2 text-stone-900 tabular-nums">{{ $pemisahan->fail->tarikh_akhir?->format('d/m/Y') ?? '—' }}</dd>

                    <dt class="text-xs font-medium text-uitm-purple-700 uppercase tracking-wider">No. Kotak</dt>
                    <dd class="sm:col-span-2 text-stone-900">{{ $pemisahan->fail->kotak ?? '—' }}</dd>
                </dl>

                <form method="POST" action="{{ route('pemisahan.update', $pemisahan) }}" x-data class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">TARIKH PEMISAHAN <span class="text-uitm-purple-700">*</span></label>
                        <input type="date" name="tarikh_pemisahan" required
                               value="{{ old('tarikh_pemisahan', $pemisahan->tarikh_pemisahan?->format('Y-m-d')) }}"
                               class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                        <x-input-error :messages="$errors->get('tarikh_pemisahan')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">TUJUAN PEMISAHAN <span class="text-uitm-purple-700">*</span></label>
                        <textarea name="tujuan_pemisahan" rows="3" required x-soft-rule="uppercase"
                                  class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition"
                                  placeholder="Nyatakan tujuan pemisahan rekod…">{{ old('tujuan_pemisahan', $pemisahan->tujuan_pemisahan) }}</textarea>
                        <x-input-error :messages="$errors->get('tujuan_pemisahan')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">PERSON IN CHARGE</label>
                        <input type="text" readonly value="{{ Auth::user()->name }}"
                               class="block w-full rounded-lg bg-stone-50 border-stone-200 shadow-sm text-sm text-stone-600 cursor-not-allowed">
                    </div>

                    <div class="flex justify-end gap-3 border-t border-stone-200 pt-5">
                        <a href="{{ route('pemisahan.index') }}"
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
