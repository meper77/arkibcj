<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center h-8 w-8 rounded-full text-stone-500 hover:bg-stone-100 hover:text-uitm-purple-700 transition">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/></svg>
            </a>
            <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
                Kemaskini Jawatan: {{ $user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-stone-200 rounded-xl p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-uitm-purple-700 tracking-tight">Tetapan Jawatan</h3>
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

                <dl class="mb-5 p-4 bg-uitm-purple-50/40 rounded-lg border border-uitm-purple-100 border-l-4 border-l-uitm-gold-400 grid grid-cols-1 sm:grid-cols-3 gap-x-3 gap-y-2 text-sm">
                    <dt class="text-xs font-medium text-uitm-purple-700 uppercase tracking-wider">Pengguna</dt>
                    <dd class="sm:col-span-2 font-semibold text-stone-900">{{ $user->name }}</dd>

                    <dt class="text-xs font-medium text-uitm-purple-700 uppercase tracking-wider">Emel</dt>
                    <dd class="sm:col-span-2 text-stone-900">{{ $user->email }}</dd>

                    <dt class="text-xs font-medium text-uitm-purple-700 uppercase tracking-wider">Jawatan Semasa</dt>
                    <dd class="sm:col-span-2">
                        @if($user->position)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-uitm-gold-100 text-uitm-purple-800 text-xs font-semibold tracking-wide">{{ $user->position }}</span>
                        @else
                            <span class="text-stone-400 text-sm">Tiada</span>
                        @endif
                    </dd>
                </dl>

                <form method="POST" action="{{ route('users.update-position', $user) }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">JAWATAN BAHARU</label>
                        <select name="position"
                                class="block w-full rounded-lg border-stone-300 shadow-sm text-sm focus:border-uitm-purple-500 focus:ring-uitm-purple-500 transition">
                            <option value="">— Tiada —</option>
                            @foreach(['PTRJ', 'PRJ'] as $pos)
                            <option value="{{ $pos }}"
                                {{ old('position', $user->position) === $pos ? 'selected' : '' }}
                                {{ isset($availablePositions[$pos]) && !$availablePositions[$pos] ? 'disabled' : '' }}>
                                {{ $pos }}{{ isset($availablePositions[$pos]) && !$availablePositions[$pos] ? ' (sudah digunakan)' : '' }}
                            </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('position')" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-3 border-t border-stone-200 pt-5">
                        <a href="{{ route('users.index') }}"
                           class="px-4 py-2 bg-stone-100 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-200 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-uitm-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-uitm-purple-800 transition">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
