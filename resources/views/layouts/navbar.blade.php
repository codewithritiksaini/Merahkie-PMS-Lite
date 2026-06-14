{{-- ===== TOP NAVBAR ===== --}}
<header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-4 border-b border-gray-200 bg-white px-4 sm:px-6 shadow-sm">

    {{-- Mobile hamburger --}}
    <button @click="mobileSidebarOpen = true"
            class="lg:hidden text-gray-500 hover:text-gray-700 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
        <i class="fas fa-bars text-base"></i>
    </button>

    {{-- Desktop sidebar toggle --}}
    <button @click="sidebarOpen = !sidebarOpen"
            class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
        <i class="fas fa-bars text-sm"></i>
    </button>

    {{-- Search --}}
    <div class="flex-1 max-w-md">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
            <input type="text"
                   placeholder="Search reservations, guests..."
                   class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent placeholder-gray-400">
        </div>
    </div>

    <div class="flex items-center gap-2 ml-auto">

        {{-- Date display --}}
        <div class="hidden sm:flex items-center gap-1.5 text-xs text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
            <i class="fas fa-calendar-day text-indigo-400"></i>
            <span>{{ now()->format('D, d M Y') }}</span>
        </div>

        {{-- Notifications --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="relative w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                <i class="fas fa-bell text-sm"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
            </button>
            <div x-show="open" @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50"
                 style="display:none">
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-800">Notifications</p>
                </div>
                <div class="px-4 py-8 text-center">
                    <i class="fas fa-bell-slash text-2xl text-gray-300 mb-2 block"></i>
                    <p class="text-sm text-gray-400">No new notifications</p>
                </div>
            </div>
        </div>

        {{-- Quick Action --}}
        <a href="{{ route('reservations.index') }}"
           class="hidden sm:inline-flex items-center gap-2 btn-primary btn-sm">
            <i class="fas fa-plus text-xs"></i>
            <span>New Booking</span>
        </a>

        {{-- User Dropdown --}}
        @auth
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-200">
                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center shrink-0">
                    <span class="text-white text-xs font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-sm font-semibold text-gray-800 leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 leading-tight">
                        {{ Auth::user()->role?->name ?? 'Staff' }}
                    </p>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:block" :class="open ? 'rotate-180' : ''" style="transition:transform 0.2s"></i>
            </button>

            <div x-show="open" @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50"
                 style="display:none">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
                <a href="{{ route('settings') }}" wire:navigate class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-cog w-4 text-gray-400"></i> Settings
                </a>
                <div class="border-t border-gray-100 mt-1 pt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt w-4"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endauth
    </div>
</header>
