<?php

use Livewire\Component;
use App\Models\Setting;

new class extends Component
{
    public string $activeTab = 'hotel';

    // Hotel Info
    public string $hotel_name     = '';
    public string $hotel_address  = '';
    public string $hotel_phone    = '';
    public string $hotel_email    = '';
    public string $hotel_website  = '';
    public string $hotel_timezone = 'UTC';

    // Preferences
    public string $currency       = 'USD';
    public string $date_format    = 'd M Y';
    public string $checkin_time   = '14:00';
    public string $checkout_time  = '12:00';

    // Notifications
    public bool $email_notifications = true;
    public bool $sms_notifications   = false;

    // Invoice
    public string $invoice_prefix = 'INV-';
    public string $invoice_footer = '';

    public function mount(): void
    {
        $s = Setting::all_map();

        $this->hotel_name           = $s['hotel_name']           ?? 'Merahkie PMS Lite';
        $this->hotel_address        = $s['hotel_address']         ?? '';
        $this->hotel_phone          = $s['hotel_phone']           ?? '';
        $this->hotel_email          = $s['hotel_email']           ?? '';
        $this->hotel_website        = $s['hotel_website']         ?? '';
        $this->hotel_timezone       = $s['hotel_timezone']        ?? 'UTC';
        $this->currency             = $s['currency']              ?? 'USD';
        $this->date_format          = $s['date_format']           ?? 'd M Y';
        $this->checkin_time         = $s['checkin_time']          ?? '14:00';
        $this->checkout_time        = $s['checkout_time']         ?? '12:00';
        $this->email_notifications  = ($s['email_notifications']  ?? '1') === '1';
        $this->sms_notifications    = ($s['sms_notifications']    ?? '0') === '1';
        $this->invoice_prefix       = $s['invoice_prefix']        ?? 'INV-';
        $this->invoice_footer       = $s['invoice_footer']        ?? '';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function saveHotel(): void
    {
        $this->validate([
            'hotel_name'  => 'required|string|max:255',
            'hotel_email' => 'nullable|email',
        ]);

        Setting::set('hotel_name',     $this->hotel_name);
        Setting::set('hotel_address',  $this->hotel_address);
        Setting::set('hotel_phone',    $this->hotel_phone);
        Setting::set('hotel_email',    $this->hotel_email);
        Setting::set('hotel_website',  $this->hotel_website);
        Setting::set('hotel_timezone', $this->hotel_timezone);

        $this->dispatch('toast', message: 'Hotel settings saved successfully.', type: 'success');
    }

    public function savePreferences(): void
    {
        Setting::set('currency',      $this->currency);
        Setting::set('date_format',   $this->date_format);
        Setting::set('checkin_time',  $this->checkin_time);
        Setting::set('checkout_time', $this->checkout_time);
        Setting::set('hotel_timezone',$this->hotel_timezone);

        $this->dispatch('toast', message: 'Preferences saved successfully.', type: 'success');
    }

    public function saveNotifications(): void
    {
        Setting::set('email_notifications', $this->email_notifications ? '1' : '0');
        Setting::set('sms_notifications',   $this->sms_notifications   ? '1' : '0');

        $this->dispatch('toast', message: 'Notification settings saved.', type: 'success');
    }

    public function saveInvoice(): void
    {
        Setting::set('invoice_prefix', $this->invoice_prefix);
        Setting::set('invoice_footer', $this->invoice_footer);

        $this->dispatch('toast', message: 'Invoice settings saved.', type: 'success');
    }

    public function render(): mixed
    {
        return $this->view([]);
    }
};
