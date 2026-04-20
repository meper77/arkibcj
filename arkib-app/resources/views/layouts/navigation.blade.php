<nav x-data="{ open: false }" class="bg-white border-b border-stone-200 shadow-uitm-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo / Brand -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('no-rujukan.index') }}" class="font-bold text-lg text-uitm-purple-700 hover:text-uitm-purple-800 transition-colors tracking-tight">
                        Sistem Arkib UiTM
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-6 sm:flex items-center">
                    <a href="{{ route('no-rujukan.index') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium border-b-2 transition duration-150 ease-in-out
                              {{ request()->routeIs('no-rujukan.*') ? 'border-uitm-purple-700 text-uitm-purple-700 font-semibold' : 'border-transparent text-stone-600 hover:text-uitm-purple-700 hover:border-uitm-gold-400' }}">
                        No. Rujukan
                    </a>
                    <a href="{{ route('fail.index') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium border-b-2 transition duration-150 ease-in-out
                              {{ request()->routeIs('fail.*') ? 'border-uitm-purple-700 text-uitm-purple-700 font-semibold' : 'border-transparent text-stone-600 hover:text-uitm-purple-700 hover:border-uitm-gold-400' }}">
                        Fail
                    </a>
                    <a href="{{ route('pemisahan.index') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium border-b-2 transition duration-150 ease-in-out
                              {{ request()->routeIs('pemisahan.*') ? 'border-uitm-purple-700 text-uitm-purple-700 font-semibold' : 'border-transparent text-stone-600 hover:text-uitm-purple-700 hover:border-uitm-gold-400' }}">
                        Pemisahan Rekod
                    </a>
                    <a href="{{ route('pelupusan.index') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium border-b-2 transition duration-150 ease-in-out
                              {{ request()->routeIs('pelupusan.*') ? 'border-uitm-purple-700 text-uitm-purple-700 font-semibold' : 'border-transparent text-stone-600 hover:text-uitm-purple-700 hover:border-uitm-gold-400' }}">
                        Pelupusan
                    </a>
                    @if(Auth::user()->is_superadmin)
                    <a href="{{ route('users.index') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium border-b-2 transition duration-150 ease-in-out
                              {{ request()->routeIs('users.*') ? 'border-uitm-purple-700 text-uitm-purple-700 font-semibold' : 'border-transparent text-stone-600 hover:text-uitm-purple-700 hover:border-uitm-gold-400' }}">
                        Pengguna
                    </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-stone-200 text-sm leading-4 font-medium rounded-md text-stone-600 bg-white hover:text-uitm-purple-700 hover:border-uitm-purple-300 hover:bg-uitm-purple-50 focus:outline-none focus:ring-2 focus:ring-uitm-purple-200 transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-stone-500 hover:text-uitm-purple-700 hover:bg-uitm-purple-50 focus:outline-none focus:bg-uitm-purple-50 focus:text-uitm-purple-700 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-stone-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('no-rujukan.index')" :active="request()->routeIs('no-rujukan.*')">
                No. Rujukan
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('fail.index')" :active="request()->routeIs('fail.*')">
                Fail
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pemisahan.index')" :active="request()->routeIs('pemisahan.*')">
                Pemisahan Rekod
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pelupusan.index')" :active="request()->routeIs('pelupusan.*')">
                Pelupusan
            </x-responsive-nav-link>
            @if(Auth::user()->is_superadmin)
            <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                Pengguna
            </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-stone-200 bg-stone-50/50">
            <div class="px-4">
                <div class="font-semibold text-base text-uitm-purple-700">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-stone-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profil
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
