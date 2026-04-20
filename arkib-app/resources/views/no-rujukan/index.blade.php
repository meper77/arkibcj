<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
            No. Rujukan
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

            {{-- Section heading --}}
            <div class="mb-5">
                <h3 class="text-xl font-semibold text-uitm-purple-700 tracking-tight">Senarai No. Rujukan</h3>
                <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2 mb-4">
                <a href="{{ route('no-rujukan.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 focus:outline-none focus:ring-2 focus:ring-uitm-purple-500 focus:ring-offset-1 transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a.75.75 0 01.75.75v3.5h3.5a.75.75 0 010 1.5h-3.5v3.5a.75.75 0 01-1.5 0v-3.5h-3.5a.75.75 0 010-1.5h3.5v-3.5A.75.75 0 0110 5z" clip-rule="evenodd"/></svg>
                    Daftar
                </a>

                <button type="button" onclick="document.getElementById('batchModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white text-uitm-purple-700 text-sm font-medium rounded-lg border border-uitm-purple-200 hover:bg-uitm-purple-50 focus:outline-none focus:ring-2 focus:ring-uitm-purple-500 focus:ring-offset-1 transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M9.25 13.25a.75.75 0 001.5 0V4.636l2.955 3.129a.75.75 0 001.09-1.03l-4.25-4.5a.75.75 0 00-1.09 0l-4.25 4.5a.75.75 0 101.09 1.03L9.25 4.636v8.614z"/><path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z"/></svg>
                    Batch (CSV)
                </button>

                <button type="button" onclick="toggleSelectMode()" id="selectBtn"
                        class="inline-flex items-center gap-2 px-4 py-2 text-uitm-purple-700 text-sm font-medium rounded-lg hover:bg-uitm-purple-50 focus:outline-none focus:ring-2 focus:ring-uitm-purple-500 focus:ring-offset-1 transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2.5 3A1.5 1.5 0 001 4.5v.75a.75.75 0 001.5 0V4.5h.75a.75.75 0 000-1.5h-.75zm10 0a.75.75 0 000 1.5h.75v.75a.75.75 0 001.5 0V4.5A1.5 1.5 0 0013.25 3h-.75zM2.5 15.5a.75.75 0 01-.75-.75v-.75a.75.75 0 011.5 0v.75c0 .414-.336.75-.75.75zm10 0a.75.75 0 010-1.5h.75v-.75a.75.75 0 011.5 0v.75a1.5 1.5 0 01-1.5 1.5h-.75z" clip-rule="evenodd"/></svg>
                    Pilih
                </button>

                <button type="button" onclick="deleteSelected()" id="deleteBtn"
                        class="hidden inline-flex items-center gap-2 px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-1 transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                    Padam Dipilih
                </button>

                <button type="button" onclick="cancelSelect()" id="cancelBtn"
                        class="hidden inline-flex items-center px-4 py-2 bg-stone-100 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-200 transition">
                    Batal
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl overflow-hidden">
                <form id="deleteForm" method="POST" action="{{ route('no-rujukan.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-uitm-purple-50">
                            <tr>
                                <th class="px-4 py-3 text-left w-8">
                                    <input type="checkbox" id="selectAll" onchange="toggleAll(this)"
                                           class="hidden rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider w-16">BIL.</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">No. Rujukan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-uitm-purple-800 uppercase tracking-wider">Perkara</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-stone-200">
                            @forelse($noRujukans as $i => $nr)
                            <tr class="hover:bg-stone-50 even:bg-stone-50/40 transition-colors">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="ids[]" value="{{ $nr->id }}"
                                           class="row-checkbox hidden rounded border-stone-300 text-uitm-purple-700 focus:ring-uitm-purple-500">
                                </td>
                                <td class="px-4 py-3 text-sm text-stone-500 text-right tabular-nums">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-uitm-purple-700 font-medium">{{ $nr->no_rujukan_full }}</td>
                                <td class="px-4 py-3 text-sm text-stone-700">{{ $nr->perkara }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3 text-stone-400">
                                        <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                        <p class="text-sm font-medium">Tiada rekod didaftarkan</p>
                                        <a href="{{ route('no-rujukan.create') }}" class="text-xs text-uitm-purple-700 hover:underline">Daftar No. Rujukan pertama</a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <!-- Batch Modal -->
    <div id="batchModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl shadow-2xl ring-1 ring-stone-200 p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-1 text-uitm-purple-700">Import CSV — No. Rujukan</h3>
            <div class="h-0.5 w-10 bg-uitm-gold-400 rounded-full mb-4"></div>

            <p class="text-sm text-stone-600 mb-4">
                Muat turun templat CSV terlebih dahulu, isi data, kemudian muat naik.
            </p>

            <a href="{{ route('no-rujukan.csv-template') }}"
               class="inline-flex items-center gap-2 mb-4 px-3 py-2 bg-stone-100 text-stone-700 text-sm rounded-lg hover:bg-stone-200 transition">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 2.75a.75.75 0 00-1.5 0v8.614L6.295 8.235a.75.75 0 10-1.09 1.03l4.25 4.5a.75.75 0 001.09 0l4.25-4.5a.75.75 0 00-1.09-1.03l-2.955 3.129V2.75z"/><path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z"/></svg>
                Muat Turun Templat CSV
            </a>

            <form method="POST" action="{{ route('no-rujukan.csv-import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-stone-700 mb-1">Fail CSV</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" required
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

        function toggleSelectMode() {
            selectMode = true;
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.classList.remove('hidden'));
            document.getElementById('selectAll').classList.remove('hidden');
            document.getElementById('selectBtn').classList.add('hidden');
            document.getElementById('deleteBtn').classList.remove('hidden');
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
            document.getElementById('cancelBtn').classList.add('hidden');
        }

        function toggleAll(cb) {
            document.querySelectorAll('.row-checkbox').forEach(box => box.checked = cb.checked);
        }

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
    </script>
</x-app-layout>
