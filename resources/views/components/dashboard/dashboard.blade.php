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
                        <td class="text-gray-600">{{ $res->room->room_number ?? 'N/A' }}</td>
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
