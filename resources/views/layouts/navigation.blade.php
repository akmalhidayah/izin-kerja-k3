<nav x-data="{ open: false }"
class="sticky top-0 z-50 
bg-green-600 via-emerald-600 to-green-700
backdrop-blur-md
border-b border-white/20 
shadow-md">

    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- LEFT -->
            <div class="flex items-center gap-3">

                <!-- LOGO -->
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo-k3.png') }}"
                        class="w-10 h-10 rounded-xl bg-white p-1 shadow-md hover:scale-105 transition">

                    <img src="{{ asset('images/logo-st.png') }}"
                        class="w-10 h-10 rounded-xl bg-white p-1 shadow-md hover:scale-105 transition">
                </div>

                <!-- TITLE -->
                <div class="flex flex-col leading-snug">

                    <!-- DESKTOP -->
                    <div class="hidden sm:block">
                        <h1 class="text-sm sm:text-base font-semibold text-white drop-shadow-sm">
                            Unit Occupational Health & Safety
                        </h1>

                        <h2 class="text-sm text-white/80">
                            PT. Semen Tonasa
                        </h2>

                        <p class="text-[11px] text-white italic drop-shadow-sm">
                            "Berangkat sehat, pulang selamat"
                        </p>
                    </div>

                    <!-- MOBILE -->
                    <div class="sm:hidden">
                        <h1 class="text-sm font-semibold text-white drop-shadow-sm">
                            OHS Semen Tonasa
                        </h1>

                        <p class="text-[10px] text-white italic">
                            "Berangkat sehat, pulang selamat"
                        </p>
                    </div>

                </div>
            </div>

            <!-- RIGHT -->
            <div class="hidden sm:flex items-center gap-3">

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 px-3 py-1.5 rounded-full 
                            bg-white/90 backdrop-blur 
                            border border-white/30 
                            shadow-md 
                            hover:shadow-lg hover:bg-white transition">

                            <i class="fas fa-user-circle text-green-700 text-lg"></i>

                            <span class="text-sm text-gray-700 font-medium">
                                {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                            </span>

                            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                viewBox="0 0 20 20">
                                <path fill="currentColor" d="M5.25 7.5l4.75 4.75L14.75 7.5"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

            </div>

            <!-- MOBILE BUTTON -->
            <div class="sm:hidden">
                <button @click="open = !open"
                    class="p-2 rounded-full bg-white/90 backdrop-blur border border-white/30 shadow-md hover:bg-white transition">

                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- GARIS AKSEN -->
    <div class="h-[2px] bg-gradient-to-r from-green-400 via-emerald-300 to-green-400"></div>

    <!-- MOBILE MENU -->
    <div x-show="open" x-transition 
        class="sm:hidden border-t border-gray-200 bg-white shadow-md">

        <div class="px-4 py-3 space-y-2">
            <x-responsive-nav-link :href="route('dashboard')" 
                :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
        </div>

        <div class="border-t px-4 py-3">
            <div class="text-sm font-semibold text-gray-800">
                {{ Auth::check() ? Auth::user()->name : 'Guest' }}
            </div>
            <div class="text-xs text-gray-500">
                {{ Auth::check() ? Auth::user()->email : '' }}
            </div>

            <div class="mt-2 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profile
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

</nav>