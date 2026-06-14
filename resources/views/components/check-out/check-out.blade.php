
<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Check-Out</h1>
            <p class="text-sm text-gray-500 mt-0.5">Process guest departures</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon bg-orange-100 text-orange-600"><i class="fas fa-calendar-day text-xl"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $checkoutsToday }}</p>
                <p class="text-xs text-gray-500">Due Today</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-red-100 text-red-600"><i class="fas fa-exclamation-triangle text-xl"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $overdueCount }}</p>
                <p class="text-xs text-gray-500">Overdue</p>
            </div>
        </div>
    </div>

    <div class="pms-card">
        <div class="pms-card-header">
            <h3 class="text-sm font-semibold text-gray-800">Currently Checked-In Guests</h3>
            <div class="relative max-w-xs">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search guest..." class="pms-input pl-9 py-1.5 text-sm">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th>Guest</th><th>Room</th><th>Check-In</th><th>Check-Out Due</th><th>Nights</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($checkedIn as $res)
                    @php
                        $nights = \Carbon\Carbon::parse($res->check_in_date)->diffInDays(\Carbon\Carbon::parse($res->check_out_date));
                        $isOverdue = \Carbon\Carbon::parse($res->check_out_date)->isPast();
                        $isDueToday = \Carbon\Carbon::parse($res->check_out_date)->isToday();
                    @endphp
                    <tr class="{{ $isOverdue ? 'bg-red-50/50' : ($isDueToday ? 'bg-amber-50/50' : '') }}">
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-emerald-600">{{ strtoupper(substr($res->guest->name ?? 'G', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $res->guest->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400">{{ $res->guest->phone ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="font-semibold text-gray-800">{{ $res->room->room_number ?? 'N/A' }}</span></td>
                        <td class="text-gray-600">{{ \Carbon\Carbon::parse($res->check_in_date)->format('d M Y') }}</td>
                        <td>
                            <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : ($isDueToday ? 'text-amber-600 font-semibold' : 'text-gray-600') }}">
                                {{ \Carbon\Carbon::parse($res->check_out_date)->format('d M Y') }}
                            </span>
                            @if($isOverdue) <span class="badge-occupied ml-1">Overdue</span>
                            @elseif($isDueToday) <span class="badge-maintenance ml-1">Due Today</span>
                            @endif
                        </td>
                        <td class="text-gray-600">{{ $nights }} nights</td>
                        <td>
                            <button wire:click="checkOut({{ $res->id }})"
                                    wire:confirm="Check out {{ $res->guest->name ?? 'guest' }}? This will generate an invoice."
                                    wire:loading.attr="disabled"
                                    class="btn-warning btn-sm">
                                <i class="fas fa-sign-out-alt"></i> Check Out
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <i class="fas fa-hotel text-5xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-500 font-medium">No guests currently checked in</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($checkedIn->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $checkedIn->links() }}</div>
        @endif
    </div>
</div>