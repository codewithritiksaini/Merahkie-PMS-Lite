<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ now()->format('l, d F Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reservations.index') }}" class="btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Booking
            </a>
            <a href="{{ route('housekeeping.index') }}" class="btn-secondary btn-sm">
                <i class="fas fa-broom"></i> Housekeeping
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <div class="stat-card col-span-1">
            <div class="stat-icon bg-slate-100 text-slate-600"><i class="fas fa-bed text-xl"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $totalRooms }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Total Rooms</p>
            </div>
        </div>
        <div class="stat-card col-span-1">
            <div class="stat-icon bg-red-100 text-red-600"><i class="fas fa-door-closed text-xl"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $occupiedRooms }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Occupied</p>
            </div>
        </div>
        <div class="stat-card col-span-1">
            <div class="stat-icon bg-emerald-100 text-emerald-600"><i class="fas fa-door-open text-xl"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $availableRooms }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Available</p>
            </div>
        </div>
        <div class="stat-card col-span-1">
            <div class="stat-icon bg-blue-100 text-blue-600"><i class="fas fa-sign-in-alt text-xl"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $checkInsToday }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Arrivals Today</p>
            </div>
        </div>
        <div class="stat-card col-span-1">
            <div class="stat-icon bg-orange-100 text-orange-600"><i class="fas fa-sign-out-alt text-xl"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $checkOutsToday }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Departures Today</p>
            </div>
        </div>
        <div class="stat-card col-span-1">
            <div class="stat-icon bg-indigo-100 text-indigo-600"><i class="fas fa-dollar-sign text-xl"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($revenueToday, 0) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Revenue Today</p>
            </div>
        </div>
    </div>

    {{-- Middle Row: Occupancy + Housekeeping --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Occupancy Card --}}
        <div class="pms-card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Live Occupancy</h3>
                <span class="text-xl font-bold text-indigo-600">{{ $occupancyPercent }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 mb-4">
                <div class="bg-indigo-600 h-3 rounded-full transition-all duration-500"
                     style="width: {{ $occupancyPercent }}%"></div>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="bg-red-50 rounded-lg py-2">
                    <p class="text-lg font-bold text-red-600">{{ $occupiedRooms }}</p>
                    <p class="text-xs text-gray-500">Occupied</p>
                </div>
                <div class="bg-emerald-50 rounded-lg py-2">
                    <p class="text-lg font-bold text-emerald-600">{{ $availableRooms }}</p>
                    <p class="text-xs text-gray-500">Available</p>
                </div>
                <div class="bg-blue-50 rounded-lg py-2">
                    <p class="text-lg font-bold text-blue-600">{{ $reservedRooms }}</p>
                    <p class="text-xs text-gray-500">Reserved</p>
                </div>
            </div>
        </div>

        {{-- Housekeeping Status --}}
        <div class="pms-card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Housekeeping</h3>
                <a href="{{ route('housekeeping.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
            </div>
            @if($housekeepingPending > 0)
            <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center shrink-0">
                    <i class="fas fa-broom text-amber-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-amber-800">{{ $housekeepingPending }} rooms need attention</p>
                    <p class="text-xs text-amber-600 mt-0.5">Pending cleaning or maintenance</p>
                </div>
            </div>
            @else
            <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                    <i class="fas fa-check text-emerald-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-emerald-800">All rooms are clean</p>
                    <p class="text-xs text-emerald-600 mt-0.5">No pending housekeeping tasks</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="pms-card p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('checkin.index') }}" class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-indigo-50 transition-colors group">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                        <i class="fas fa-sign-in-alt text-indigo-600 text-sm"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Process Check-In</span>
                    <i class="fas fa-chevron-right text-xs text-gray-400 ml-auto"></i>
                </a>
                <a href="{{ route('checkout.index') }}" class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-orange-50 transition-colors group">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                        <i class="fas fa-sign-out-alt text-orange-600 text-sm"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Process Check-Out</span>
                    <i class="fas fa-chevron-right text-xs text-gray-400 ml-auto"></i>
                </a>
                <a href="{{ route('reports.daily') }}" class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-blue-50 transition-colors group">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-chart-bar text-blue-600 text-sm"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Daily Report</span>
                    <i class="fas fa-chevron-right text-xs text-gray-400 ml-auto"></i>
                </a>
                <a href="{{ route('calendar') }}" class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-emerald-50 transition-colors group">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                        <i class="fas fa-calendar-alt text-emerald-600 text-sm"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Booking Calendar</span>
                    <i class="fas fa-chevron-right text-xs text-gray-400 ml-auto"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Live Room Floor Map --}}
    <div class="pms-card mb-6">
        <div class="pms-card-header">
            <div class="flex items-center gap-2">
                <i class="fas fa-map text-indigo-600"></i>
                <h3 class="text-sm font-semibold text-gray-800">Live Room Floor Map</h3>
            </div>
            <div class="flex flex-wrap gap-2.5 text-xs">
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Available</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Occupied</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Reserved</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-orange-400"></span> Dirty</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span> Maintenance</span>
            </div>
        </div>
        <div class="p-5 space-y-6">
            @foreach($rooms->groupBy(fn($r) => substr($r->room_number, 0, 1)) as $floor => $floorRooms)
            <div>
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2.5">Floor {{ $floor }}</h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-10 gap-3">
                    @foreach($floorRooms as $room)
                        @php
                            $hk = optional($room->latestHousekeeping)->status ?? 'Clean';
                            $status = $room->status;
                            
                            // Determine color scheme
                            if ($status === 'Maintenance') {
                                $classes = 'bg-slate-50 border border-slate-200 text-slate-500 hover:bg-slate-100';
                            } elseif ($status === 'Occupied') {
                                $classes = 'bg-red-50 border border-red-200 text-red-700 hover:bg-red-100';
                            } elseif ($status === 'Reserved') {
                                $classes = 'bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100';
                            } elseif ($hk === 'Dirty') {
                                $classes = 'bg-orange-50 border border-orange-200 text-orange-700 hover:bg-orange-100';
                            } else {
                                $classes = 'bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-100';
                            }
                        @endphp
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" type="button" class="w-full p-3 rounded-xl flex flex-col items-center justify-center transition-all duration-150 cursor-pointer shadow-sm text-center {{ $classes }}">
                                <span class="font-bold text-sm tracking-tight">{{ $room->room_number }}</span>
                                <span class="text-[9px] opacity-80 mt-0.5 uppercase font-medium">{{ $room->roomType->name }}</span>
                                
                                {{-- Status micro-indicators --}}
                                <div class="flex gap-1 mt-1">
                                    @if($hk === 'Dirty')
                                        <i class="fas fa-broom text-[9px] text-orange-600" title="Dirty"></i>
                                    @endif
                                    @if($room->activeMaintenanceTickets->count() > 0)
                                        <i class="fas fa-tools text-[9px] text-red-600" title="Maintenance Open"></i>
                                    @endif
                                </div>
                            </button>
                            
                            {{-- Click popover details --}}
                            <div x-show="open" @click.outside="open = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 z-30 bg-white border border-slate-200 rounded-lg shadow-xl p-3 w-52 text-left"
                                 style="display:none;">
                                 <div class="flex items-center justify-between border-b border-gray-100 pb-1.5 mb-1.5">
                                     <span class="font-bold text-gray-900 text-sm">Room {{ $room->room_number }}</span>
                                     <span class="text-[10px] font-semibold text-slate-500 uppercase">{{ $room->roomType->name }}</span>
                                 </div>
                                 <div class="space-y-1 text-xs">
                                     <div class="flex justify-between"><span class="text-gray-500">Price:</span> <span class="font-semibold text-gray-900">${{ number_format($room->price, 2) }}</span></div>
                                     <div class="flex justify-between">
                                         <span class="text-gray-500">Status:</span> 
                                         <span class="font-semibold text-gray-800">{{ $status }}</span>
                                     </div>
                                     <div class="flex justify-between">
                                         <span class="text-gray-500">Housekeeping:</span>
                                         <span class="font-semibold @if($hk=='Clean') text-emerald-600 @elseif($hk=='Dirty') text-orange-600 @else text-amber-600 @endif">{{ $hk }}</span>
                                     </div>
                                     @if($room->activeMaintenanceTickets->count() > 0)
                                     <div class="flex justify-between text-red-600 border-t border-dashed border-gray-100 pt-1 mt-1">
                                         <span>Tickets:</span>
                                         <span class="font-bold">{{ $room->activeMaintenanceTickets->count() }} Open</span>
                                     </div>
                                     @endif
                                 </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Reservations --}}
    <div class="pms-card">
        <div class="pms-card-header">
            <h3 class="text-sm font-semibold text-gray-800">Recent Reservations</h3>
            <a href="{{ route('reservations.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReservations as $res)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-indigo-600">{{ strtoupper(substr($res->guest->name ?? 'G', 0, 1)) }}</span>
                                </div>
                                <span class="font-medium text-gray-800">{{ $res->guest->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="text-gray-600">{{ $res->rooms->pluck('room_number')->implode(', ') ?: 'N/A' }}</td>
                        <td class="text-gray-600">{{ \Carbon\Carbon::parse($res->check_in_date)->format('d M Y') }}</td>
                        <td class="text-gray-600">{{ \Carbon\Carbon::parse($res->check_out_date)->format('d M Y') }}</td>
                        <td>
                            @php $s = $res->status; @endphp
                            <span class="@if($s=='Confirmed') badge-confirmed @elseif($s=='Checked-In') badge-checkedin @elseif($s=='Checked-Out') badge-checkedout @elseif($s=='Cancelled') badge-cancelled @else badge-reserved @endif">
                                {{ $s }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-gray-400 py-8">No reservations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
