<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
            Pengurusan Pengguna
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

            <div class="mb-4">
                <a href="{{ route('users.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 focus:outline-none focus:ring-2 focus:ring-uitm-purple-500 focus:ring-offset-1 transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a.75.75 0 01.75.75v3.5h3.5a.75.75 0 010 1.5h-3.5v3.5a.75.75 0 01-1.5 0v-3.5h-3.5a.75.75 0 010-1.5h3.5v-3.5A.75.75 0 0110 5z" clip-rule="evenodd"/></svg>
                    Tambah Pengguna
                </a>
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
                            <td class="px-3 py-3 text-sm text-stone-700">{{ $user->fakulti_bahagian ?? '—' }}</td>
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
                                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/></svg>
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
                                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                    <p class="text-sm font-medium">Tiada pengguna berdaftar</p>
                                    <a href="{{ route('users.create') }}" class="text-xs text-uitm-purple-700 hover:underline">Tambah pengguna pertama</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
