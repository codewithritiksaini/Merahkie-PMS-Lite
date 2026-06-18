<div>
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('rooms.index') }}" class="btn-icon text-gray-500 hover:bg-gray-100">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Room</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update room {{ $room->room_number }}</p>
        </div>
    </div>

    <div class="pms-card p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="pms-label">Room Number <span class="text-red-500">*</span></label>
                <input type="text" wire:model="room_number" class="pms-input" placeholder="e.g. 101">
                @error('room_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <div class="flex items-center justify-between">
                    <label class="pms-label">Room Type <span class="text-red-500">*</span></label>
                    <a href="{{ route('rooms.types') }}" class="text-xs text-indigo-600 hover:underline mb-1.5">
                        <i class="fas fa-cog"></i> Manage Types
                    </a>
                </div>
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
        <div class="flex justify-end gap-3 pt-5">
            <a href="{{ route('rooms.index') }}" class="btn-secondary">Cancel</a>
            <button wire:click="save" wire:loading.attr="disabled" class="btn-primary">
                <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin"></i></span>
                Update Room
            </button>
        </div>
    </div>
</div>
