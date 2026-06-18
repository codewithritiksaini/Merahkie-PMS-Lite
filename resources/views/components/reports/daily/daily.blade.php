
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
                        <td class="text-gray-600">{{ $res->rooms ? ($res->rooms->pluck('room_number')->implode(', ') ?: '—') : '—' }}</td>
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

    {{-- Daily Cash Sheet --}}
    <div class="pms-card mt-6">
        <div class="pms-card-header">
            <h3 class="text-sm font-semibold text-gray-800">Daily Cash Sheet — {{ \Carbon\Carbon::parse($date)->format('d M Y, l') }}</h3>
            <a href="{{ route('reports.daily-cash-sheet.download', ['date' => $date]) }}" target="_blank" class="btn-secondary btn-sm">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th>Room No.</th><th>Name</th><th>Rent</th><th>Tax</th><th>Misc. Chgs</th>
                        <th>Arrv. Date</th><th>Dept. Date</th><th>Bal. Due</th><th>Paid</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cashSheet['rows'] as $row)
                    <tr class="{{ $row['name'] ? '' : 'text-gray-300' }}">
                        <td class="font-medium {{ $row['name'] ? 'text-gray-800' : 'text-gray-400' }}">{{ $row['room_number'] }}</td>
                        <td class="{{ $row['name'] ? 'text-gray-800 font-medium' : 'text-gray-300' }}">{{ $row['name'] ?? '—' }}</td>
                        <td class="text-gray-600">{{ $row['rent'] !== null ? '$' . number_format($row['rent'], 2) : '—' }}</td>
                        <td class="text-gray-600">{{ $row['tax'] !== null ? '$' . number_format($row['tax'], 2) : '—' }}</td>
                        <td class="text-gray-400">{{ $row['misc'] ?? '—' }}</td>
                        <td class="text-gray-600">{{ $row['arrival_date'] ? \Carbon\Carbon::parse($row['arrival_date'])->format('d M Y') : '—' }}</td>
                        <td class="text-gray-600">{{ $row['departure_date'] ? \Carbon\Carbon::parse($row['departure_date'])->format('d M Y') : '—' }}</td>
                        <td class="{{ ($row['balance_due'] ?? 0) > 0 ? 'text-red-600 font-semibold' : 'text-gray-600' }}">{{ $row['balance_due'] !== null ? '$' . number_format($row['balance_due'], 2) : '—' }}</td>
                        <td class="text-gray-600">{{ $row['paid'] !== null ? '$' . number_format($row['paid'], 2) : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div><span class="text-gray-500">Total Cash:</span> <span class="font-semibold text-gray-900">${{ number_format($cashSheet['totals']['cash'], 2) }}</span></div>
            <div><span class="text-gray-500">Total Card:</span> <span class="font-semibold text-gray-900">${{ number_format($cashSheet['totals']['card'], 2) }}</span></div>
            <div><span class="text-gray-500">Total UPI:</span> <span class="font-semibold text-gray-900">${{ number_format($cashSheet['totals']['upi'], 2) }}</span></div>
            <div><span class="text-gray-500">Grand Total:</span> <span class="font-semibold text-emerald-600">${{ number_format($cashSheet['totals']['grand_total'], 2) }}</span></div>
        </div>
    </div>

    {{-- Range export --}}
    <div class="pms-card mt-6 p-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Export a Date Range</h3>
        <p class="text-xs text-gray-500 mb-3">Generates one Daily Cash Sheet page per day in the selected range (PDF).</p>
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="pms-label">From</label>
                <input type="date" wire:model="rangeFrom" class="pms-input py-1.5 text-sm">
            </div>
            <div>
                <label class="pms-label">To</label>
                <input type="date" wire:model="rangeTo" class="pms-input py-1.5 text-sm">
            </div>
            <a href="{{ route('reports.daily-cash-sheet.download-range', ['from' => $rangeFrom, 'to' => $rangeTo]) }}"
               target="_blank" class="btn-primary btn-sm">
                <i class="fas fa-file-pdf"></i> Download Range PDF
            </a>
        </div>
    </div>

    {{-- Auto-email schedule --}}
    <div class="pms-card mt-6 p-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Email Automation</h3>
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl mb-4">
            <div>
                <p class="font-medium text-gray-800">Auto-send Daily Cash Sheet by Email</p>
                <p class="text-sm text-gray-500">Today's report will be emailed automatically at the time set below</p>
            </div>
            <button wire:click="$toggle('daily_report_auto_send')"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $daily_report_auto_send ? 'bg-indigo-600' : 'bg-gray-300' }}">
                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $daily_report_auto_send ? 'translate-x-6' : 'translate-x-1' }}"></span>
            </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="pms-label">Send to Email <span class="text-gray-400 font-normal">(comma-separated for multiple)</span></label>
                <input type="text" wire:model="daily_report_email" class="pms-input" placeholder="manager@hotel.com, accounts@hotel.com">
                @error('daily_report_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label">Send Time</label>
                <input type="time" wire:model="daily_report_time" class="pms-input">
                @error('daily_report_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex justify-end pt-4">
            <button wire:click="saveEmailSchedule" class="btn-primary"><i class="fas fa-save"></i> Save Email Schedule</button>
        </div>
    </div>
</div>