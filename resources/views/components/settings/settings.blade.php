
<div>
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Settings</h1>
        <p class="text-sm text-gray-500 mt-0.5">Configure hotel system preferences</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Tab sidebar --}}
        <div class="lg:w-52 shrink-0">
            <div class="pms-card p-2">
                @foreach([['hotel','fas fa-hotel','Hotel Info'],['preferences','fas fa-sliders-h','Preferences'],['notifications','fas fa-bell','Notifications'],['invoice','fas fa-file-invoice','Invoice'],['email','fas fa-envelope','Email (SMTP)']] as [$tab,$icon,$label])
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

            @elseif($activeTab === 'email')
            <div class="pms-card p-6 space-y-5">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="font-medium text-gray-800">Enable SMTP Email</p>
                        <p class="text-sm text-gray-500">Use these settings to send emails instead of the system default</p>
                    </div>
                    <button wire:click="$toggle('smtp_enabled')"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $smtp_enabled ? 'bg-indigo-600' : 'bg-gray-300' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $smtp_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>

                <h2 class="text-base font-semibold text-gray-900 mb-1">SMTP Configuration</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="pms-label">SMTP Host <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="smtp_host" class="pms-input" placeholder="smtp.gmail.com">
                        @error('smtp_host') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label">Port <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="smtp_port" class="pms-input" placeholder="587">
                        @error('smtp_port') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label">Encryption</label>
                        <select wire:model="smtp_encryption" class="pms-select">
                            <option value="tls">TLS (STARTTLS)</option>
                            <option value="ssl">SSL (Implicit TLS)</option>
                            <option value="none">None</option>
                        </select>
                    </div>
                    <div>
                        <label class="pms-label">Username</label>
                        <input type="text" wire:model="smtp_username" class="pms-input" placeholder="you@example.com" autocomplete="off">
                    </div>
                    <div>
                        <label class="pms-label">Password</label>
                        <input type="password" wire:model="smtp_password" class="pms-input" placeholder="••••••••" autocomplete="new-password">
                    </div>
                    <div>
                        <label class="pms-label">From Address</label>
                        <input type="email" wire:model="smtp_from_address" class="pms-input" placeholder="noreply@hotel.com">
                        @error('smtp_from_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label">From Name</label>
                        <input type="text" wire:model="smtp_from_name" class="pms-input" placeholder="Merahkie Hotel">
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button wire:click="saveEmail" class="btn-primary"><i class="fas fa-save"></i> Save Email Settings</button>
                </div>

                <div class="border-t border-gray-100 pt-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Send a Test Email</h3>
                    <p class="text-xs text-gray-500 mb-3">Sends using the settings above (even if not saved yet) so you can verify they work.</p>
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <input type="email" wire:model="test_email" class="pms-input" placeholder="test@example.com">
                            @error('test_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button wire:click="sendTestEmail" wire:loading.attr="disabled" class="btn-secondary shrink-0">
                            <span wire:loading wire:target="sendTestEmail"><i class="fas fa-spinner fa-spin"></i></span>
                            <i class="fas fa-paper-plane" wire:loading.remove wire:target="sendTestEmail"></i> Send Test
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>