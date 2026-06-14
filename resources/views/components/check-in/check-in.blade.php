
<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Check-In</h1>
            <p class="text-sm text-gray-500 mt-0.5">Process guest arrivals</p>
        </div>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon bg-blue-100 text-blue-600"><i class="fas fa-calendar-day text-xl"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $todayCount }}</p>
                <p class="text-xs text-gray-500">Arrivals Today</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-amber-100 text-amber-600"><i class="fas fa-clock text-xl"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $pendingTotal }}</p>
                <p class="text-xs text-gray-500">Pending Check-Ins</p>
            </div>
        </div>
    </div>

    <div class="pms-card">
        <div class="pms-card-header">
            <h3 class="text-sm font-semibold text-gray-800">Pending Arrivals</h3>
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
                        <th>Guest</th><th>Room</th><th>Check-In Date</th><th>Check-Out</th><th>Guests</th><th>Notes</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($arrivals as $res)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-indigo-600">{{ strtoupper(substr($res->guest->name ?? 'G', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $res->guest->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400">{{ $res->guest->phone ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-semibold text-gray-800">{{ $res->room->room_number ?? 'N/A' }}</span>
                            <p class="text-xs text-gray-400">{{ $res->room->roomType->name ?? '' }}</p>
                        </td>
                        <td>
                            <span class="@if(\Carbon\Carbon::parse($res->check_in_date)->isToday()) text-emerald-600 font-semibold @elseif(\Carbon\Carbon::parse($res->check_in_date)->isPast()) text-red-500 font-semibold @else text-gray-600 @endif">
                                {{ \Carbon\Carbon::parse($res->check_in_date)->format('d M Y') }}
                            </span>
                            @if(\Carbon\Carbon::parse($res->check_in_date)->isToday())
                                <span class="badge-available ml-1">Today</span>
                            @elseif(\Carbon\Carbon::parse($res->check_in_date)->isPast())
                                <span class="badge-occupied ml-1">Overdue</span>
                            @endif
                        </td>
                        <td class="text-gray-600">{{ \Carbon\Carbon::parse($res->check_out_date)->format('d M Y') }}</td>
                        <td class="text-gray-600">{{ $res->adults }}+{{ $res->children }}</td>
                        <td class="text-gray-500 text-xs max-w-[120px] truncate">{{ $res->special_notes ?? '—' }}</td>
                        <td>
                            <button wire:click="checkIn({{ $res->id }})"
                                    wire:confirm="Check in {{ $res->guest->name ?? 'guest' }}?"
                                    wire:loading.attr="disabled"
                                    class="btn-success btn-sm">
                                <i class="fas fa-sign-in-alt"></i> Check In
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center">
                            <i class="fas fa-check-circle text-5xl text-emerald-200 mb-3 block"></i>
                            <p class="text-gray-500 font-medium">No pending check-ins</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($arrivals->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $arrivals->links() }}</div>
        @endif
    </div>
</div>