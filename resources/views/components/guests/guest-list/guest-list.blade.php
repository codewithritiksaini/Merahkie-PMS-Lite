<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Guests</h1>
            <p class="text-sm text-gray-500 mt-0.5">Guest directory and profiles</p>
        </div>
        <button wire:click="openCreate" class="btn-primary">
            <i class="fas fa-user-plus"></i> Add Guest
        </button>
    </div>

    <div class="pms-card">
        <div class="pms-card-header">
            <div class="relative max-w-xs w-full">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search guests..."
                       class="pms-input pl-9 py-1.5 text-sm">
            </div>
            <span class="text-xs text-gray-400">{{ $guests->total() }} guests</span>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th>Guest ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Nationality</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                    <tr>
                        <td><span class="text-xs font-mono text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $guest->guest_id }}</span></td>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center shrink-0">
                                    <span class="text-white text-xs font-bold">{{ strtoupper(substr($guest->name, 0, 1)) }}</span>
                                </div>
                                <span class="font-medium text-gray-800">{{ $guest->name }}</span>
                            </div>
                        </td>
                        <td class="text-gray-600">{{ $guest->email ?? '—' }}</td>
                        <td class="text-gray-600">{{ $guest->phone ?? '—' }}</td>
                        <td class="text-gray-600">{{ $guest->nationality ?? '—' }}</td>
                        <td>
                            <div class="flex items-center gap-1">
                                <button wire:click="edit({{ $guest->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button wire:click="delete({{ $guest->id }})" wire:confirm="Delete guest {{ $guest->name }}?"
                                        class="btn-icon text-red-500 hover:bg-red-50" title="Delete">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <i class="fas fa-users text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-400 text-sm">No guests found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($guests->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $guests->links() }}</div>
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
            <h3 class="text-base font-semibold text-gray-900">{{ $isEditMode ? 'Edit Guest' : 'Add New Guest' }}</h3>
            <button @click="$wire.showDrawer = false" class="btn-icon"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-body space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="pms-label">Guest ID <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="guest_id" class="pms-input" placeholder="G-00001">
                    @error('guest_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="pms-label">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="name" class="pms-input" placeholder="John Doe">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="pms-label">Email</label>
                    <input type="email" wire:model="email" class="pms-input" placeholder="john@example.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="pms-label">Phone</label>
                    <input type="text" wire:model="phone" class="pms-input" placeholder="+1 234 567 8900">
                </div>
                <div>
                    <label class="pms-label">Nationality</label>
                    <input type="text" wire:model="nationality" class="pms-input" placeholder="e.g. Indian">
                </div>
                <div>
                    <label class="pms-label">Passport No.</label>
                    <input type="text" wire:model="passport_number" class="pms-input" placeholder="A1234567">
                </div>
                <div class="col-span-2">
                    <label class="pms-label">Address</label>
                    <textarea wire:model="address" rows="2" class="pms-input resize-none" placeholder="Full address..."></textarea>
                </div>
            </div>
        </div>
        <div class="drawer-footer">
            <button @click="$wire.showDrawer = false" class="btn-secondary">Cancel</button>
            <button wire:click="store" wire:loading.attr="disabled" class="btn-primary">
                <span wire:loading wire:target="store"><i class="fas fa-spinner fa-spin"></i></span>
                {{ $isEditMode ? 'Update Guest' : 'Add Guest' }}
            </button>
        </div>
    </div>
</div>
