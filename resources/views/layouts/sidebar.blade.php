{{-- ===== LEFT SIDEBAR ===== --}}

{{-- Desktop Sidebar --}}
<aside class="hidden lg:flex lg:flex-col fixed inset-y-0 left-0 z-40 bg-slate-900 transition-all duration-300"
       :class="sidebarOpen ? 'w-64' : 'w-16'">

    {{-- Logo --}}
    <div class="flex h-16 items-center border-b border-slate-800 px-4 shrink-0" :class="sidebarOpen ? 'justify-between' : 'justify-center'">
        <div x-show="sidebarOpen" class="flex items-center gap-2" x-transition>
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-hotel text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-lg tracking-tight">{{ $hotelName }}</span>
        </div>
        <div x-show="!sidebarOpen" class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center" style="display:none">
            <i class="fas fa-hotel text-white text-sm"></i>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" x-show="sidebarOpen"
                class="text-slate-400 hover:text-white p-1 rounded transition-colors">
            <i class="fas fa-chevron-left text-xs"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-0.5">

        {{-- Main --}}
        <div x-show="sidebarOpen" class="px-3 pb-2">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Main</span>
        </div>

        <a href="{{ route('dashboard') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-tachometer-alt nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Dashboard</span>
        </a>

        @if(Auth::check() && Auth::user()->hasRole('admin'))
        <a href="{{ route('rooms.index') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-bed nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Rooms</span>
        </a>
        @endif

        <a href="{{ route('reservations.index') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('reservations.*') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-calendar-check nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Reservations</span>
        </a>

        <a href="{{ route('calendar') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('calendar') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-calendar-alt nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Booking Calendar</span>
        </a>

        <a href="{{ route('guests.index') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('guests.*') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-users nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Guests</span>
        </a>

        {{-- Operations --}}
        <div x-show="sidebarOpen" class="px-3 pt-4 pb-2">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Operations</span>
        </div>
        <div x-show="!sidebarOpen" class="border-t border-slate-800 my-2" style="display:none"></div>

        <a href="{{ route('checkin.index') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('checkin.*') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-sign-in-alt nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Check-In</span>
        </a>

        <a href="{{ route('checkout.index') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('checkout.*') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-sign-out-alt nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Check-Out</span>
        </a>

        <a href="{{ route('invoices.index') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-file-invoice-dollar nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Invoices</span>
        </a>

        <a href="{{ route('housekeeping.index') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('housekeeping.*') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-broom nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Housekeeping</span>
        </a>

        <a href="{{ route('maintenance.index') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-tools nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Maintenance</span>
        </a>

        {{-- Analytics --}}
        <div x-show="sidebarOpen" class="px-3 pt-4 pb-2">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Analytics</span>
        </div>
        <div x-show="!sidebarOpen" class="border-t border-slate-800 my-2" style="display:none"></div>

        <a href="{{ route('reports.daily') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-chart-bar nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Reports</span>
        </a>

        {{-- Admin --}}
        @if(Auth::check() && Auth::user()->hasRole('admin'))
        <div x-show="sidebarOpen" class="px-3 pt-4 pb-2">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Admin</span>
        </div>
        <div x-show="!sidebarOpen" class="border-t border-slate-800 my-2" style="display:none"></div>

        <a href="{{ route('users.index') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-user-shield nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Users</span>
        </a>

        <a href="{{ route('settings') }}" wire:navigate
           class="sidebar-link {{ request()->routeIs('settings') ? 'active' : '' }}"
           :class="sidebarOpen ? '' : 'justify-center px-0'">
            <i class="fas fa-cog nav-icon"></i>
            <span x-show="sidebarOpen" x-transition>Settings</span>
        </a>
        @endif

    </nav>

    {{-- Sidebar footer --}}
    <div class="border-t border-slate-800 px-3 py-3 shrink-0">
        <div class="flex items-center gap-3" :class="sidebarOpen ? '' : 'justify-center'">
            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center shrink-0">
                <span class="text-white text-xs font-bold">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
            </div>
            <div x-show="sidebarOpen" x-transition class="min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? '' }}</p>
                <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
            </div>
        </div>
    </div>
</aside>

{{-- Mobile Sidebar --}}
<aside class="lg:hidden fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 flex flex-col transform transition-transform duration-300"
       :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="flex h-16 items-center justify-between border-b border-slate-800 px-4 shrink-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-hotel text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-lg">{{ $hotelName }}</span>
        </div>
        <button @click="mobileSidebarOpen = false" class="text-slate-400 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-0.5">
        <a href="{{ route('dashboard') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt nav-icon"></i><span>Dashboard</span>
        </a>
        <a href="{{ route('rooms.index') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
            <i class="fas fa-bed nav-icon"></i><span>Rooms</span>
        </a>
        <a href="{{ route('reservations.index') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('reservations.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-check nav-icon"></i><span>Reservations</span>
        </a>
        <a href="{{ route('calendar') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt nav-icon"></i><span>Booking Calendar</span>
        </a>
        <a href="{{ route('guests.index') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('guests.*') ? 'active' : '' }}">
            <i class="fas fa-users nav-icon"></i><span>Guests</span>
        </a>
        <div class="px-3 pt-3 pb-1"><span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Operations</span></div>
        <a href="{{ route('checkin.index') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('checkin.*') ? 'active' : '' }}">
            <i class="fas fa-sign-in-alt nav-icon"></i><span>Check-In</span>
        </a>
        <a href="{{ route('checkout.index') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('checkout.*') ? 'active' : '' }}">
            <i class="fas fa-sign-out-alt nav-icon"></i><span>Check-Out</span>
        </a>
        <a href="{{ route('invoices.index') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar nav-icon"></i><span>Invoices</span>
        </a>
        <a href="{{ route('housekeeping.index') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('housekeeping.*') ? 'active' : '' }}">
            <i class="fas fa-broom nav-icon"></i><span>Housekeeping</span>
        </a>
        <a href="{{ route('maintenance.index') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
            <i class="fas fa-tools nav-icon"></i><span>Maintenance</span>
        </a>
        <div class="px-3 pt-3 pb-1"><span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Analytics</span></div>
        <a href="{{ route('reports.daily') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar nav-icon"></i><span>Reports</span>
        </a>
        @if(Auth::check() && Auth::user()->hasRole('admin'))
        <div class="px-3 pt-3 pb-1"><span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Admin</span></div>
        <a href="{{ route('users.index') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fas fa-user-shield nav-icon"></i><span>Users</span>
        </a>
        <a href="{{ route('settings') }}" wire:navigate @click="mobileSidebarOpen = false"
           class="sidebar-link {{ request()->routeIs('settings') ? 'active' : '' }}">
            <i class="fas fa-cog nav-icon"></i><span>Settings</span>
        </a>
        @endif
    </nav>
</aside>
