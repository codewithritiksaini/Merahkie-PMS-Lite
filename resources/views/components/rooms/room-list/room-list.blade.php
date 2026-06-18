<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Rooms</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage hotel rooms and their status</p>
        </div>
        <a href="{{ route('rooms.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Add Room
        </a>
    </div>

    {{-- Table Card --}}
    <div class="pms-card">
        <div class="pms-card-header">
            <div class="relative max-w-xs w-full">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search rooms..."
                       class="pms-input pl-9 py-1.5 text-sm">
            </div>
            <span class="text-xs text-gray-400">{{ $rooms->total() }} rooms</span>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th>Room No.</th>
                        <th>Type</th>
                        <th>Floor</th>
                        <th>Price / Night</th>
                        <th>Status</th>
                        <th>Housekeeping</th>
                        <th>Active Tickets</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                    <tr>
                        <td><span class="font-semibold text-gray-800">{{ $room->room_number }}</span></td>
                        <td class="text-gray-600">{{ $room->roomType->name ?? '—' }}</td>
                        <td class="text-gray-600">{{ $room->floor ?? '—' }}</td>
                        <td class="font-medium text-gray-800">${{ number_format($room->price, 2) }}</td>
                        <td>
                            @php $s = $room->status; @endphp
                            <span class="@if($s=='Available') badge-available @elseif($s=='Occupied') badge-occupied @elseif($s=='Reserved') badge-reserved @else badge-maintenance @endif">
                                {{ $s }}
                            </span>
                        </td>
                        <td>
                            @php
                                $hk = optional($room->latestHousekeeping)->status ?? 'Clean';
                            @endphp
                            <span class="@if($hk=='Clean') badge-clean @elseif($hk=='Dirty') badge-dirty @elseif($hk=='Inspecting') badge-inspecting @else badge-maintenance @endif">
                                {{ $hk }}
                            </span>
                        </td>
                        <td>
                            @php
                                $ticketsCount = $room->activeMaintenanceTickets->count();
                            @endphp
                            @if($ticketsCount > 0)
                                <span class="badge-occupied inline-flex items-center gap-1">
                                    <i class="fas fa-tools text-[10px]"></i>
                                    {{ $ticketsCount }} Ticket{{ $ticketsCount > 1 ? 's' : '' }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    None
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('rooms.edit', $room->id) }}"
                                   class="btn-icon text-indigo-500 hover:bg-indigo-50" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <button wire:click="delete({{ $room->id }})"
                                        wire:confirm="Delete room {{ $room->room_number }}?"
                                        class="btn-icon text-red-500 hover:bg-red-50" title="Delete">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <i class="fas fa-bed text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-400 text-sm">No rooms found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rooms->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $rooms->links() }}
        </div>
        @endif
    </div>
</div>
