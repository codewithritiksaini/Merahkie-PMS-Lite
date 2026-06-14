<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Reservations</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage bookings and guest stays</p>
        </div>
        <button wire:click="openCreate" class="btn-primary">
            <i class="fas fa-plus"></i> New Reservation
        </button>
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
                        <th>Room</th>
                        <th>Check In</th>
                        <th>Check Out</th>
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
                        <td class="text-gray-600">{{ $res->room->room_number ?? 'N/A' }}</td>
                        <td class="text-gray-600">{{ \Carbon\Carbon::parse($res->check_in_date)->format('d M Y') }}</td>
                        <td class="text-gray-600">{{ \Carbon\Carbon::parse($res->check_out_date)->format('d M Y') }}</td>
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
                                <button wire:click="edit({{ $res->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button wire:click="delete({{ $res->id }})" wire:confirm="Delete this reservation?"
                                        class="btn-icon text-red-500 hover:bg-red-50" title="Delete">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center">
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

    {{-- Drawer --}}
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
            <h3 class="text-base font-semibold text-gray-900">{{ $isEditMode ? 'Edit Reservation' : 'New Reservation' }}</h3>
            <button @click="$wire.showDrawer = false" class="btn-icon"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-body space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="pms-label">Guest <span class="text-red-500">*</span></label>
                    <select wire:model="guest_id" class="pms-select">
                        <option value="">Select guest...</option>
                        @foreach($guests as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                    @error('guest_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-2 relative" x-data="{
                    open: false,
                    search: '',
                    rooms: @js($rooms->map(fn($r) => [
                        'id' => $r->id,
                        'room_number' => $r->room_number,
                        'type' => $r->roomType->name ?? '',
                        'price' => $r->price,
                        'hk' => optional($r->latestHousekeeping)->status ?? 'Clean',
                        'maint' => $r->activeMaintenanceTickets->count()
                    ])),
                    selectedRoom: null,
                    init() {
                        if ('{{ $room_id }}') {
                            this.selectedRoom = this.rooms.find(r => r.id == '{{ $room_id }}');
                        }
                        this.$watch('$wire.room_id', value => {
                            this.selectedRoom = this.rooms.find(r => r.id == value);
                        });
                    },
                    selectRoom(room) {
                        $wire.set('room_id', room.id);
                        this.selectedRoom = room;
                        this.open = false;
                        this.search = '';
                    },
                    get filteredRooms() {
                        if (!this.search) return this.rooms;
                        return this.rooms.filter(r => 
                            r.room_number.toLowerCase().includes(this.search.toLowerCase()) ||
                            r.type.toLowerCase().includes(this.search.toLowerCase())
                        );
                    }
                }" @click.outside="open = false">
                    <label class="pms-label">Room <span class="text-red-500">*</span></label>
                    
                    {{-- Trigger button --}}
                    <button type="button" @click="open = !open" 
                            class="w-full flex items-center justify-between pms-input text-left bg-white border border-slate-300 rounded-lg shadow-sm px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 cursor-pointer">
                        <template x-if="selectedRoom">
                            <span class="flex items-center justify-between w-full">
                                <span class="font-medium" x-text="'Room ' + selectedRoom.room_number + ' (' + selectedRoom.type + ')'"></span>
                                <span class="text-xs text-gray-500 font-semibold" x-text="'$' + Number(selectedRoom.price).toFixed(2) + '/night'"></span>
                            </span>
                        </template>
                        <template x-if="!selectedRoom">
                            <span class="text-gray-400">Select room...</span>
                        </template>
                        <i class="fas fa-chevron-down text-gray-400 text-xs ml-2"></i>
                    </button>

                    {{-- Dropdown Panel --}}
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-xl max-h-60 overflow-y-auto flex flex-col"
                         style="display:none;">
                        
                        {{-- Search input inside dropdown --}}
                        <div class="p-2 border-b border-slate-100 sticky top-0 bg-white z-10">
                            <div class="relative">
                                <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input type="text" x-model="search" placeholder="Type room number or type..."
                                       class="w-full text-xs border border-slate-200 rounded px-2 py-1.5 pl-7 focus:outline-none focus:border-indigo-500">
                            </div>
                        </div>

                        {{-- Rooms List --}}
                        <div class="flex-1 overflow-y-auto">
                            <template x-for="room in filteredRooms" :key="room.id">
                                <button type="button" @click="selectRoom(room)"
                                        class="w-full text-left px-4 py-2.5 hover:bg-slate-50 border-b border-slate-50 flex items-center justify-between transition-colors cursor-pointer">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-gray-900" x-text="'Room ' + room.room_number"></span>
                                            <span class="text-xs text-gray-500" x-text="room.type"></span>
                                        </div>
                                        <div class="flex gap-2 pt-1">
                                            {{-- Housekeeping Badge --}}
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold"
                                                  :class="{
                                                      'bg-emerald-100 text-emerald-700': room.hk === 'Clean',
                                                      'bg-red-100 text-red-700': room.hk === 'Dirty',
                                                      'bg-amber-100 text-amber-700': room.hk === 'Inspecting',
                                                      'bg-orange-100 text-orange-700': room.hk === 'Maintenance'
                                                  }"
                                                  x-text="room.hk"></span>
                                            {{-- Maintenance Tickets --}}
                                            <template x-if="room.maint > 0">
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-700">
                                                    <i class="fas fa-tools text-[8px]"></i>
                                                    <span x-text="room.maint + ' Ticket' + (room.maint > 1 ? 's' : '')"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-semibold text-gray-900 text-sm" x-text="'$' + Number(room.price).toFixed(2)"></span>
                                        <span class="text-[10px] text-gray-400 block">/night</span>
                                    </div>
                                </button>
                            </template>
                            <template x-if="filteredRooms.length === 0">
                                <div class="p-4 text-center text-xs text-gray-400">No rooms found.</div>
                            </template>
                        </div>
                    </div>

                    @error('room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    @if($room_id)
                        @php
                            $selectedRoom = $rooms->firstWhere('id', $room_id);
                        @endphp
                        @if($selectedRoom)
                            @php
                                $hkStatus = optional($selectedRoom->latestHousekeeping)->status ?? 'Clean';
                                $maintCount = $selectedRoom->activeMaintenanceTickets->count();
                            @endphp
                            @if($hkStatus !== 'Clean' || $maintCount > 0)
                                <div class="mt-2.5 rounded-lg p-3 text-xs bg-amber-50 border border-amber-200 text-amber-800 space-y-1">
                                    <p class="font-semibold flex items-center gap-1.5"><i class="fas fa-exclamation-triangle"></i> Room Status Alert:</p>
                                    @if($hkStatus !== 'Clean')
                                        <p>• Housekeeping status is <strong>{{ $hkStatus }}</strong></p>
                                    @endif
                                    @if($maintCount > 0)
                                        <p>• Room has <strong>{{ $maintCount }} active maintenance ticket(s)</strong></p>
                                    @endif
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
                <div>
                    <label class="pms-label">Check-In <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="check_in_date" class="pms-input">
                    @error('check_in_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="pms-label">Check-Out <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="check_out_date" class="pms-input">
                    @error('check_out_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="pms-label">Adults</label>
                    <input type="number" wire:model="adults" class="pms-input" min="1">
                    @error('adults') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="pms-label">Children</label>
                    <input type="number" wire:model="children" class="pms-input" min="0">
                </div>
                <div class="col-span-2">
                    <label class="pms-label">Status</label>
                    <select wire:model="status" class="pms-select">
                        <option value="Confirmed">Confirmed</option>
                        <option value="Checked-In">Checked-In</option>
                        <option value="Checked-Out">Checked-Out</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="pms-label">Special Notes</label>
                    <textarea wire:model="special_notes" rows="3" class="pms-input resize-none" placeholder="Any special requests..."></textarea>
                </div>
            </div>
        </div>
        <div class="drawer-footer">
            <button @click="$wire.showDrawer = false" class="btn-secondary">Cancel</button>
            <button wire:click="store" wire:loading.attr="disabled" class="btn-primary">
                <span wire:loading wire:target="store"><i class="fas fa-spinner fa-spin"></i></span>
                {{ $isEditMode ? 'Update' : 'Create Reservation' }}
            </button>
        </div>
    </div>
</div>
