<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Guests</h1>
            <p class="text-sm text-gray-500 mt-0.5">Guest directory and profiles</p>
        </div>
        <a href="{{ route('guests.create') }}" class="btn-primary">
            <i class="fas fa-user-plus"></i> Add Guest
        </a>
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
                                <a href="{{ route('guests.edit', $guest->id) }}" class="btn-icon text-indigo-500 hover:bg-indigo-50" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
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
</div>
