
<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Occupancy Report</h1>
            <p class="text-sm text-gray-500 mt-0.5">Monthly occupancy trends</p>
        </div>
        <input type="month" wire:model.live="month" class="pms-input py-1.5 text-sm w-40">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon bg-slate-100 text-slate-600"><i class="fas fa-bed text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $totalRooms }}</p><p class="text-xs text-gray-500">Total Rooms</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-red-100 text-red-600"><i class="fas fa-door-closed text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $occupiedRooms }}</p><p class="text-xs text-gray-500">Currently Occupied</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 text-indigo-600"><i class="fas fa-percent text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $occupancyRate }}%</p><p class="text-xs text-gray-500">Occupancy Rate</p></div>
        </div>
    </div>

    {{-- Daily occupancy chart (visual bar) --}}
    <div class="pms-card p-5 mb-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Daily Occupancy — {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h3>
        <div class="flex items-end gap-1 h-32">
            @foreach($dailyData as $d)
            <div class="flex flex-col items-center flex-1 gap-1">
                <div class="w-full rounded-t-sm bg-indigo-500 transition-all duration-300 min-h-[2px]"
                     style="height: {{ max(2, $d['rate']) }}%"
                     title="Day {{ $d['date'] }}: {{ $d['count'] }} rooms ({{ $d['rate'] }}%)"></div>
                @if(count($dailyData) <= 31)
                <span class="text-gray-300 text-[9px] leading-none">{{ $d['date'] }}</span>
                @endif
            </div>
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-3">
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 bg-indigo-500 rounded-sm"></div><span class="text-xs text-gray-500">Occupied rooms</span></div>
        </div>
    </div>

    {{-- Room type breakdown --}}
    <div class="pms-card">
        <div class="pms-card-header"><h3 class="text-sm font-semibold text-gray-800">Current Status by Room Type</h3></div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead><tr><th>Room Type</th><th>Available</th><th>Occupied</th><th>Reserved</th><th>Maintenance</th></tr></thead>
                <tbody>
                    @forelse($roomTypeStats as $typeId => $group)
                    @php
                        $typeName = optional($group->first()->roomType)->name ?? 'Unknown';
                        $byStatus = $group->pluck('count','status');
                    @endphp
                    <tr>
                        <td class="font-medium text-gray-800">{{ $typeName }}</td>
                        <td><span class="badge-available">{{ $byStatus['Available'] ?? 0 }}</span></td>
                        <td><span class="badge-occupied">{{ $byStatus['Occupied'] ?? 0 }}</span></td>
                        <td><span class="badge-reserved">{{ $byStatus['Reserved'] ?? 0 }}</span></td>
                        <td><span class="badge-maintenance">{{ $byStatus['Maintenance'] ?? 0 }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-8 text-center text-gray-400 text-sm">No data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>