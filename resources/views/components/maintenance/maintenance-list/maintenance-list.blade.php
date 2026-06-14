
<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Maintenance</h1>
            <p class="text-sm text-gray-500 mt-0.5">Room issue tracking and ticket management</p>
        </div>
        <button wire:click="openCreate" class="btn-primary">
            <i class="fas fa-plus"></i> New Ticket
        </button>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon bg-blue-100 text-blue-600"><i class="fas fa-folder-open text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $counts['open'] }}</p><p class="text-xs text-gray-500">Open</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-amber-100 text-amber-600"><i class="fas fa-spinner text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $counts['inprogress'] }}</p><p class="text-xs text-gray-500">In Progress</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-emerald-100 text-emerald-600"><i class="fas fa-check-circle text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $counts['completed'] }}</p><p class="text-xs text-gray-500">Completed</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-red-100 text-red-600"><i class="fas fa-exclamation-circle text-xl"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $counts['critical'] }}</p><p class="text-xs text-gray-500">Critical</p></div>
        </div>
    </div>

    <div class="pms-card">
        <div class="pms-card-header flex-wrap gap-3">
            <div class="relative max-w-xs w-full">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search tickets..." class="pms-input pl-9 py-1.5 text-sm">
            </div>
            <div class="flex gap-2">
                <select wire:model.live="priorityFilter" class="pms-select py-1.5 text-sm w-32">
                    <option value="">Priority</option>
                    <option>Low</option><option>Medium</option><option>High</option><option>Critical</option>
                </select>
                <select wire:model.live="statusFilter" class="pms-select py-1.5 text-sm w-36">
                    <option value="">Status</option>
                    <option>Open</option><option>In Progress</option><option>Completed</option><option>Cancelled</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th>#</th><th>Room</th><th>Issue</th><th>Priority</th><th>Assigned To</th><th>Status</th><th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $t)
                    <tr>
                        <td class="text-gray-400 text-xs">#{{ $t->id }}</td>
                        <td class="font-semibold text-gray-800">{{ $t->room_number }}</td>
                        <td class="max-w-[200px]">
                            <p class="text-gray-800 truncate">{{ $t->issue }}</p>
                            @if($t->notes) <p class="text-gray-400 text-xs truncate">{{ $t->notes }}</p> @endif
                        </td>
                        <td>
                            @php $p = $t->priority; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($p=='Critical') bg-red-100 text-red-700
                                @elseif($p=='High') bg-orange-100 text-orange-700
                                @elseif($p=='Medium') bg-amber-100 text-amber-700
                                @else bg-slate-100 text-slate-600
                                @endif">
                                @if($p=='Critical')<i class="fas fa-fire mr-1"></i>@endif
                                {{ $p }}
                            </span>
                        </td>
                        <td class="text-gray-600">{{ $t->assignee_name ?? '—' }}</td>
                        <td>
                            @php $s = $t->status; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($s=='Open') bg-blue-100 text-blue-700
                                @elseif($s=='In Progress') bg-amber-100 text-amber-700
                                @elseif($s=='Completed') bg-emerald-100 text-emerald-700
                                @else bg-slate-100 text-slate-500
                                @endif">{{ $s }}</span>
                        </td>
                        <td class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y') }}</td>
                        <td>
                            <div class="flex items-center gap-1">
                                <button wire:click="edit({{ $t->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50"><i class="fas fa-edit text-sm"></i></button>
                                <button wire:click="delete({{ $t->id }})" wire:confirm="Delete this ticket?" class="btn-icon text-red-500 hover:bg-red-50"><i class="fas fa-trash text-sm"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <i class="fas fa-tools text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-400 text-sm">No maintenance tickets found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())<div class="px-5 py-4 border-t border-gray-100">{{ $tickets->links() }}</div>@endif
    </div>

    {{-- Drawer --}}
    <div x-show="$wire.showDrawer" class="drawer-overlay" @click="$wire.showDrawer = false"
         x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="display:none"></div>
    <div x-show="$wire.showDrawer" class="drawer-panel"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         style="display:none">
        <div class="drawer-header">
            <h3 class="text-base font-semibold text-gray-900">{{ $isEditMode ? 'Edit Ticket' : 'New Maintenance Ticket' }}</h3>
            <button @click="$wire.showDrawer = false" class="btn-icon"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-body space-y-4">
            <div>
                <label class="pms-label">Room <span class="text-red-500">*</span></label>
                <select wire:model="room_id" class="pms-select">
                    <option value="">Select room...</option>
                    @foreach($rooms as $room)<option value="{{ $room->id }}">{{ $room->room_number }}</option>@endforeach
                </select>
                @error('room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label">Issue Description <span class="text-red-500">*</span></label>
                <textarea wire:model="issue" rows="3" class="pms-input resize-none" placeholder="Describe the issue..."></textarea>
                @error('issue') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="pms-label">Priority</label>
                    <select wire:model="priority" class="pms-select">
                        <option>Low</option><option>Medium</option><option>High</option><option>Critical</option>
                    </select>
                </div>
                <div>
                    <label class="pms-label">Status</label>
                    <select wire:model="status" class="pms-select">
                        <option>Open</option><option>In Progress</option><option>Completed</option><option>Cancelled</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="pms-label">Assign To</label>
                <select wire:model="assigned_to" class="pms-select">
                    <option value="">Unassigned</option>
                    @foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="pms-label">Notes</label>
                <textarea wire:model="notes" rows="2" class="pms-input resize-none" placeholder="Additional notes..."></textarea>
            </div>
        </div>
        <div class="drawer-footer">
            <button @click="$wire.showDrawer = false" class="btn-secondary">Cancel</button>
            <button wire:click="store" wire:loading.attr="disabled" class="btn-primary">
                <span wire:loading wire:target="store"><i class="fas fa-spinner fa-spin"></i></span>
                {{ $isEditMode ? 'Update Ticket' : 'Create Ticket' }}
            </button>
        </div>
    </div>
</div>