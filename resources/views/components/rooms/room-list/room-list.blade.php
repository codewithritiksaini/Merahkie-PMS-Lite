<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Rooms</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage hotel rooms and their status</p>
        </div>
        <button wire:click="openCreate" class="btn-primary">
            <i class="fas fa-plus"></i> Add Room
        </button>
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
                                <button wire:click="edit({{ $room->id }})"
                                        class="btn-icon text-indigo-500 hover:bg-indigo-50" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
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

    {{-- ===== SLIDE-OVER DRAWER ===== --}}
    <div x-show="$wire.showDrawer" class="drawer-overlay" @click="$wire.showDrawer = false"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="display:none"></div>

    <div x-show="$wire.showDrawer" class="drawer-panel"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         style="display:none">
        <div class="drawer-header">
            <h3 class="text-base font-semibold text-gray-900">
                {{ $isEditMode ? 'Edit Room' : 'Add New Room' }}
            </h3>
            <button @click="$wire.showDrawer = false" class="btn-icon">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="drawer-body space-y-4">
            <div>
                <label class="pms-label">Room Number <span class="text-red-500">*</span></label>
                <input type="text" wire:model="room_number" class="pms-input" placeholder="e.g. 101">
                @error('room_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label">Room Type <span class="text-red-500">*</span></label>
                <select wire:model="room_type_id" class="pms-select">
                    <option value="">Select type...</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('room_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label">Price per Night ($) <span class="text-red-500">*</span></label>
                <input type="number" wire:model="price" class="pms-input" placeholder="0.00" min="0" step="0.01">
                @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label">Status <span class="text-red-500">*</span></label>
                <select wire:model="status" class="pms-select">
                    <option value="Available">Available</option>
                    <option value="Occupied">Occupied</option>
                    <option value="Reserved">Reserved</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="drawer-footer">
            <button @click="$wire.showDrawer = false" class="btn-secondary">Cancel</button>
            <button wire:click="store" wire:loading.attr="disabled" class="btn-primary">
                <span wire:loading wire:target="store"><i class="fas fa-spinner fa-spin"></i></span>
                {{ $isEditMode ? 'Update Room' : 'Create Room' }}
            </button>
        </div>
    </div>
</div>
