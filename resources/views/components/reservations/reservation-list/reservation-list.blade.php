<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Reservations</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage bookings and guest stays</p>
        </div>
        <a href="{{ route('reservations.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> New Reservation
        </a>
    </div>

    <div class="pms-card">
        <div class="pms-card-header">
            <div class="relative max-w-xs w-full">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search by guest or room..."
                       class="pms-input pl-9 py-1.5 text-sm">
            </div>
            <span class="text-xs text-gray-400">{{ $reservations->total() }} reservations</span>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Guest</th>
                        <th>Room(s)</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $res)
                    <tr>
                        <td class="text-gray-400 text-xs">#{{ $res->id }}</td>
                        <td>
                            <div class="flex items-center gap-2">
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
                            @php $balance = $res->balance_due; @endphp
                            <span class="text-xs font-semibold {{ $balance > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                {{ $balance > 0 ? '$' . number_format($balance, 2) . ' due' : 'Paid' }}
                            </span>
                        </td>
                        <td>
                            @php $s = $res->status; @endphp
                            <span class="@if($s=='Confirmed') badge-confirmed @elseif($s=='Checked-In') badge-checkedin @elseif($s=='Checked-Out') badge-checkedout @elseif($s=='Cancelled') badge-cancelled @else badge-reserved @endif">
                                {{ $s }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-1 flex-wrap">
                                @if($res->status == 'Confirmed' || $res->status == 'Reserved')
                                <button wire:click="checkIn({{ $res->id }})" wire:confirm="Check-In this guest?"
                                        class="btn-success btn-sm">
                                    <i class="fas fa-sign-in-alt"></i> Check-In
                                </button>
                                @elseif($res->status == 'Checked-In')
                                <button wire:click="checkOut({{ $res->id }})" wire:confirm="Check-Out this guest?"
                                        class="btn-warning btn-sm">
                                    <i class="fas fa-sign-out-alt"></i> Check-Out
                                </button>
                                @elseif($res->status == 'Checked-Out' && optional(optional($res->checkOut)->invoice)->id)
                                <a href="{{ route('invoice.download', $res->checkOut->invoice->id) }}"
                                   target="_blank" class="btn-secondary btn-sm">
                                    <i class="fas fa-file-pdf"></i> Invoice
                                </a>
                                @endif
                                <a href="{{ route('reservations.edit', $res->id) }}" class="btn-icon text-indigo-500 hover:bg-indigo-50" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <button wire:click="delete({{ $res->id }})" wire:confirm="Delete this reservation?"
                                        class="btn-icon text-red-500 hover:bg-red-50" title="Delete">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <i class="fas fa-calendar-times text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-400 text-sm">No reservations found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reservations->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $reservations->links() }}</div>
        @endif
    </div>
</div>
