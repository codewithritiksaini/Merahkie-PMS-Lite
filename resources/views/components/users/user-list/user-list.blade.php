
<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Users</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage staff accounts and roles</p>
        </div>
        <button wire:click="openCreate" class="btn-primary">
            <i class="fas fa-user-plus"></i> Add User
        </button>
    </div>

    <div class="pms-card">
        <div class="pms-card-header">
            <div class="relative max-w-xs w-full">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search users..." class="pms-input pl-9 py-1.5 text-sm">
            </div>
            <span class="text-xs text-gray-400">{{ $users->total() }} users</span>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr><th>User</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center shrink-0">
                                    <span class="text-white text-sm font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->role)
                            <span class="badge-confirmed">{{ ucfirst($user->role->name) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ ($user->status ?? 'active') === 'active' ? 'badge-available' : 'badge-occupied' }}">
                                {{ ucfirst($user->status ?? 'active') }}
                            </span>
                        </td>
                        <td class="text-gray-400 text-xs">{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never' }}</td>
                        <td>
                            <div class="flex items-center gap-1">
                                <button wire:click="edit({{ $user->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50"><i class="fas fa-edit text-sm"></i></button>
                                <button wire:click="toggleStatus({{ $user->id }})" class="btn-icon text-amber-500 hover:bg-amber-50" title="Toggle Status"><i class="fas fa-power-off text-sm"></i></button>
                                <button wire:click="delete({{ $user->id }})" wire:confirm="Delete user {{ $user->name }}?" class="btn-icon text-red-500 hover:bg-red-50"><i class="fas fa-trash text-sm"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-12 text-center"><p class="text-gray-400 text-sm">No users found.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())<div class="px-5 py-4 border-t border-gray-100">{{ $users->links() }}</div>@endif
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
            <h3 class="text-base font-semibold text-gray-900">{{ $isEditMode ? 'Edit User' : 'Add New User' }}</h3>
            <button @click="$wire.showDrawer = false" class="btn-icon"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-body space-y-4">
            <div>
                <label class="pms-label">Full Name <span class="text-red-500">*</span></label>
                <input type="text" wire:model="name" class="pms-input" placeholder="John Doe">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label">Email <span class="text-red-500">*</span></label>
                <input type="email" wire:model="email" class="pms-input" placeholder="john@hotel.com">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label">Password {{ $isEditMode ? '(leave blank to keep current)' : '' }} <span class="text-red-500">*</span></label>
                <input type="password" wire:model="password" class="pms-input" placeholder="Min. 6 characters">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label">Role <span class="text-red-500">*</span></label>
                <select wire:model="role_id" class="pms-select">
                    <option value="">Select role...</option>
                    @foreach($roles as $role)<option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>@endforeach
                </select>
                @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label">Status</label>
                <select wire:model="status" class="pms-select">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="drawer-footer">
            <button @click="$wire.showDrawer = false" class="btn-secondary">Cancel</button>
            <button wire:click="store" wire:loading.attr="disabled" class="btn-primary">
                <span wire:loading wire:target="store"><i class="fas fa-spinner fa-spin"></i></span>
                {{ $isEditMode ? 'Update User' : 'Create User' }}
            </button>
        </div>
    </div>
</div>