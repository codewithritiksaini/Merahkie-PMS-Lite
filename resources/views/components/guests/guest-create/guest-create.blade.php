<div>
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('guests.index') }}" class="btn-icon text-gray-500 hover:bg-gray-100">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Add Guest</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create a new guest record</p>
        </div>
    </div>

    <div class="pms-card p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
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
            <div class="sm:col-span-2">
                <label class="pms-label">Address</label>
                <textarea wire:model="address" rows="2" class="pms-input resize-none" placeholder="Full address..."></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-5">
            <a href="{{ route('guests.index') }}" class="btn-secondary">Cancel</a>
            <button wire:click="save" wire:loading.attr="disabled" class="btn-primary">
                <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin"></i></span>
                Create Guest
            </button>
        </div>
    </div>
</div>
