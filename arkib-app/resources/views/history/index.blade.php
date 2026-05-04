<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-uitm-purple-700 tracking-tight">
            History
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg flex items-start gap-3">
                    <svg class="h-5 w-5 mt-0.5 flex-shrink-0 text-emerald-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="mb-5">
                <h3 class="text-xl font-semibold text-uitm-purple-700 tracking-tight">Senarai Aktiviti</h3>
                <div class="mt-1 h-0.5 w-12 bg-uitm-gold-400 rounded-full"></div>
            </div>

            @if($histories->isEmpty())
                <div class="p-6 bg-white border border-stone-200 rounded-lg text-center text-stone-500 text-sm">
                    Tiada rekod history.
                </div>
            @else
                <div class="relative">
                    <div class="absolute left-4 top-2 bottom-2 w-px bg-uitm-purple-200"></div>
                    <div class="space-y-4">
                        @foreach($histories as $history)
                            <div class="relative pl-12">
                                <span class="absolute left-2.5 top-4 h-3 w-3 rounded-full bg-uitm-gold-400 border-2 border-uitm-purple-700"></span>
                                <div class="bg-white border border-stone-200 rounded-lg shadow-uitm-sm p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-uitm-purple-700">{{ $history->action }}</p>
                                            @if($history->description)
                                                <p class="mt-1 text-sm text-stone-600">{{ $history->description }}</p>
                                            @endif
                                            <p class="mt-2 text-xs text-stone-500">
                                                {{ $history->user_name ?? '—' }}
                                                &bull; {{ $history->created_at->diffForHumans() }}
                                                ({{ $history->created_at->format('d/m/Y H:i') }})
                                            </p>
                                        </div>
                                        @if(auth()->user()->is_superadmin)
                                            <form method="POST" action="{{ route('history.destroy', $history) }}"
                                                  onsubmit="return confirm('Padam rekod history ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-xs px-2 py-1 rounded border border-rose-200 text-rose-700 hover:bg-rose-50 transition">
                                                    Padam
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6">
                    {{ $histories->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
