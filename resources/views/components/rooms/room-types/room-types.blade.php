<div>
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('rooms.index') }}" class="btn-icon text-gray-500 hover:bg-gray-100">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Room Types</h1>
            <p class="text-sm text-gray-500 mt-0.5">Add, rename, or remove room types used across the hotel</p>
        </div>
    </div>

    <div class="pms-card p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-3">Add New Room Type</h2>
        <div class="flex items-end gap-3">
            <div class="flex-1">
                <label class="pms-label">Name <span class="text-red-500">*</span></label>
                <input type="text" wire:model="name" wire:keydown.enter="addType" class="pms-input" placeholder="e.g. Deluxe, Suite, Standard">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button wire:click="addType" wire:loading.attr="disabled" class="btn-primary shrink-0">
                <i class="fas fa-plus"></i> Add Type
            </button>
        </div>
    </div>

    <div class="pms-card">
        <div class="pms-card-header">
            <h3 class="text-sm font-semibold text-gray-800">All Room Types</h3>
            <span class="text-xs text-gray-400">{{ $roomTypes->count() }} types</span>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Rooms Using This Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roomTypes as $type)
                    <tr wire:key="type-{{ $type->id }}">
                        @if($editingId === $type->id)
                            <td colspan="2">
                                <input type="text" wire:model="editingName" wire:keydown.enter="updateType"
                                       class="pms-input py-1.5 text-sm" autofocus>
                                @error('editingName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <button wire:click="updateType" class="btn-icon text-emerald-600 hover:bg-emerald-50" title="Save">
                                        <i class="fas fa-check text-sm"></i>
                                    </button>
                                    <button wire:click="cancelEdit" class="btn-icon text-gray-500 hover:bg-gray-100" title="Cancel">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        @else
                            <td class="font-medium text-gray-800">{{ $type->name }}</td>
                            <td class="text-gray-600">{{ $type->rooms_count }}</td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <button wire:click="editType({{ $type->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50" title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button wire:click="deleteType({{ $type->id }})"
                                            wire:confirm="Delete room type \"{{ $type->name }}\"?"
                                            class="btn-icon text-red-500 hover:bg-red-50" title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-12 text-center">
                            <i class="fas fa-tags text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-gray-400 text-sm">No room types yet. Add one above.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
