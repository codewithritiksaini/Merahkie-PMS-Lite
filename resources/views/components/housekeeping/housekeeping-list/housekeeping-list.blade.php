<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Housekeeping</h1>
            <p class="text-sm text-gray-500 mt-0.5">Room cleaning status and assignments</p>
        </div>
        @if(Auth::user()->hasRole('admin'))
        <button wire:click="openCreate" class="btn-primary">
            <i class="fas fa-plus"></i> Add Record
        </button>
        @endif
    </div>

    @php
        $statusConfig = [
            'Clean' => ['key' => 'clean', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'icon' => 'fa-check-circle', 'border' => 'border-emerald-100'],
            'Dirty' => ['key' => 'dirty', 'bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'icon' => 'fa-times-circle', 'border' => 'border-rose-100'],
            'Inspecting' => ['key' => 'inspecting', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'icon' => 'fa-search', 'border' => 'border-amber-100'],
            'Maintenance' => ['key' => 'maintenance', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'icon' => 'fa-tools', 'border' => 'border-slate-200'],
        ];
    @endphp

    {{-- Status Overview Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @foreach($statusConfig as $label => $config)
        <button wire:click="$set('statusFilter', '{{ $statusFilter === $label ? '' : $label }}')"
                class="pms-card p-5 text-left hover:shadow-md transition-all duration-200 cursor-pointer border border-transparent {{ $statusFilter === $label ? 'ring-2 ring-indigo-600 border-indigo-100 shadow-md bg-indigo-50/10' : 'hover:border-slate-200' }}">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl {{ $config['bg'] }} flex items-center justify-center shrink-0">
                    <i class="fas {{ $config['icon'] }} {{ $config['text'] }} text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 tracking-tight">{{ $counts[$config['key']] }}</p>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">{{ $label }}</p>
                </div>
            </div>
        </button>
        @endforeach
    </div>

    <div class="pms-card">
        <div class="pms-card-header flex-wrap gap-3">
            <div class="flex items-center gap-2 flex-1">
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search by room..." class="pms-input pl-9 py-1.5 text-sm">
                </div>
                <select wire:model.live="statusFilter" class="pms-select py-1.5 text-sm max-w-xs">
                    <option value="">All Statuses</option>
                    <option>Clean</option>
                    <option>Dirty</option>
                    <option>Inspecting</option>
                    <option>Maintenance</option>
                </select>
            </div>
            @if($statusFilter)
            <button wire:click="$set('statusFilter', '')" class="text-xs text-indigo-600 hover:underline">
                Clear filter
            </button>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Status</th>
                        <th>Updated By</th>
                        <th>Notes</th>
                        <th>Last Updated</th>
                        @if(Auth::user()->hasRole('admin'))<th>Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                    <tr>
                        <td class="font-semibold text-gray-800">{{ $rec->room->room_number ?? 'N/A' }}</td>
                        <td>
                            @php 
                                $s = $rec->status; 
                                $badgeClass = match($s) {
                                    'Clean' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Dirty' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    'Inspecting' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'Maintenance' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-100',
                                };
                                $badgeIcon = match($s) {
                                    'Clean' => 'fa-check-circle',
                                    'Dirty' => 'fa-exclamation-circle',
                                    'Inspecting' => 'fa-hourglass-half',
                                    'Maintenance' => 'fa-tools',
                                    default => 'fa-info-circle',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                <i class="fas {{ $badgeIcon }} text-[10px]"></i>
                                {{ $s }}
                            </span>
                        </td>
                        <td class="text-gray-600">{{ $rec->updater->name ?? '—' }}</td>
                        <td class="text-gray-500 max-w-[200px] truncate">{{ $rec->notes ?? '—' }}</td>
                        <td class="text-gray-500 text-xs">{{ $rec->updated_at->format('d M Y, h:i A') }}</td>
                        @if(Auth::user()->hasRole('admin'))
                        <td>
                            <div class="flex items-center gap-1">
                                <button wire:click="edit({{ $rec->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button wire:click="delete({{ $rec->id }})" wire:confirm="Delete this record?"
                                        class="btn-icon text-red-500 hover:bg-red-50">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <i class="fas fa-broom text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-400 text-sm">No records found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $records->links() }}</div>
        @endif
    </div>

    {{-- Drawer --}}
    @if(Auth::user()->hasRole('admin'))
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
            <h3 class="text-base font-semibold text-gray-900">{{ $isEditMode ? 'Edit Status' : 'New Housekeeping Record' }}</h3>
            <button @click="$wire.showDrawer = false" class="btn-icon"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-body space-y-4">
            <div>
                <label class="pms-label">Room <span class="text-red-500">*</span></label>
                <select wire:model="room_id" class="pms-select">
                    <option value="">Select room...</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->room_number }}</option>
                    @endforeach
                </select>
                @error('room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label">Status <span class="text-red-500">*</span></label>
                <select wire:model="status" class="pms-select">
                    <option value="Clean">Clean</option>
                    <option value="Dirty">Dirty</option>
                    <option value="Inspecting">Inspecting</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
            <div>
                <label class="pms-label">Notes</label>
                <textarea wire:model="notes" rows="3" class="pms-input resize-none" placeholder="Any notes..."></textarea>
            </div>
        </div>
        <div class="drawer-footer">
            <button @click="$wire.showDrawer = false" class="btn-secondary">Cancel</button>
            <button wire:click="store" wire:loading.attr="disabled" class="btn-primary">
                <span wire:loading wire:target="store"><i class="fas fa-spinner fa-spin"></i></span>
                {{ $isEditMode ? 'Update' : 'Save Record' }}
            </button>
        </div>
    </div>
    @endif
</div>
