
<div>
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Settings</h1>
        <p class="text-sm text-gray-500 mt-0.5">Configure hotel system preferences</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Tab sidebar --}}
        <div class="lg:w-52 shrink-0">
            <div class="pms-card p-2">
                @foreach([['hotel','fas fa-hotel','Hotel Info'],['preferences','fas fa-sliders-h','Preferences'],['notifications','fas fa-bell','Notifications'],['invoice','fas fa-file-invoice','Invoice']] as [$tab,$icon,$label])
                <button wire:click="setTab('{{ $tab }}')"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ $activeTab === $tab ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="{{ $icon }} w-4 text-center"></i>
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Tab content --}}
        <div class="flex-1">
            @if($activeTab === 'hotel')
            <div class="pms-card p-6 space-y-5">
                <h2 class="text-base font-semibold text-gray-900 mb-1">Hotel Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><label class="pms-label">Hotel Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="hotel_name" class="pms-input" placeholder="Grand Hotel">
                        @error('hotel_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div><label class="pms-label">Phone</label>
                        <input type="text" wire:model="hotel_phone" class="pms-input" placeholder="+1 234 567 8900">
                    </div>
                    <div><label class="pms-label">Email</label>
                        <input type="email" wire:model="hotel_email" class="pms-input" placeholder="info@hotel.com">
                    </div>
                    <div><label class="pms-label">Website</label>
                        <input type="url" wire:model="hotel_website" class="pms-input" placeholder="https://hotel.com">
                    </div>
                    <div class="col-span-2"><label class="pms-label">Address</label>
                        <textarea wire:model="hotel_address" rows="2" class="pms-input resize-none" placeholder="Full address..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button wire:click="saveHotel" class="btn-primary">
                        <i class="fas fa-save"></i> Save Hotel Info
                    </button>
                </div>
            </div>

            @elseif($activeTab === 'preferences')
            <div class="pms-card p-6 space-y-5">
                <h2 class="text-base font-semibold text-gray-900 mb-1">Preferences</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><label class="pms-label">Currency</label>
                        <select wire:model="currency" class="pms-select">
                            <option>USD</option><option>EUR</option><option>GBP</option><option>INR</option>
                        </select>
                    </div>
                    <div><label class="pms-label">Date Format</label>
                        <select wire:model="date_format" class="pms-select">
                            <option value="d M Y">25 Jan 2026</option>
                            <option value="Y-m-d">2026-01-25</option>
                            <option value="m/d/Y">01/25/2026</option>
                        </select>
                    </div>
                    <div><label class="pms-label">Default Check-In Time</label>
                        <input type="time" wire:model="checkin_time" class="pms-input">
                    </div>
                    <div><label class="pms-label">Default Check-Out Time</label>
                        <input type="time" wire:model="checkout_time" class="pms-input">
                    </div>
                    <div><label class="pms-label">Timezone</label>
                        <select wire:model="hotel_timezone" class="pms-select">
                            <option>UTC</option><option>Asia/Kolkata</option><option>America/New_York</option><option>Europe/London</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button wire:click="savePreferences" class="btn-primary"><i class="fas fa-save"></i> Save Preferences</button>
                </div>
            </div>

            @elseif($activeTab === 'notifications')
            <div class="pms-card p-6 space-y-5">
                <h2 class="text-base font-semibold text-gray-900 mb-1">Notifications</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div>
                            <p class="font-medium text-gray-800">Email Notifications</p>
                            <p class="text-sm text-gray-500">Receive reservation alerts via email</p>
                        </div>
                        <button wire:click="$toggle('email_notifications')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $email_notifications ? 'bg-indigo-600' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $email_notifications ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div>
                            <p class="font-medium text-gray-800">SMS Notifications</p>
                            <p class="text-sm text-gray-500">Receive alerts via SMS</p>
                        </div>
                        <button wire:click="$toggle('sms_notifications')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $sms_notifications ? 'bg-indigo-600' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $sms_notifications ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button wire:click="saveNotifications" class="btn-primary"><i class="fas fa-save"></i> Save</button>
                </div>
            </div>

            @elseif($activeTab === 'invoice')
            <div class="pms-card p-6 space-y-5">
                <h2 class="text-base font-semibold text-gray-900 mb-1">Invoice Settings</h2>
                <div>
                    <label class="pms-label">Invoice Number Prefix</label>
                    <input type="text" wire:model="invoice_prefix" class="pms-input" placeholder="INV-">
                </div>
                <div>
                    <label class="pms-label">Invoice Footer Text</label>
                    <textarea wire:model="invoice_footer" rows="3" class="pms-input resize-none" placeholder="Thank you for staying with us..."></textarea>
                </div>
                <div class="flex justify-end pt-2">
                    <button wire:click="saveInvoice" class="btn-primary"><i class="fas fa-save"></i> Save Invoice Settings</button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>