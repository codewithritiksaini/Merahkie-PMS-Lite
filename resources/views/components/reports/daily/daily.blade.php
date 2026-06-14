
<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Daily Operations Report</h1>
            <p class="text-sm text-gray-500 mt-0.5">Summary for selected date</p>
        </div>
        <div class="flex items-center gap-2">
            <input type="date" wire:model.live="date" class="pms-input py-1.5 text-sm w-44">
            <button wire:click="$set('date', '{{ now()->toDateString() }}')" class="btn-secondary btn-sm">
                <i class="fas fa-calendar-day"></i> Today
            </button>
        </div>
    </div>

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon bg-slate-100 text-slate-600"><i class="fas fa-bed text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $totalRooms }}</p><p class="text-xs text-gray-500">Total Rooms</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-red-100 text-red-600"><i class="fas fa-door-closed text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $occupiedRooms }}</p><p class="text-xs text-gray-500">Occupied</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-emerald-100 text-emerald-600"><i class="fas fa-door-open text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $availableRooms }}</p><p class="text-xs text-gray-500">Available</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-blue-100 text-blue-600"><i class="fas fa-sign-in-alt text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $checkInsToday }}</p><p class="text-xs text-gray-500">Check-Ins</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-orange-100 text-orange-600"><i class="fas fa-sign-out-alt text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $checkOutsToday }}</p><p class="text-xs text-gray-500">Check-Outs</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 text-indigo-600"><i class="fas fa-dollar-sign text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">${{ number_format($revenueToday, 0) }}</p><p class="text-xs text-gray-500">Revenue</p></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        {{-- Occupancy rate --}}
        <div class="pms-card p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Occupancy Rate</h3>
            <div class="flex items-end gap-3 mb-3">
                <span class="text-4xl font-bold text-indigo-600">{{ $occupancyRate }}%</span>
                <span class="text-sm text-gray-400 mb-1">of {{ $totalRooms }} rooms</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="bg-indigo-600 h-3 rounded-full" style="width: {{ $occupancyRate }}%"></div>
            </div>
            <div class="mt-3 flex justify-between text-xs text-gray-400">
                <span>{{ $occupiedRooms }} occupied</span>
                <span>{{ $availableRooms }} available</span>
            </div>
        </div>

        {{-- Housekeeping --}}
        <div class="pms-card p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Housekeeping Status</h3>
            @if($housekeepingPending > 0)
            <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-xl border border-amber-200">
                <i class="fas fa-broom text-amber-500 text-xl"></i>
                <div>
                    <p class="font-semibold text-amber-800">{{ $housekeepingPending }} rooms pending</p>
                    <p class="text-xs text-amber-600">Need cleaning or inspection</p>
                </div>
            </div>
            @else
            <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                <div>
                    <p class="font-semibold text-emerald-800">All rooms clean</p>
                    <p class="text-xs text-emerald-600">No pending tasks</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Revenue summary --}}
        <div class="pms-card p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Revenue Summary</h3>
            <div class="text-center">
                <p class="text-4xl font-bold text-emerald-600">${{ number_format($revenueToday, 2) }}</p>
                <p class="text-sm text-gray-400 mt-1">Total for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">from {{ $checkOutsToday }} check-out(s)</p>
            </div>
        </div>
    </div>

    {{-- Recent Check-Outs table --}}
    <div class="pms-card">
        <div class="pms-card-header">
            <h3 class="text-sm font-semibold text-gray-800">Check-Outs on {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr><th>Guest</th><th>Room</th><th>Duration</th><th>Amount</th><th>Invoice</th></tr>
                </thead>
                <tbody>
                    @forelse($recentCheckOuts as $co)
                    @php $res = optional($co->reservation); @endphp
                    <tr>
                        <td class="font-medium text-gray-800">{{ optional($res->guest)->name ?? '—' }}</td>
                        <td class="text-gray-600">{{ optional($res->room)->room_number ?? '—' }}</td>
                        <td class="text-gray-600">
                            {{ $res->check_in_date && $res->check_out_date
                                ? \Carbon\Carbon::parse($res->check_in_date)->diffInDays(\Carbon\Carbon::parse($res->check_out_date)) . ' nights'
                                : '—' }}
                        </td>
                        <td class="font-semibold text-gray-900">${{ number_format($co->total_amount, 2) }}</td>
                        <td>
                            @if($co->invoice)
                            <a href="{{ route('invoice.download', $co->invoice->id) }}" target="_blank"
                               class="btn-secondary btn-sm">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                            @else <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-10 text-center text-gray-400 text-sm">No check-outs for this date.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>